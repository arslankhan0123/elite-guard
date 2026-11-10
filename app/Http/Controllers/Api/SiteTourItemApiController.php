<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Repositories\SiteTourItemRepository;
use App\Repositories\ShiftRepository;
use Illuminate\Http\Request;
use App\Traits\ApiResponser;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class SiteTourItemApiController extends Controller
{
    use ApiResponser;
    protected $siteTourItemRepo;
    protected $shiftRepo;

    // Inject the repository via constructor
    public function __construct(SiteTourItemRepository $siteTourItemRepo, ShiftRepository $shiftRepo)
    {
        $this->siteTourItemRepo = $siteTourItemRepo;
        $this->shiftRepo = $shiftRepo;
    }

    /**
     * @OA\Get(
     *     path="/api/site-tour-items/user",
     *     summary="Get site tour items assigned to the authenticated user",
     *     tags={"Site Tour Items"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="start_date",
     *         in="query",
     *         description="Optional start date to filter items (YYYY-MM-DD)",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="end_date",
     *         in="query",
     *         description="Optional end date to filter items (YYYY-MM-DD)",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Assigned site tour items fetched successfully.",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="Success"),
     *             @OA\Property(property="message", type="string", example="Assigned site tour items fetched."),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="status", type="boolean", example=true),
     *                 @OA\Property(property="message", type="string", example="Assigned site tour items retrieved successfully"),
     *                 @OA\Property(property="site_tour_items", type="array", @OA\Items(type="object"))
     *             )
     *         )
     *     )
     * )
     */
    public function userSiteTourItems(Request $request)
    {
        Log::info('userSiteTourItems Request:', $request->all());

        $user = Auth::user();

        // Automatically resolve the active (or next upcoming) shift.
        // This way mobile does not need to send shift_id manually.
        $activeShift = $this->shiftRepo->getActiveShift();

        if (!$activeShift || Carbon::now(config('app.timezone', 'UTC'))->gt($activeShift->end_datetime)) {
            return $this->successResponse([
                'status'          => true,
                'message'         => 'No active or upcoming shift found.',
                'shift'           => null,
                'site_tour_items' => [],
            ], 'No active or upcoming shift found.');
        }

        $shift_id   = $activeShift->id;
        $start_date = $activeShift->start_datetime ? $activeShift->start_datetime->toDateString() : $activeShift->date;
        $end_date   = $activeShift->end_datetime ? $activeShift->end_datetime->toDateString() : $activeShift->date;

        $data = $this->siteTourItemRepo->getUserSiteTourItems($user, $start_date, $end_date, $shift_id);
        $data['shift'] = $activeShift;

        return $this->successResponse($data, 'Assigned site tour items fetched.');
    }

    /**
     * @OA\Post(
     *     path="/api/site-tour-items/scan",
     *     summary="Scan an NFC tag for a site tour item",
     *     tags={"Site Tour Items"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"site_tour_item_id","nfc_tag_id","site_id","date","time"},
     *                 @OA\Property(property="site_tour_item_id", type="integer", example=1),
     *                 @OA\Property(property="nfc_tag_id", type="integer", example=2),
     *                 @OA\Property(property="site_id", type="integer", example=3),
     *                 @OA\Property(property="date", type="string", format="date", example="2026-07-14"),
     *                 @OA\Property(property="time", type="string", example="14:30:00"),
     *                 @OA\Property(property="reason", type="string", nullable=true, example="NFC tag was inaccessible"),
     *                 @OA\Property(property="image", type="string", format="binary", description="Optional image/photo taken during scanning")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="NFC tag scanned successfully.",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="Success"),
     *             @OA\Property(property="message", type="string", example="NFC tag scanned and saved successfully."),
     *             @OA\Property(property="data", type="object")
     *         )
     *     )
     * )
     */
    public function storeScan(Request $request)
    {
        Log::info('Store Scan Request:', $request->all());
        $request->validate([
            'site_tour_item_id' => 'required|integer',
            'nfc_tag_id' => 'required|integer',
            'site_id' => 'required|integer',
            'date' => 'required|date',
            'time' => 'required',
            'reason' => 'nullable|string',
            'image' => 'nullable|image',
            'images' => 'nullable|array',
            'images.*' => 'nullable|image'
        ]);

        
        $user = Auth::user();

        // ── Validate the scan falls within the tour point's date + time window ──
        $siteTourItem = \App\Models\SiteTourItem::with('site')->find($request->input('site_tour_item_id'));
        if (! $siteTourItem) {
            return $this->notFoundResponse('Site tour item not found.');
        }

        $windowError = $this->validateScanWindow($request, $siteTourItem, $user);
        if ($windowError !== null) {
            return $this->wrongDataResponse($windowError);
        }

        $response = $this->siteTourItemRepo->storeScan($request, $user);
        return $this->successResponse($response, $response['message']);
    }

    /**
     * Ensure the scan datetime (sent by the app) falls within the tour point's own
     * date + start_time..end_time window. Handles overnight slots that cross midnight
     * (e.g. 23:46 -> 00:30, or an 8pm -> 6am shift). Returns an error message string
     * when invalid (and alerts the SuperAdmins), or null when the scan is valid.
     */
    private function validateScanWindow(Request $request, \App\Models\SiteTourItem $item, $user): ?string
    {
        // Need valid date + start/end on the item to validate against
        if (empty($item->date) || empty($item->start_time) || empty($item->end_time)) {
            return null; // nothing to validate against -> let it through
        }

        try {
            $itemDate = Carbon::parse($item->date)->format('Y-m-d');
            $startT   = Carbon::parse($item->start_time)->format('H:i:s');
            $endT     = Carbon::parse($item->end_time)->format('H:i:s');
        } catch (\Throwable $e) {
            return null;
        }

        $winStart = Carbon::parse("{$itemDate} {$startT}");
        $winEnd   = Carbon::parse("{$itemDate} {$endT}");
        if ($winEnd->lessThanOrEqualTo($winStart)) {
            $winEnd->addDay(); // overnight slot -> end falls on the next calendar day
        }

        // Allow scanning to open early: subtract the site's open_time (in minutes)
        // from the window start. e.g. start 03:16 with open_time 10 -> window opens 03:06.
        $openTime = (int) ($item->site->open_time ?? 0);
        if ($openTime > 0) {
            $winStart->subMinutes($openTime);
        }

        // Build the scan datetime from what the API actually sent (date + time)
        try {
            $reqDate = Carbon::parse($request->input('date'))->format('Y-m-d');
            $reqTime = Carbon::parse($request->input('time'))->format('H:i:s');
            $reqDt   = Carbon::parse("{$reqDate} {$reqTime}");
        } catch (\Throwable $e) {
            return 'Invalid scan date/time supplied.';
        }

        // Inclusive check: covers both the date and the time together
        if ($reqDt->between($winStart, $winEnd)) {
            return null;
        }

        $siteName = $item->site?->name ?? 'N/A';
        $this->alertSuperAdminsInvalidScan($user, $siteName, $reqDt, $winStart, $winEnd);

        return 'Scan rejected: the scan time (' . $reqDt->format('d M Y g:i A') .
               ') is outside the allowed window for this tour point (' .
               $winStart->format('d M Y g:i A') . ' - ' . $winEnd->format('d M Y g:i A') . ').';
    }

    /** Email every SuperAdmin (except the excluded one) that an out-of-window scan was blocked. */
    private function alertSuperAdminsInvalidScan($guard, string $siteName, Carbon $reqDt, Carbon $winStart, Carbon $winEnd): void
    {
        try {
            $dashboardUrl = url('/dashboard');
            $guardName    = $guard->name ?? 'Guard';
            $subject      = "\u{26A0} Invalid Site Tour Scan: {$guardName} at {$siteName}";

            $html = view('emails.invalid-site-tour-scan', [
                'guardName'    => $guardName,
                'siteName'     => $siteName,
                'scanTime'     => $reqDt->format('d M Y g:i A'),
                'windowStart'  => $winStart->format('d M Y g:i A'),
                'windowEnd'    => $winEnd->format('d M Y g:i A'),
                'dashboardUrl' => $dashboardUrl,
            ])->render();

            // Track who we've emailed so nobody gets it twice (e.g. a guard who is also a SuperAdmin)
            $sentTo = [];

            $send = function ($email, $name) use (&$sentTo, $subject, $html) {
                $email = trim((string) $email);
                if ($email === '') {
                    return;
                }
                // Do not email this specific super-admin
                // if (strcasecmp($email, 'arslan.devsspace@gmail.com') === 0) {
                //     return;
                // }
                if (in_array(strtolower($email), $sentTo, true)) {
                    return;
                }
                try {
                    \App\Services\TenantMailSender::sendHtml($email, $name, $subject, $html);
                    $sentTo[] = strtolower($email);
                    Log::info('[InvalidScan] alert email sent to: ' . $email);
                } catch (\Throwable $e) {
                    Log::warning('[InvalidScan] email FAILED to ' . $email . ': ' . $e->getMessage());
                }
            };

            // 1) The guard (user) who made the scan
            $send($guard->email ?? null, $guardName);

            // 2) All SuperAdmins
            $superAdmins = \App\Models\User::where('role', 'SuperAdmin')->get();
            foreach ($superAdmins as $admin) {
                $send($admin->email, $admin->name);
            }
        } catch (\Throwable $e) {
            Log::warning('[InvalidScan] alert routine failed: ' . $e->getMessage());
        }
    }
}

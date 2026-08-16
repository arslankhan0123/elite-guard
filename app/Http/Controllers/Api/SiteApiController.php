<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Schedule;
use App\Models\Site;
use App\Models\NfcTag;
use App\Models\SiteItem;
use App\Models\SiteItemScan;
use App\Repositories\ScheduleRepository;
use Illuminate\Http\Request;
use App\Repositories\SiteRepository;
use App\Traits\ApiResponser;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class SiteApiController extends Controller
{
    use ApiResponser;
    protected $siteRepo;
    protected $scheduleRepo;

    // Inject the repository via constructor
    public function __construct(SiteRepository $siteRepo, ScheduleRepository $scheduleRepo)
    {
        $this->siteRepo = $siteRepo;
        $this->scheduleRepo = $scheduleRepo;
    }

    /**
     * @OA\Get(
     *     path="/api/sites",
     *     summary="Get sites assigned to the authenticated user",
     *     tags={"Sites"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Assigned sites fetched.",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="Success"),
     *             @OA\Property(property="message", type="string", example="Assigned sites fetched."),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="status", type="boolean", example=true),
     *                 @OA\Property(property="message", type="string", example="Assigned sites retrieved successfully"),
     *                 @OA\Property(property="sites", type="array", @OA\Items(type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="Elite Plaza"),
     *                     @OA\Property(property="company", type="object"),
     *                     @OA\Property(property="nfc_tags", type="array", @OA\Items(type="object"))
     *                 ))
     *             )
     *         )
     *     )
     * )
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $data = $this->siteRepo->getAllSites();

        return $this->successResponse($data, 'Assigned Sites with Company and NFC Tags fetched.');
    }

    /**
     * @OA\Get(
     *     path="/api/sites/user",
     *     summary="Get sites assigned to the authenticated user",
     *     tags={"Sites"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Assigned sites fetched successfully.",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="Success"),
     *             @OA\Property(property="message", type="string", example="Assigned Sites with Company and NFC Tags fetched."),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="status", type="boolean", example=true),
     *                 @OA\Property(property="message", type="string", example="Assigned sites retrieved successfully"),
     *                 @OA\Property(property="sites", type="array", @OA\Items(type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="Elite Plaza"),
     *                     @OA\Property(property="company", type="object"),
     *                     @OA\Property(property="nfc_tags", type="array", @OA\Items(type="object"))
     *                 ))
     *             )
     *         )
     *     )
     * )
     */
    public function userSites(Request $request)
    {
        $user = Auth::user();
        $data = $this->siteRepo->getUserAssignedSites($user);

        return $this->successResponse($data, 'Assigned Sites with Company and NFC Tags fetched.');
    }

    /**
     * @OA\Post(
     *     path="/api/sites/scan",
     *     summary="Record an NFC tag scan against a site",
     *     tags={"Sites"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Scans with the same site_id, authenticated user, app_date and app_time are grouped under the same SiteItem. A different app_date or app_time creates a new SiteItem.",
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"site_id", "nfc_tag_id", "app_date", "app_time"},
     *                 @OA\Property(property="site_id", type="integer", example=1),
     *                 @OA\Property(property="nfc_tag_id", type="integer", example=2),
     *                 @OA\Property(property="app_date", type="string", format="date", example="2026-08-16", description="App date in YYYY-MM-DD format"),
     *                 @OA\Property(property="app_time", type="string", format="time", example="09:30:00", description="App time in HH:mm:ss 24-hour format"),
     *                 @OA\Property(property="type", type="string", example="scan"),
     *                 @OA\Property(property="reason", type="string", nullable=true, example="Routine checkpoint scan"),
     *                 @OA\Property(property="image", type="string", format="binary")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=201, description="Scan recorded successfully"),
     *     @OA\Response(response=403, description="Site is not assigned to the user"),
     *     @OA\Response(response=422, description="Invalid scan data")
     * )
     */
    public function storeScan(Request $request)
    {
        Log::info('Store Scan Request Data: ', $request->all());
        $validator = Validator::make($request->all(), [
            'site_id' => ['required', 'integer', 'exists:sites,id'],
            'nfc_tag_id' => ['required', 'integer', 'exists:nfc_tags,id'],
            'app_date' => ['required', 'date_format:Y-m-d'],
            'app_time' => ['required', 'date_format:H:i:s'],
            'image' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:10240'],
            'type' => ['nullable', 'string', 'max:100'],
            'reason' => ['nullable', 'string'],
        ]);

        if ($validator->fails()) {
            return $this->errorResponse($validator->errors()->first(), $validator->errors(), 422);
        }

        $user = Auth::user();

        if (!$user->sites()->whereKey($request->integer('site_id'))->exists()) {
            return $this->errorResponse('This site is not assigned to you.', null, 403);
        }

        $tagBelongsToSite = NfcTag::whereKey($request->integer('nfc_tag_id'))
            ->where('site_id', $request->integer('site_id'))
            ->exists();

        if (!$tagBelongsToSite) {
            return $this->errorResponse('The selected NFC tag does not belong to this site.', null, 422);
        }

        $imagePath = null;

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('documents/SiteScans', 'public');
        }

        try {
            $scannedAt = Carbon::now();

            $scan = DB::transaction(function () use ($request, $user, $scannedAt, $imagePath) {
                $date = $scannedAt->toDateString();
                $time = $scannedAt->format('H:i:s');
                $type = $request->filled('type') ? $request->input('type') : 'scan';

                $siteItem = SiteItem::firstOrCreate(
                    [
                        'site_id' => $request->integer('site_id'),
                        'user_id' => $user->id,
                        'app_date' => $request->input('app_date'),
                        'app_time' => $request->input('app_time'),
                    ],
                    [
                        'date' => $date,
                        'type' => $type,
                        'start_time' => $time,
                        'end_time' => $time,
                        'status' => false,
                        'reason' => $request->input('reason'),
                    ]
                );

                $scan = SiteItemScan::create([
                    'site_item_id' => $siteItem->id,
                    'site_id' => $request->integer('site_id'),
                    'nfc_tag_id' => $request->integer('nfc_tag_id'),
                    'user_id' => $user->id,
                    'date' => $date,
                    'time' => $time,
                    'image' => $imagePath ? Storage::disk('public')->url($imagePath) : null,
                ]);

                $totalTags = NfcTag::where('site_id', $siteItem->site_id)->count();
                $scannedTags = $siteItem->scans()->distinct()->count('nfc_tag_id');

                $siteItem->update([
                    'end_time' => $time,
                    'status' => $totalTags > 0 && $scannedTags >= $totalTags,
                    'type' => $type,
                    'reason' => $request->exists('reason')
                        ? $request->input('reason')
                        : $siteItem->reason,
                ]);

                return $scan;
            });
        } catch (\Throwable $exception) {
            if ($imagePath) {
                Storage::disk('public')->delete($imagePath);
            }

            throw $exception;
        }

        $scan->load([
            'siteItem.site:id,name',
            'siteItem.user:id,name',
            'nfcTag:id,site_id,uid,name',
            'user:id,name',
        ]);

        return $this->successResponse($scan, 'NFC tag scanned and saved successfully.', 201);
    }
    
}

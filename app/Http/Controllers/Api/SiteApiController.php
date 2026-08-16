<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Schedule;
use App\Models\Site;
use App\Models\NfcTag;
use App\Models\SiteScan;
use App\Repositories\ScheduleRepository;
use Illuminate\Http\Request;
use App\Repositories\SiteRepository;
use App\Traits\ApiResponser;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

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
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"site_id", "nfc_tag_id"},
     *                 @OA\Property(property="site_id", type="integer", example=1),
     *                 @OA\Property(property="nfc_tag_id", type="integer", example=2),
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
            'image' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:10240'],
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

            $scan = SiteScan::create([
                'site_id' => $request->integer('site_id'),
                'nfc_tag_id' => $request->integer('nfc_tag_id'),
                'user_id' => $user->id,
                'date' => $scannedAt->toDateString(),
                'time' => $scannedAt->format('H:i:s'),
                'image' => $imagePath ? Storage::disk('public')->url($imagePath) : null,
            ]);
        } catch (\Throwable $exception) {
            if ($imagePath) {
                Storage::disk('public')->delete($imagePath);
            }

            throw $exception;
        }

        $scan->load(['site:id,name', 'nfcTag:id,site_id,uid,name', 'user:id,name']);

        return $this->successResponse($scan, 'NFC tag scanned and saved successfully.', 201);
    }
    
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class DocumentsApiController extends Controller
{
    use ApiResponser;

    /**
     * @OA\Post(
     *     path="/api/documents/upload",
     *     summary="Upload user documents (Bank and License details)",
     *     tags={"Documents"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(property="bank_detail[void_cheque_file]", type="string", format="binary", description="Void Cheque File (PDF/Image)"),
     *                 @OA\Property(property="license_detail[security_license_file]", type="string", format="binary", description="Security License File"),
     *                 @OA\Property(property="license_detail[drivers_license_file]", type="string", format="binary", description="Drivers License File"),
     *                 @OA\Property(property="license_detail[work_eligibility_file]", type="string", format="binary", description="Work Eligibility File"),
     *                 @OA\Property(property="license_detail[other_documents_file]", type="string", format="binary", description="Other Documents File")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Documents uploaded successfully.",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="Success"),
     *             @OA\Property(property="message", type="string", example="User documents updated successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="user", type="object")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     )
     * )
     */
    public function documentsUpload(Request $request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // 1. Handle Bank Details
        if ($request->has('bank_detail')) {
            $updateBank = [];
            if ($request->hasFile('bank_detail.void_cheque_file')) {
                $updateBank['void_cheque_file'] = $this->uploadDocument(
                    $request->file('bank_detail.void_cheque_file'),
                    $user->id,
                    'bank_details_documents',
                    $user->bankDetail->void_cheque_file ?? []
                );
            }

            if (!empty($updateBank)) {
                $user->bankDetail()->updateOrCreate(['user_id' => $user->id], $updateBank);
            }
        }

        // 2. Handle License Details
        if ($request->has('license_detail')) {
            $updateLicense = [];

            // Map request keys to database columns
            $fileFields = [
                'security_license_file' => 'license_detail_documents',
                'drivers_license_file' => 'license_detail_documents',
                'work_eligibility_file' => 'license_detail_documents',
                'other_documents_file' => 'license_detail_documents',
            ];

            foreach ($fileFields as $field => $directory) {
                if ($request->hasFile("license_detail.{$field}")) {
                    $updateLicense[$field] = $this->uploadDocument(
                        $request->file("license_detail.{$field}"),
                        $user->id,
                        $directory,
                        $user->licenseDetail->{$field} ?? []
                    );
                }
            }

            if (!empty($updateLicense)) {
                $user->licenseDetail()->updateOrCreate(['user_id' => $user->id], $updateLicense);
            }
        }

        // Reload user with relations to return fresh data
        $user->load(['candidate', 'bankDetail', 'licenseDetail', 'availability', 'officeDetail', 'sites']);

        return $this->successResponse(
            ['user' => $user],
            'User documents updated successfully'
        );
    }

    /**
     * Helper to handle file movement and path generation.
     */
    private function uploadDocument($file, $userId, $subDir, $existingFiles)
    {
        $filename = $userId . '_' . time() . '_' . substr(uniqid(), -10) . '.' . $file->getClientOriginalExtension();
        $relativeDir = "documents/{$subDir}";
        $destinationPath = public_path($relativeDir);

        if (!File::exists($destinationPath)) {
            File::makeDirectory($destinationPath, 0777, true);
        }

        $file->move($destinationPath, $filename);

        $baseUrl = rtrim(config('app.url'), '/');
        return array_merge($existingFiles, ["{$baseUrl}/{$relativeDir}/{$filename}"]);
    }
}

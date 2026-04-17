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
     * Handle document uploads for bank and license details.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
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

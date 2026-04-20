<?php

namespace App\Traits;

use Illuminate\Support\Facades\File;

trait CommonTrait
{
    /**
     * Delete all physically stored documents associated with a user (Employee).
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function DeleteEmployeeDocuments($user)
    {
        if (!$user) {
            return;
        }

        // 1. Collect all document URLs to delete physical files
        $allFiles = [];
        
        // Load relations if not loaded
        if (!$user->relationLoaded('bankDetail')) {
            $user->load('bankDetail');
        }
        if (!$user->relationLoaded('licenseDetail')) {
            $user->load('licenseDetail');
        }

        if ($user->bankDetail) {
            $files = $user->bankDetail->void_cheque_file;
            if ($files) {
                $allFiles = array_merge($allFiles, is_array($files) ? $files : [$files]);
            }
        }
        
        if ($user->licenseDetail) {
            $fields = ['security_license_file', 'drivers_license_file', 'work_eligibility_file', 'other_documents_file'];
            foreach ($fields as $field) {
                $files = $user->licenseDetail->$field;
                if ($files) {
                    $allFiles = array_merge($allFiles, is_array($files) ? $files : [$files]);
                }
            }
        }
        
        // 2. Delete physical files from public/documents/
        $baseUrl = rtrim(config('app.url'), '/');
        foreach ($allFiles as $fileUrl) {
            // Strip the base URL to get the relative path
            $relativePath = str_replace($baseUrl . '/', '', $fileUrl);
            // Ensure no leading slash remains after replacement
            $relativePath = ltrim($relativePath, '/');
            $fullPath = public_path($relativePath);
            
            if (File::exists($fullPath)) {
                File::delete($fullPath);
            }
        }
    }
}

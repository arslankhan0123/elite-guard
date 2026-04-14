<?php

namespace App\Repositories;

use App\Models\Site;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class UserRepository
{
    // Get all sites
    public function getUser()
    {
        $user = User::with(['candidate', 'bankDetail', 'licenseDetail', 'availability', 'officeDetail', 'sites'])->find(Auth::id());
        $data = [
            'status' => true,
            'message' => 'User retrieved successfully',
            'user' => $user
        ];
        return $data;
    }

    public function userUpdate($request)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        DB::transaction(function () use ($request, $user) {
            // 1. Core User Data
            if ($request->has('first_name')) {
                $user->update(['name' => $request->first_name . ' ' . $request->last_name]);
            }
            if ($request->has('email')) {
                $user->update(['email' => $request->email]);
            }
            if ($request->has('password')) {
                $user->update([
                    'real_password' => $request->password,
                    'password' => Hash::make($request->password)
                ]);
            }

            // 2. Candidate Information (nested as 'candidate')
            if ($request->has('candidate')) {
                $candidateData = $request->input('candidate');
                $user->candidate()->updateOrCreate(['user_id' => $user->id], [
                    'first_name' => $candidateData['first_name'] ?? ($user->candidate->first_name ?? null),
                    'last_name' => $candidateData['last_name'] ?? ($user->candidate->last_name ?? null),
                    'dob' => $candidateData['dob'] ?? ($user->candidate->dob ?? null),
                    'sin' => $candidateData['sin'] ?? ($user->candidate->sin ?? null),
                    'phone' => $candidateData['phone'] ?? ($user->candidate->phone ?? null),
                    'email' => $candidateData['email'] ?? ($user->candidate->email ?? null),
                    'address' => $candidateData['address'] ?? ($user->candidate->address ?? null),
                    'city' => $candidateData['city'] ?? ($user->candidate->city ?? null),
                    'province' => $candidateData['province'] ?? ($user->candidate->province ?? null),
                    'postal_code' => $candidateData['postal_code'] ?? ($user->candidate->postal_code ?? null),
                    'emergency_contact_name' => $candidateData['emergency_contact_name'] ?? ($user->candidate->emergency_contact_name ?? null),
                    'emergency_contact_phone' => $candidateData['emergency_contact_phone'] ?? ($user->candidate->emergency_contact_phone ?? null),
                ]);

                // Sync User Name if first/last name changed
                if (isset($candidateData['first_name']) || isset($candidateData['last_name'])) {
                    $fn = $candidateData['first_name'] ?? ($user->candidate->first_name ?? '');
                    $ln = $candidateData['last_name'] ?? ($user->candidate->last_name ?? '');
                    $user->update(['name' => trim($fn . ' ' . $ln)]);
                }
            }

            // 3. Bank Details (nested as 'bank_detail')
            if ($request->has('bank_detail')) {
                $bankData = $request->input('bank_detail');
                $updateData = [
                    'bank_name' => $bankData['bank_name'] ?? ($user->bankDetail->bank_name ?? null),
                    'institution_number' => $bankData['institution_number'] ?? ($user->bankDetail->institution_number ?? null),
                    'transit_number' => $bankData['transit_number'] ?? ($user->bankDetail->transit_number ?? null),
                    'account_number' => $bankData['account_number'] ?? ($user->bankDetail->account_number ?? null),
                    'bank_address' => $bankData['bank_address'] ?? ($user->bankDetail->bank_address ?? null),
                    'interac_email' => $bankData['interac_email'] ?? ($user->bankDetail->interac_email ?? null),
                ];

                if ($request->hasFile('bank_detail.void_cheque_file')) {
                    if ($user->bankDetail && $user->bankDetail->void_cheque_file) {
                        Storage::disk('public')->delete($user->bankDetail->void_cheque_file);
                    }
                    $updateData['void_cheque_file'] = $request->file('bank_detail.void_cheque_file')->store('employee_documents/cheques', 'public');
                }
                $user->bankDetail()->updateOrCreate(['user_id' => $user->id], $updateData);
            }

            // 4. License Information (nested as 'license_detail')
            if ($request->has('license_detail')) {
                $licData = $request->input('license_detail');
                $updateLic = [
                    'security_license_number' => $licData['security_license_number'] ?? ($user->licenseDetail->security_license_number ?? null),
                    'security_license_expiry' => $licData['security_license_expiry'] ?? ($user->licenseDetail->security_license_expiry ?? null),
                    'drivers_license_number' => $licData['drivers_license_number'] ?? ($user->licenseDetail->drivers_license_number ?? null),
                    'drivers_license_expiry' => $licData['drivers_license_expiry'] ?? ($user->licenseDetail->drivers_license_expiry ?? null),
                    'work_eligibility_type_number' => $licData['work_eligibility_type_number'] ?? ($user->licenseDetail->work_eligibility_type_number ?? null),
                    'work_eligibility_expiry' => $licData['work_eligibility_expiry'] ?? ($user->licenseDetail->work_eligibility_expiry ?? null),
                    'criminal_record_check' => $licData['criminal_record_check'] ?? ($user->licenseDetail->criminal_record_check ?? null),
                    'first_aid_training' => $licData['first_aid_training'] ?? ($user->licenseDetail->first_aid_training ?? null),
                    'other_certificates' => $licData['other_certificates'] ?? ($user->licenseDetail->other_certificates ?? null),
                ];

                $fileFields = ['security_license_file', 'drivers_license_file', 'work_eligibility_file', 'other_documents_file'];
                foreach ($fileFields as $field) {
                    if ($request->hasFile("license_detail.$field")) {
                        if ($user->licenseDetail && $user->licenseDetail->$field) {
                            Storage::disk('public')->delete($user->licenseDetail->$field);
                        }
                        $folder = $field == 'other_documents_file' ? 'others' : 'licenses';
                        $updateLic[$field] = $request->file("license_detail.$field")->store('employee_documents/' . $folder, 'public');
                    }
                }
                $user->licenseDetail()->updateOrCreate(['user_id' => $user->id], $updateLic);
            }

            // 5. Availability (nested as 'availability')
            if ($request->has('availability')) {
                $availData = $request->input('availability');
                $user->availability()->updateOrCreate(['user_id' => $user->id], [
                    'availability_date' => $availData['availability_date'] ?? ($user->availability->availability_date ?? null),
                    'willing_hours' => $availData['willing_hours'] ?? ($user->availability->willing_hours ?? null),
                    'unable_hours' => $availData['unable_hours'] ?? ($user->availability->unable_hours ?? null),
                    'unable_days' => $availData['unable_days'] ?? ($user->availability->unable_days ?? null),
                ]);
            }

            // 6. Office Use Only (nested as 'office_detail')
            if ($request->has('office_detail')) {
                $offData = $request->input('office_detail');
                $user->officeDetail()->updateOrCreate(['user_id' => $user->id], [
                    'employment_type' => $offData['employment_type'] ?? ($user->officeDetail->employment_type ?? null),
                    'start_date' => $offData['start_date'] ?? ($user->officeDetail->start_date ?? null),
                    'job_position' => $offData['job_position'] ?? ($user->officeDetail->job_position ?? null),
                    'wage' => $offData['wage'] ?? ($user->officeDetail->wage ?? null),
                    'other_notes' => $offData['other_notes'] ?? ($user->officeDetail->other_notes ?? null),
                    'hiring_manager_name' => $offData['hiring_manager_name'] ?? ($user->officeDetail->hiring_manager_name ?? null),
                    'hiring_manager_signature' => $offData['hiring_manager_signature'] ?? ($user->officeDetail->hiring_manager_signature ?? null),
                ]);
            }
        });

        $user = User::with(['candidate', 'bankDetail', 'licenseDetail', 'availability', 'officeDetail', 'sites'])->find($user->id);
        $data = [
            'status' => true,
            'message' => 'User updated successfully',
            'user' => $user
        ];
        return $data;
    }
}

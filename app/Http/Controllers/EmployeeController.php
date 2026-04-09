<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\User;
use App\Models\EmployeeCandidate;
use App\Models\EmployeeBankDetail;
use App\Models\EmployeeLicenseDetail;
use App\Models\EmployeeAvailability;
use App\Models\EmployeeOfficeDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class EmployeeController extends Controller
{
    public function index()
    {
        $employees = Employee::with('user')->get();
        return view('admin.employees.index', compact('employees'));
    }

    public function create()
    {
        return view('admin.employees.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|string',
            
            // Files
            'void_cheque_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'security_license_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'drivers_license_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'work_eligibility_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'other_documents_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        DB::transaction(function () use ($request) {
            $user = User::create([
                'name' => $request->first_name . ' ' . $request->last_name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'real_password' => $request->password,
                'role' => $request->role,
            ]);

            // Part 1: Candidate Information
            EmployeeCandidate::create([
                'user_id' => $user->id,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'dob' => $request->dob,
                'sin' => $request->sin,
                'phone' => $request->phone,
                'email' => $request->email_personal, // personal email from form if different
                'address' => $request->address,
                'city' => $request->city,
                'province' => $request->province,
                'postal_code' => $request->postal_code,
                'emergency_contact_name' => $request->emergency_contact_name,
                'emergency_contact_phone' => $request->emergency_contact_phone,
            ]);

            // Part 2: Bank Details
            $voidChequePath = $request->hasFile('void_cheque_file') ? $request->file('void_cheque_file')->store('employee_documents/cheques', 'public') : null;
            EmployeeBankDetail::create([
                'user_id' => $user->id,
                'bank_name' => $request->bank_name,
                'institution_number' => $request->institution_number,
                'transit_number' => $request->transit_number,
                'account_number' => $request->account_number,
                'bank_address' => $request->bank_address,
                'interac_email' => $request->interac_email,
                'void_cheque_file' => $voidChequePath,
            ]);

            // Part 3: License Information
            $securityFile = $request->hasFile('security_license_file') ? $request->file('security_license_file')->store('employee_documents/licenses', 'public') : null;
            $driversFile = $request->hasFile('drivers_license_file') ? $request->file('drivers_license_file')->store('employee_documents/licenses', 'public') : null;
            $workFile = $request->hasFile('work_eligibility_file') ? $request->file('work_eligibility_file')->store('employee_documents/licenses', 'public') : null;
            $otherFile = $request->hasFile('other_documents_file') ? $request->file('other_documents_file')->store('employee_documents/others', 'public') : null;

            EmployeeLicenseDetail::create([
                'user_id' => $user->id,
                'security_license_number' => $request->security_license_number,
                'security_license_expiry' => $request->security_license_expiry,
                'security_license_file' => $securityFile,
                'drivers_license_number' => $request->drivers_license_number,
                'drivers_license_expiry' => $request->drivers_license_expiry,
                'drivers_license_file' => $driversFile,
                'work_eligibility_type_number' => $request->work_eligibility_type_number,
                'work_eligibility_expiry' => $request->work_eligibility_expiry,
                'work_eligibility_file' => $workFile,
                'criminal_record_check' => $request->criminal_record_check,
                'first_aid_training' => $request->first_aid_training,
                'other_certificates' => $request->other_certificates,
                'other_documents_file' => $otherFile,
            ]);

            // Part 4: Availability
            EmployeeAvailability::create([
                'user_id' => $user->id,
                'availability_date' => $request->availability_date,
                'willing_hours' => $request->willing_hours,
                'unable_hours' => $request->unable_hours,
                'unable_days' => $request->unable_days,
            ]);

            // Part 5: Office Use Only
            EmployeeOfficeDetail::create([
                'user_id' => $user->id,
                'employment_type' => $request->employment_type,
                'start_date' => $request->start_date,
                'job_position' => $request->job_position,
                'wage' => $request->wage,
                'other_notes' => $request->other_notes,
                'hiring_manager_name' => $request->hiring_manager_name,
                'hiring_manager_signature' => $request->hiring_manager_signature,
            ]);

            // Keep the legacy Employee record for compatibility if needed, but the user said "leave it as it is"
            // meaning we don't use it for the new form, but maybe we should create a basic link.
            Employee::create([
                'user_id' => $user->id,
                'phone' => $request->phone,
                'address' => $request->address,
                'status' => true,
            ]);
        });

        return redirect()->route('employees.index')->with('success', 'Employee created successfully!');
    }

    public function edit($id)
    {
        $employee = Employee::with(['user', 'user.candidate', 'user.bankDetail', 'user.licenseDetail', 'user.availability', 'user.officeDetail'])->findOrFail($id);
        return view('admin.employees.edit', compact('employee'));
    }

    public function update(Request $request, $id)
    {
        $employee = Employee::findOrFail($id);
        $user = $employee->user;

        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'role' => 'required|string',
        ]);

        DB::transaction(function () use ($request, $user) {
            $user->update([
                'name' => $request->first_name . ' ' . $request->last_name,
                'email' => $request->email,
                'role' => $request->role,
            ]);

            if ($request->filled('password')) {
                $user->update([
                    'password' => Hash::make($request->password),
                    'real_password' => $request->password,
                ]);
            }

            // Update Sections
            $user->candidate()->updateOrCreate(['user_id' => $user->id], [
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'dob' => $request->dob,
                'sin' => $request->sin,
                'phone' => $request->phone,
                'email' => $request->email_personal,
                'address' => $request->address,
                'city' => $request->city,
                'province' => $request->province,
                'postal_code' => $request->postal_code,
                'emergency_contact_name' => $request->emergency_contact_name,
                'emergency_contact_phone' => $request->emergency_contact_phone,
            ]);

            // Bank Details
            $bankData = [
                'bank_name' => $request->bank_name,
                'institution_number' => $request->institution_number,
                'transit_number' => $request->transit_number,
                'account_number' => $request->account_number,
                'bank_address' => $request->bank_address,
                'interac_email' => $request->interac_email,
            ];
            if ($request->hasFile('void_cheque_file')) {
                if ($user->bankDetail && $user->bankDetail->void_cheque_file) {
                    Storage::disk('public')->delete($user->bankDetail->void_cheque_file);
                }
                $bankData['void_cheque_file'] = $request->file('void_cheque_file')->store('employee_documents/cheques', 'public');
            }
            $user->bankDetail()->updateOrCreate(['user_id' => $user->id], $bankData);

            // Licenses
            $licenseData = [
                'security_license_number' => $request->security_license_number,
                'security_license_expiry' => $request->security_license_expiry,
                'drivers_license_number' => $request->drivers_license_number,
                'drivers_license_expiry' => $request->drivers_license_expiry,
                'work_eligibility_type_number' => $request->work_eligibility_type_number,
                'work_eligibility_expiry' => $request->work_eligibility_expiry,
                'criminal_record_check' => $request->criminal_record_check,
                'first_aid_training' => $request->first_aid_training,
                'other_certificates' => $request->other_certificates,
            ];
            
            $fileFields = ['security_license_file', 'drivers_license_file', 'work_eligibility_file', 'other_documents_file'];
            foreach ($fileFields as $field) {
                if ($request->hasFile($field)) {
                    if ($user->licenseDetail && $user->licenseDetail->$field) {
                        Storage::disk('public')->delete($user->licenseDetail->$field);
                    }
                    $folder = $field == 'other_documents_file' ? 'others' : ($field == 'void_cheque_file' ? 'cheques' : 'licenses');
                    $licenseData[$field] = $request->file($field)->store('employee_documents/' . $folder, 'public');
                }
            }
            $user->licenseDetail()->updateOrCreate(['user_id' => $user->id], $licenseData);

            // Availability
            $user->availability()->updateOrCreate(['user_id' => $user->id], [
                'availability_date' => $request->availability_date,
                'willing_hours' => $request->willing_hours,
                'unable_hours' => $request->unable_hours,
                'unable_days' => $request->unable_days,
            ]);

            // Office
            $user->officeDetail()->updateOrCreate(['user_id' => $user->id], [
                'employment_type' => $request->employment_type,
                'start_date' => $request->start_date,
                'job_position' => $request->job_position,
                'wage' => $request->wage,
                'other_notes' => $request->other_notes,
                'hiring_manager_name' => $request->hiring_manager_name,
                'hiring_manager_signature' => $request->hiring_manager_signature,
            ]);
        });

        return redirect()->route('employees.index')->with('success', 'Employee updated successfully!');
    }

    public function delete($id)
    {
        $employee = Employee::findOrFail($id);
        $user = $employee->user;
        
        DB::transaction(function () use ($employee, $user) {
            $employee->delete();
            $user->delete();
        });

        return redirect()->route('employees.index')->with('success', 'Employee deleted successfully!');
    }
}

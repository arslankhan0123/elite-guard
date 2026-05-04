<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Site;
use App\Models\User;
use App\Models\EmployeeCandidate;
use App\Models\EmployeeBankDetail;
use App\Models\EmployeeLicenseDetail;
use App\Models\EmployeeAvailability;
use App\Models\EmployeeOfficeDetail;
use App\Models\EmployeeOfferLetter;
use App\Models\PaySlip;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;
use App\Traits\CommonTrait;
use App\Mail\OfferLetterMail;
use Illuminate\Support\Facades\DB;

class EmployeeController extends Controller
{
    use CommonTrait;
    public function index()
    {
        $currentMonday = Carbon::now()->startOfWeek(Carbon::MONDAY)->format('Y-m-d');
        
        $employees = Employee::with(['user', 'user.sites', 'user.offerLetter', 'user.schedules' => function($query) use ($currentMonday) {
            $query->where('week_start_date', $currentMonday);
        }, 'user.schedules.shifts.site'])->get();
        
        $sites = Site::orderBy('name')->get();
        return view('admin.employees.index', compact('employees', 'sites', 'currentMonday'));
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
            $voidChequeFile = $request->hasFile('void_cheque_file') 
                ? $this->uploadDocument($request->file('void_cheque_file'), $user->id, 'bank_details_documents', []) 
                : [];

            EmployeeBankDetail::create([
                'user_id' => $user->id,
                'bank_name' => $request->bank_name,
                'institution_number' => $request->institution_number,
                'transit_number' => $request->transit_number,
                'account_number' => $request->account_number,
                'bank_address' => $request->bank_address,
                'interac_email' => $request->interac_email,
                'void_cheque_file' => $voidChequeFile,
            ]);

            // Part 3: License Information
            $securityFile = $request->hasFile('security_license_file') 
                ? $this->uploadDocument($request->file('security_license_file'), $user->id, 'license_detail_documents', []) 
                : [];
            $driversFile = $request->hasFile('drivers_license_file') 
                ? $this->uploadDocument($request->file('drivers_license_file'), $user->id, 'license_detail_documents', []) 
                : [];
            $workFile = $request->hasFile('work_eligibility_file') 
                ? $this->uploadDocument($request->file('work_eligibility_file'), $user->id, 'license_detail_documents', []) 
                : [];
            $otherFile = $request->hasFile('other_documents_file') 
                ? $this->uploadDocument($request->file('other_documents_file'), $user->id, 'license_detail_documents', []) 
                : [];

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
                $existingFiles = $user->bankDetail->void_cheque_file ?? [];
                $bankData['void_cheque_file'] = $this->uploadDocument(
                    $request->file('void_cheque_file'), 
                    $user->id, 
                    'bank_details_documents', 
                    $existingFiles
                );
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
            
            $fileFields = [
                'security_license_file' => 'license_detail_documents',
                'drivers_license_file' => 'license_detail_documents',
                'work_eligibility_file' => 'license_detail_documents',
                'other_documents_file' => 'license_detail_documents'
            ];

            foreach ($fileFields as $field => $directory) {
                if ($request->hasFile($field)) {
                    $existingFiles = $user->licenseDetail->{$field} ?? [];
                    $licenseData[$field] = $this->uploadDocument(
                        $request->file($field),
                        $user->id,
                        $directory,
                        $existingFiles
                    );
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
            // Use CommonTrait to delete all associated physical documents
            $this->DeleteEmployeeDocuments($user);

            // Delete DB records
            $employee->delete();
            $user->delete();
        });

        return redirect()->route('employees.index')->with('success', 'Employee and associated documents deleted successfully!');
    }

    public function assignSites(Request $request, $user_id)
    {
        $user = User::findOrFail($user_id);

        $siteData = [];
        if ($request->has('site_ids')) {
            foreach ($request->site_ids as $siteId) {
                $siteData[$siteId] = ['assigned_at' => now()];
            }
        }
        $user->sites()->sync($siteData);

        return redirect()->route('employees.index')->with('success', 'Sites assigned to ' . $user->name . ' successfully!');
    }

    public function updateOfferLetter(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'job_title' => 'nullable|string|max:255',
            'joining_date' => 'nullable|date',
            'salary' => 'nullable|string|max:255',
            'description' => 'nullable|string',
        ]);

        $offer = EmployeeOfferLetter::updateOrCreate(
            ['user_id' => $request->user_id],
            [
                'job_title' => $request->job_title,
                'joining_date' => $request->joining_date,
                'salary' => $request->salary,
                'description' => $request->description,
            ]
        );

        $isEmailSent = false;
        if ($request->has('send_email') && $request->send_email == '1') {
            $user = User::find($request->user_id);
            if ($user && $user->email) {
                Mail::to($user->email)->send(new OfferLetterMail($user, $offer));
                $isEmailSent = true;
            }
        }

        $offer->update(['is_email_sent' => $isEmailSent]);

        return redirect()->back()->with('success', 'Offer letter updated' . ($isEmailSent ? ' and sent via email' : '') . ' successfully!');
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
        // If existingFiles is a string (legacy), convert to array
        $existingFiles = is_array($existingFiles) ? $existingFiles : ($existingFiles ? [$existingFiles] : []);
        
        return array_merge($existingFiles, ["{$baseUrl}/{$relativeDir}/{$filename}"]);
    }

    public function updatePaySlip(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:2000|max:2100',
            'file' => 'required|file|mimes:pdf,doc,docx,png,jpg,jpeg|max:5120',
        ]);

        $user_id = $request->user_id;
        $month = $request->month;
        $year = $request->year;
        $file = $request->file('file');

        // Check for existing pay slip for this month/year
        $existing = PaySlip::where('user_id', $user_id)
            ->where('month', $month)
            ->where('year', $year)
            ->first();

        if ($existing) {
            // Delete old physical file
            $baseUrl = rtrim(config('app.url'), '/');
            $relativePath = str_replace($baseUrl . '/', '', $existing->file_path);
            $relativePath = ltrim($relativePath, '/');
            $fullPath = public_path($relativePath);
            
            if (File::exists($fullPath)) {
                File::delete($fullPath);
            }
        }

        // Custom naming: user_id, timestamp and unique string of 20 digits
        $timestamp = time();
        $uniqueStr = bin2hex(random_bytes(10)); // 20 hex characters
        $extension = $file->getClientOriginalExtension();
        $filename = "{$user_id}_{$timestamp}_{$uniqueStr}.{$extension}";

        $relativeDir = "documents/pay_slips";
        $destinationPath = public_path($relativeDir);

        if (!File::exists($destinationPath)) {
            File::makeDirectory($destinationPath, 0777, true);
        }

        $file->move($destinationPath, $filename);

        $baseUrl = rtrim(config('app.url'), '/');
        $filePath = "{$baseUrl}/{$relativeDir}/{$filename}";

        if ($existing) {
            $existing->update(['file_path' => $filePath]);
            $message = 'Pay slip updated successfully!';
        } else {
            PaySlip::create([
                'user_id' => $user_id,
                'month' => $month,
                'year' => $year,
                'file_path' => $filePath,
            ]);
            $message = 'Pay slip uploaded successfully!';
        }

        return redirect()->back()->with('success', $message);
    }
}

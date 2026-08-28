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
use App\Models\Policy;
use App\Models\TaxDocument;
use App\Models\WeeklyRunSheet;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;
use App\Traits\CommonTrait;
use App\Mail\OfferLetterMail;
use App\Mail\EmployeeWelcomeMail;
use Illuminate\Support\Facades\DB;
use App\Services\ProfileCompletionService;

class EmployeeController extends Controller
{
    use CommonTrait;
    public function index(ProfileCompletionService $profileCompletion)
    {
        $currentMonday = Carbon::now()->startOfWeek(Carbon::MONDAY)->format('Y-m-d');
        
        $employees = Employee::with(['user', 'user.candidate', 'user.bankDetail', 'user.licenseDetail',
            'user.orientationAttempts', 'user.signedPolicies', 'user.taxDocumentSubmissions',
            'user.sites', 'user.weeklyRunSheets', 'user.offerLetter', 'user.runSheets' => function($query) use ($currentMonday) {
            $weekEnd = Carbon::parse($currentMonday)->endOfWeek(Carbon::SUNDAY)->format('Y-m-d');
            $query->whereBetween('date', [$currentMonday, $weekEnd]);
        }, 'user.schedules' => function($query) use ($currentMonday) {
            $query->where('week_start_date', $currentMonday);
        }, 'user.schedules.shifts.site', 'user.schedules.shifts.weeklyRunSheet'])->get();
        
        $employees->each(function ($employee) use ($profileCompletion) {
            $employee->user->profile_completion = $profileCompletion->calculate($employee->user);
        });
        $sites = Site::orderBy('name')->get();
        $weeklyRunSheets = WeeklyRunSheet::with(['entries.site'])->withCount('entries')->orderByDesc('week_start_date')->get();
        return view('admin.employees.index', compact('employees', 'sites', 'weeklyRunSheets', 'currentMonday'));
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
            'permissions' => 'nullable|array',
            
            // Files
            'void_cheque_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'security_license_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'drivers_license_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'work_eligibility_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'other_documents_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'status' => 'nullable|in:0,1',
        ]);

        $plainPassword = $request->password;
        $isEmailSent = false;
        $role = auth()->user()->role === 'SuperAdmin' ? $request->role : 'Employee';

        DB::transaction(function () use ($request, $plainPassword, $role, &$isEmailSent) {
            $user = User::create([
                'name' => $request->first_name . ' ' . $request->last_name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'real_password' => $request->password,
                'role' => $role,
                'email_verified_at' => in_array($role, ['Admin', 'SuperAdmin'], true) ? now() : null,
                'admin_permissions' => $role === 'Admin' ? $this->validatedAdminPermissions($request) : null,
                'status' => $request->get('status', 1),
            ]);

            // Part 1: Candidate Information
            EmployeeCandidate::create([
                'user_id' => $user->id,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'designation' => $request->designation,
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

            // Send welcome email with credentials if toggle is on
            if ($request->has('send_email') && $request->send_email == '1') {
                try {
                    Mail::to($user->email)->send(new EmployeeWelcomeMail($user, $plainPassword));
                    $isEmailSent = true;
                } catch (\Exception $e) {
                    logger()->error('Failed to send welcome email: ' . $e->getMessage());
                }
            }

            // Keep the legacy Employee record for compatibility
            Employee::create([
                'user_id' => $user->id,
                'phone' => $request->phone,
                'address' => $request->address,
                'status' => $request->get('status', 1),
                'is_email_sent' => $isEmailSent,
            ]);
        });

        $message = 'Employee created successfully!' . ($isEmailSent ? ' Login credentials sent via email.' : '');
        return redirect()->route('employees.index')->with('success', $message);
    }

    public function show($id, ProfileCompletionService $profileCompletion)
    {
        $employee = Employee::with([
            'user', 
            'user.candidate', 
            'user.bankDetail', 
            'user.licenseDetail', 
            'user.availability', 
            'user.officeDetail', 
            'user.offerLetter', 
            'user.paySlips',
            'user.orientationAttempts.orientation.questions.options',
            'user.signedPolicies.policy',
            'user.taxDocumentSubmissions.taxDocument'
        ])->findOrFail($id);
        
        $profileCompletion = $profileCompletion->calculate($employee->user);
        $allPolicies = Policy::where('status', true)->get();
        $allTaxDocs = TaxDocument::all();
        
        return view('admin.employees.show', compact('employee', 'profileCompletion', 'allPolicies', 'allTaxDocs'));
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
            'permissions' => 'nullable|array',

            'password' => 'nullable|string|min:8|confirmed',
            'password_confirmation' => 'required_with:password',
            'status' => 'nullable|in:0,1',
        ]);

        $isEmailSent = false;
        $role = auth()->user()->role === 'SuperAdmin' ? $request->role : $user->role;

        DB::transaction(function () use ($request, $user, $employee, $role, &$isEmailSent) {
            $user->update([
                'name' => $request->first_name . ' ' . $request->last_name,
                'email' => $request->email,
                'role' => $role,
                'email_verified_at' => in_array($role, ['Admin', 'SuperAdmin'], true)
                    ? ($user->email_verified_at ?? now())
                    : $user->email_verified_at,
                'admin_permissions' => $role === 'Admin'
                    ? (auth()->user()->role === 'SuperAdmin' ? $this->validatedAdminPermissions($request) : $user->admin_permissions)
                    : null,
                'status' => $request->get('status', 1),
            ]);

            $plainPassword = null;
            if ($request->filled('password')) {
                $plainPassword = $request->password;
                $user->update([
                    'password' => Hash::make($request->password),
                    'real_password' => $request->password,
                ]);
            } else {
                // Use stored real_password for the email if password wasn't changed
                $plainPassword = $user->real_password;
            }

            // Update Sections
            $user->candidate()->updateOrCreate(['user_id' => $user->id], [
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'designation' => $request->designation,
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

            // Send welcome email with credentials if toggle is on
            if ($request->has('send_email') && $request->send_email == '1') {
                try {
                    Mail::to($user->email)->send(new EmployeeWelcomeMail($user, $plainPassword));
                    $isEmailSent = true;
                } catch (\Exception $e) {
                    logger()->error('Failed to send welcome email on update: ' . $e->getMessage());
                }
            }

            // Update is_email_sent and status on the employee record
            $employee->update([
                'is_email_sent' => $isEmailSent ? true : $employee->is_email_sent,
                'status' => $request->get('status', 1),
            ]);
        });

        $message = 'Employee updated successfully!' . ($isEmailSent ? ' Login credentials sent via email.' : '');
        return redirect()->route('employees.index')->with('success', $message);
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

    private function validatedAdminPermissions(Request $request): array
    {
        $submitted = $request->input('permissions', []);

        return collect(config('admin_permissions.modules'))
            ->mapWithKeys(function ($label, $module) use ($submitted) {
                $actions = collect(config('admin_permissions.actions'))
                    ->mapWithKeys(fn ($actionLabel, $action) => [
                        $action => isset($submitted[$module][$action]),
                    ])->all();
                return [$module => $actions];
            })->all();
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

    public function assignWeeklyRunSheets(Request $request, $user_id)
    {
        $validated = $request->validate([
            'weekly_run_sheet_ids' => ['nullable', 'array'],
            'weekly_run_sheet_ids.*' => ['integer', 'exists:weekly_run_sheets,id'],
        ]);

        $user = User::findOrFail($user_id);
        $assignments = collect($validated['weekly_run_sheet_ids'] ?? [])
            ->mapWithKeys(fn ($id) => [(int) $id => ['assigned_at' => now()]])
            ->all();

        $user->weeklyRunSheets()->sync($assignments);

        return redirect()->route('employees.index')->with('success', 'Runsheets assigned to ' . $user->name . ' successfully!');
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
                try {
                    Mail::to($user->email)->send(new OfferLetterMail($user, $offer));
                    $isEmailSent = true;
                } catch (\Exception $e) {
                    logger()->error('Failed to send offer letter email: ' . $e->getMessage());
                }
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

    public function checkPaySlip(Request $request)
    {
        $userId = $request->query('user_id');
        $month = $request->query('month');
        $year = $request->query('year');

        if (!$userId || !$month || !$year) {
            return response()->json(['exists' => false]);
        }

        $paySlip = \App\Models\PaySlip::where('user_id', $userId)
            ->where('month', $month)
            ->where('year', $year)
            ->first();

        if ($paySlip) {
            return response()->json([
                'exists' => true,
                'url' => $paySlip->file_path
            ]);
        }

        return response()->json(['exists' => false]);
    }

    public function updateProfilePicture(Request $request, $id)
    {
        $request->validate([
            'profile_picture' => 'required|image|mimes:jpeg,png,jpg,gif|max:5120',
        ]);

        $employee = Employee::findOrFail($id);
        $user = $employee->user;

        if ($request->hasFile('profile_picture')) {
            $file = $request->file('profile_picture');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

            $destinationPath = public_path('images/profileImages');
            if (!File::exists($destinationPath)) {
                File::makeDirectory($destinationPath, 0755, true);
            }

            $file->move($destinationPath, $filename);

            // Delete old profile picture if exists
            $candidate = $user->candidate;
            if ($candidate && $candidate->profile_picture) {
                $parsedPath = parse_url($candidate->profile_picture, PHP_URL_PATH);
                $relativePath = ltrim($parsedPath, '/');
                $oldFile = public_path($relativePath);
                if (File::exists($oldFile)) {
                    File::delete($oldFile);
                }
            }

            $dbPath = url('images/profileImages/' . $filename);
            $user->candidate()->updateOrCreate(
                ['user_id' => $user->id],
                ['profile_picture' => $dbPath]
            );

            return response()->json([
                'success' => true,
                'message' => 'Profile picture updated successfully',
                'url' => $dbPath
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'No image was selected'
        ], 400);
    }

    public function deleteProfilePicture($id)
    {
        $employee = Employee::findOrFail($id);
        $user = $employee->user;
        $candidate = $user->candidate;

        if ($candidate && $candidate->profile_picture) {
            $parsedPath = parse_url($candidate->profile_picture, PHP_URL_PATH);
            $relativePath = ltrim($parsedPath, '/');
            $oldFile = public_path($relativePath);
            if (File::exists($oldFile)) {
                File::delete($oldFile);
            }

            $candidate->update(['profile_picture' => null]);

            return response()->json([
                'success' => true,
                'message' => 'Profile picture deleted successfully'
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'No profile picture to delete'
        ], 400);
    }
}

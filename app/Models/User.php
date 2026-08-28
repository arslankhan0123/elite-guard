<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'email_verified_at',
        'password',
        'real_password',
        'role',
        'admin_permissions',
        'fcm_token',
        'status',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'admin_permissions' => 'array',
            'status' => 'boolean',
        ];
    }

    public function hasAdminPermission(string $module, string $action = 'list'): bool
    {
        if ($this->role === 'SuperAdmin') {
            return true;
        }

        return $this->role === 'Admin'
            && (bool) data_get($this->admin_permissions ?? [], "$module.$action", false);
    }

    public function canAccessAdminModule(string $module): bool
    {
        if ($this->role === 'SuperAdmin') {
            return true;
        }

        return collect(($this->admin_permissions ?? [])[$module] ?? [])->contains(true);
    }

    public function adminLandingRoute(): string
    {
        $routes = [
            'dashboard' => 'dashboard', 'companies' => 'companies.index', 'sites' => 'sites.index',
            'site-tours' => 'sites.tours.all', 'nfc' => 'nfc.index', 'schedules' => 'schedules.index',
            'open-shifts' => 'open-shifts.index',
            'availabilities' => 'availabilities.index', 'time-clocks' => 'time-clocks.index',
            'attendance' => 'attendance.index', 'reports-forms' => 'reports.all',
            'management-reports' => 'reports.index', 'shifts-reports' => 'reports.index',
            'employees' => 'employees.index', 'policies' => 'policies.index',
            'orientations' => 'orientations.index', 'pay-slips' => 'pay-slips.index',
            'tax-docs' => 'tax-docs.index', 'numbers' => 'numbers.index',
            'notice-board' => 'notice-board.index', 'post-esc' => 'post-esc.index',
            'dispatches' => 'dispatches.index',
        ];

        return collect($routes)->first(fn ($route, $module) => $this->hasAdminPermission($module, 'list')) ?? 'profile.edit';
    }

    public function employee()
    {
        return $this->hasOne(Employee::class);
    }

    public function candidate()
    {
        return $this->hasOne(EmployeeCandidate::class);
    }

    public function bankDetail()
    {
        return $this->hasOne(EmployeeBankDetail::class);
    }

    public function licenseDetail()
    {
        return $this->hasOne(EmployeeLicenseDetail::class);
    }

    public function availability()
    {
        return $this->hasOne(EmployeeAvailability::class);
    }

    public function officeDetail()
    {
        return $this->hasOne(EmployeeOfficeDetail::class);
    }

    public function offerLetter()
    {
        return $this->hasOne(EmployeeOfferLetter::class);
    }

    public function sites()
    {
        return $this->belongsToMany(Site::class, 'site_user')->withPivot('assigned_at')->withTimestamps();
    }

    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }

    public function paySlips()
    {
        return $this->hasMany(PaySlip::class);
    }

    public function openShiftClaims()
    {
        return $this->hasMany(OpenShiftClaim::class);
    }

    public function assessments()
    {
        return $this->hasMany(Assessment::class);
    }

    public function orientationAttempts()
    {
        return $this->hasMany(OrientationAttempt::class);
    }

    public function signedPolicies()
    {
        return $this->hasMany(SignedPolicy::class);
    }

    public function taxDocumentSubmissions()
    {
        return $this->hasMany(TaxDocumentSubmission::class);
    }

    public function dailyVehicleChecklists()
    {
        return $this->hasMany(DailyVehicleChecklist::class);
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public function runSheets()
    {
        return $this->hasMany(RunSheet::class);
    }

    public function weeklyRunSheets()
    {
        return $this->belongsToMany(WeeklyRunSheet::class, 'user_weekly_run_sheet')
            ->withPivot('assigned_at')
            ->withTimestamps();
    }

    public function siteScans()
    {
        return $this->hasMany(SiteScan::class);
    }

    public function siteItems()
    {
        return $this->hasMany(SiteItem::class);
    }

    public function siteItemScans()
    {
        return $this->hasMany(SiteItemScan::class);
    }

    /**
     * Route notifications for the FCM channel.
     *
     * @return string|array
     */
    public function routeNotificationForFcm()
    {
        return $this->fcm_token;
    }
}

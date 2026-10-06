<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;

class Tenant extends Model
{
    protected $connection = 'master';
    protected $table      = 'tenants';

    protected $fillable = [
        'name',
        'slug',
        'db_name',
        'admin_email',
        'phone',
        'is_active',
        'notes',
        'subscription_type',
        'subscription_period',
        'subscription_amount',
        'subscription_starts_at',
        'subscription_ends_at',
        'expiry_notified',
    ];

    protected function casts(): array
    {
        return [
            'is_active'              => 'boolean',
            'expiry_notified'        => 'boolean',
            'subscription_starts_at' => 'datetime',
            'subscription_ends_at'   => 'datetime',
        ];
    }

    public function userLookups()
    {
        return $this->hasMany(TenantUserLookup::class, 'tenant_id');
    }

    /**
     * Convert any tenant name to a valid DB name.
     */
    public static function dbNameFromSlug(string $slug): string
    {
        return 'elite_guard_tenant_' . preg_replace('/[^a-z0-9_]/', '_', strtolower($slug));
    }

    /**
     * Compute subscription end date from type + period.
     */
    public static function computeEndsAt(string $type, ?string $period, \Carbon\Carbon $startsAt): \Carbon\Carbon
    {
        if ($type === 'monthly') {
            return (clone $startsAt)->addMonth();
        }
        if ($type === 'yearly') {
            return (clone $startsAt)->addYear();
        }
        // trial
        return match ($period) {
            '1day'   => (clone $startsAt)->addDay(),
            '2days'  => (clone $startsAt)->addDays(2),
            '3days'  => (clone $startsAt)->addDays(3),
            '1week'  => (clone $startsAt)->addWeek(),
            '2weeks' => (clone $startsAt)->addWeeks(2),
            '3weeks' => (clone $startsAt)->addWeeks(3),
            '1month' => (clone $startsAt)->addMonth(),
            default  => (clone $startsAt)->addDay(),
        };
    }

    /**
     * Whether this tenant's subscription/trial is currently active (not expired).
     */
    public function isSubscriptionActive(): bool
    {
        if (! $this->subscription_ends_at) {
            return true;
        }
        return now()->lessThanOrEqualTo($this->subscription_ends_at);
    }
}

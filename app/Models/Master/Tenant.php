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
        'is_active',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
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
}

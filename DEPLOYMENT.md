# Deployment Guide — Multi-Tenant Laravel Setup

## Overview

This app uses a **two-database architecture**:
- **Master DB** (`elite_guard_master`) — stores `master_admins`, `tenants`, `tenant_user_lookup`
- **Tenant DB** (`elite_guard_tenant_*`) — stores all app data per tenant

The master connection reuses `DB_HOST`, `DB_USERNAME`, `DB_PASSWORD` from the default connection — only the DB name differs via `MASTER_DB_DATABASE`.

---

## Environment Setup

### Standard Server (AWS, DigitalOcean, VPS, etc.)

Databases are created with normal privileges — no manual pre-creation needed.

Copy your local `.env` and change only these values:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

# Your existing live DB (the one with all real data)
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_live_db_name
DB_USERNAME=your_live_db_user
DB_PASSWORD=your_live_db_password

# Master DB — only the name differs, same host/user/pass
MASTER_DB_DATABASE=elite_guard_master
```

> ✅ On standard servers, `TenantService::createDatabase()` auto-creates tenant databases.
> No manual DB creation needed.

---

### Shared Hosting (Hostinger, cPanel, etc.)

Shared hosting **does not allow** `CREATE DATABASE` via SQL. You must create databases manually first.

**Step 1 — Create databases via hPanel/cPanel:**

> ⚠️ Hostinger auto-prepends your account prefix (e.g. `u227527917_`) to all DB names.
> The 14-character suffix limit applies to what you type in the form.

| What to type | Full DB name created |
|---|---|
| `eg_master` | `u227527917_eg_master` |
| `eg_tenant` | `u227527917_eg_tenant` |

For each database, create a user and assign it — or assign your existing DB user.

**Step 2 — Update `.env`:**

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

# Existing live DB (unchanged)
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=u227527917_elite_guard
DB_USERNAME=u227527917_elite_guard
DB_PASSWORD=your_password

# Master DB — same user must have access to this DB too
MASTER_DB_DATABASE=u227527917_eg_master
```

> ⚠️ `config/database.php` master connection uses `DB_USERNAME` / `DB_PASSWORD`.
> Make sure your existing DB user has privileges on the master DB as well
> (assign it via hPanel → Databases → Management).

> ✅ `TenantService::createDatabase()` is wrapped in try/catch — it will log a warning
> and continue if `CREATE DATABASE` is denied. Pre-create the tenant DB via hPanel before
> running `EliteGuardTenantSeeder`.

---

## EliteGuardTenantSeeder — What to Update

Open `database/seeders/EliteGuardTenantSeeder.php` and update these 3 values:

```php
$tenant = Tenant::on('master')->firstOrCreate(
    ['slug' => $slug],
    [
        'name'        => 'Your Company Name',       // ← real company name
        'db_name'     => $dbName,                   // auto-generated from slug
        'admin_email' => 'admin@yourcompany.com',   // ← real SuperAdmin email on live
        'is_active'   => true,
        'notes'       => 'Migrated from existing live database.',
    ]
);
```

> `TenantService::seedFromExistingDatabase($tenant, 'mysql')` reads from `DB_DATABASE`
> in `.env` — which already points to your live DB. No other seeder changes needed.

**For shared hosting** — also hardcode `db_name` to the full prefixed name:

```php
'db_name' => 'u227527917_eg_tenant',   // full name with hosting prefix
```

---

## Run Order on Live Server

```bash
# 1. Clear cached config (always run first after .env changes)
php artisan config:clear
php artisan cache:clear

# 2. Set up master DB tables + master admin account
php artisan db:seed --class=MasterSeeder --force

# 3. Create tenant record + migrate existing live data into tenant DB
php artisan db:seed --class=EliteGuardTenantSeeder --force

# 4. Run phone migration on master DB (if not already run)
php artisan migrate \
  --path=database/migrations/master/2026_09_30_000010_add_phone_to_tenants_table.php \
  --database=master \
  --force

# 5. Final cleanup
php artisan view:clear
php artisan optimize
```

---

## Troubleshooting

### `Access denied for user ... to database 'elite_guard_master'`
- Your DB user doesn't have access to the master DB
- **Fix:** Assign the existing DB user to the master DB via hPanel → Databases → Management

### `MASTER_DB_DATABASE` not being picked up
- `config/database.php` master block only reads `MASTER_DB_DATABASE` from env
- Username/password come from `DB_USERNAME` / `DB_PASSWORD`
- Run `php artisan config:clear` then `php artisan config:show database | grep master` to verify

### Tenant DB name mismatch
- The `db_name` stored in the `tenants` table must be the **full** database name
- On Hostinger: `u227527917_eg_tenant` (with prefix)
- On AWS/standard: `elite_guard_tenant_eliteguard` (no prefix)

---

## Notes

- `APP_ENV=local` in `.env` on live server will cause issues — always set `production`
- `APP_DEBUG=false` on production — never expose stack traces publicly
- The `MASTER_DB_DATABASE` env var is the **only** extra env key this architecture adds

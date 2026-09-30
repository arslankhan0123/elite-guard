# Tenant Implementation Architecture & Security Specification

This document details the technical implementation of the **Multi-Tenant System** in Elite Guard. It covers tenant isolation across modules (Companies, Sites, Unified Reports & Forms), direct URL authorization protection, flash message error redirects, API data storage, and batch SQL script.

---

## Table of Contents
1. [1. Tenant Module Overview](#1-tenant-module-overview)
2. [2. Companies Module & Tenant Isolation](#2-companies-module--tenant-isolation)
3. [3. Sites Module & Unauthorized URL Guard](#3-sites-module--unauthorized-url-guard)
4. [4. System Reports & Unified Reports Filtering](#4-system-reports--unified-reports-filtering)
5. [5. Direct URL Security & Unauthorized Redirect Guard](#5-direct-url-security--unauthorized-redirect-guard)
6. [6. Forms & Reports API Data Flow](#6-forms--reports-api-data-flow)
7. [7. SQL Batch Assignment Script](#7-sql-batch-assignment-script)

---

## 1. Tenant Module Overview

The **Tenant Module** serves as the primary multi-tenancy core of the application, ensuring strict data isolation per organization.

### Key Components:
* **Database Schema (`tenants`):** Stores tenant details (`id`, `name`, `status`, `created_at`, `updated_at`).
* **User Multi-Tenancy:**
  - `users` table contains `tenant_id` foreign key.
  - **MasterAdmin:** Bypasses tenant scoping (`role === 'MasterAdmin'`) to access cross-tenant data.
  - **Tenant Users / Admins:** Bound to their respective `tenant_id` and restricted from viewing or modifying other tenants' resources.

---

## 2. Companies Module & Tenant Isolation

Companies represent clients/organizations managed under a tenant.

### Implementation:
* **Database:** `companies` table contains a `tenant_id` column.
* **Auto Assignment:** On company creation, the logged-in user's `tenant_id` is stored.
* **Scoped Repository (`CompanyRepository`):**
  ```php
  if ($authUser->role !== 'MasterAdmin') {
      $query->where('tenant_id', $authUser->tenant_id);
  }
  ```

---

## 3. Sites Module & Unauthorized URL Guard

Sites are locations attached to companies and tenants.

### Security & Authorization Guard:
1. **Dropdown & Query Scoping:** Sites dropdowns and listings are scoped by `tenant_id`.
2. **Direct URL Manipulation Protection:**
   - If a user manually alters the browser URL to view/edit/delete a site belonging to another tenant (e.g., `/sites/edit/{site_id}`), the system checks repository permissions.
   - If unauthorized, it redirects back to `/sites` with an error message instead of revealing unauthorized content:
     ```php
     $site = $this->siteRepo->findSiteById($site_id);
     if (! $site) {
         return redirect()->route('sites.index')->with('error', 'Unauthorized access! You do not have permission to view or edit this site.');
     }
     ```

---

## 4. System Reports & Unified Reports Filtering

All report and form models are integrated into the multi-tenant architecture.

### Implementation:
1. **Database Migration (`2026_09_30_000001_add_tenant_id_to_report_and_form_tables.php`):**
   Added `tenant_id` (foreign key referencing `tenants(id)`) to 8 report & form tables:
   - `report_general_forms`
   - `report_incident_forms`
   - `report_security_guard_disciplinary_forms`
   - `report_daily_shift_forms`
   - `assessments`
   - `daily_vehicle_checklists`
   - `fire_watch_reports`
   - `shift_adjustment_forms`

2. **Backend Automatic Query Scoping (`UnifiedReportController::applyFilters()`):**
   - Automatically scopes list views by `tenant_id` or authoring user's tenant:
     ```php
     $authUser = auth()->user();
     if ($authUser && $authUser->role !== 'MasterAdmin') {
         if ($authUser->tenant_id) {
             $query->where(function ($q) use ($authUser) {
                 $q->where('tenant_id', $authUser->tenant_id)
                   ->orWhereHas('user', fn ($uq) => $uq->where('tenant_id', $authUser->tenant_id));
             });
         } else {
             $query->where('user_id', $authUser->id);
         }
     }
     ```

3. **Frontend Table Display:** Added **Tenant ID** column to table partial views.

---

## 5. Direct URL Security & Unauthorized Redirect Guard

To mirror the Sites module behavior in Unified Reports and Forms:

1. **Instance Retrieval Guard (`getReportInstance`):**
   When fetching a report instance for `show`, `edit`, `update`, `downloadPdf`, or `destroy`, the controller verifies tenant ownership:
   ```php
   if ($authUser && $authUser->role !== 'MasterAdmin') {
       if ($authUser->tenant_id) {
           $query->where(function ($q) use ($authUser) {
               $q->where('tenant_id', $authUser->tenant_id)
                 ->orWhereHas('user', fn ($uq) => $uq->where('tenant_id', $authUser->tenant_id));
           });
       } else {
           $query->where('user_id', $authUser->id);
       }
   }
   return $query->find($id);
   ```

2. **Redirect with Flash Error (No 404 Page):**
   If an unauthorized user attempts to view a report via direct URL manipulation (e.g. `/security-reports/show/daily-shift/246`), instead of displaying a generic 404 page, the system redirects to `/security-reports/all` with a flash error alert:
   ```php
   $report = $this->getReportInstance($type, $id);
   if (!$report) {
       return redirect()->route('reports.all', ['type' => $type])
           ->with('error', 'Unauthorized access! You do not have permission to view this report.');
   }
   ```

---

## 6. Forms & Reports API Data Flow

All REST API submit endpoints automatically extract `$user->tenant_id` from the authenticated JWT/Sanctum bearer token and persist it.

### Covered Endpoints:
- `POST /api/forms/assessments/store`
- `POST /api/forms/daily-vehicle-checklist/store`
- `POST /api/forms/shift-adjustment/store`
- `POST /api/reports/security-guard-disciplinary-form/store`
- `POST /api/reports/incident-report-form/store`
- `POST /api/reports/general-report-form/store`
- `POST /api/reports/daily-shift-report-form/store`
- `POST /api/reports/fire-watch/store`

### Repository Code:
```php
// FormsRepository & ReportsRepository
$user = Auth::user();
$record = Model::create([
    'tenant_id' => $user->tenant_id ?? null,
    'user_id'   => $user->id,
    ...
]);
```

---

## 7. SQL Batch Assignment Script

Execute this SQL query to update existing database records with default tenant assignment:

```sql
UPDATE report_general_forms SET tenant_id = 3 WHERE tenant_id IS NULL;
UPDATE report_incident_forms SET tenant_id = 3 WHERE tenant_id IS NULL;
UPDATE report_security_guard_disciplinary_forms SET tenant_id = 3 WHERE tenant_id IS NULL;
UPDATE report_daily_shift_forms SET tenant_id = 3 WHERE tenant_id IS NULL;
UPDATE assessments SET tenant_id = 3 WHERE tenant_id IS NULL;
UPDATE daily_vehicle_checklists SET tenant_id = 3 WHERE tenant_id IS NULL;
UPDATE fire_watch_reports SET tenant_id = 3 WHERE tenant_id IS NULL;
UPDATE shift_adjustment_forms SET tenant_id = 3 WHERE tenant_id IS NULL;
```

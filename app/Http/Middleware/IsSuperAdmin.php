<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsSuperAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        if (! $user || ! in_array($user->role, ['SuperAdmin', 'Admin'], true)) {
            return redirect('/')->with('error', 'Unauthorized access! Admin privileges required.');
        }

        if ($user->role === 'SuperAdmin') {
            return $next($request);
        }

        [$module, $action] = $this->permissionForRoute((string) optional($request->route())->getName());
        abort_unless($module && $user->hasAdminPermission($module, $action), 403, 'You do not have permission to access this feature.');

        return $next($request);
    }

    private function permissionForRoute(string $routeName): array
    {
        $module = match (true) {
            $routeName === 'dashboard' => 'dashboard',
            str_starts_with($routeName, 'sites.tours.') => 'site-tours',
            str_starts_with($routeName, 'companies.') => 'companies',
            str_starts_with($routeName, 'sites.') => 'sites',
            str_starts_with($routeName, 'nfc.') => 'nfc',
            str_starts_with($routeName, 'schedules.'), str_starts_with($routeName, 'run-sheets.') => 'schedules',
            str_starts_with($routeName, 'open-shifts.') => 'open-shifts',
            str_starts_with($routeName, 'availabilities.') => 'availabilities',
            str_starts_with($routeName, 'time-clocks.') => 'time-clocks',
            str_starts_with($routeName, 'attendance.') => 'attendance',
            $routeName === 'reports.index' => request()->get('type') === 'shifts' ? 'shifts-reports' : 'management-reports',
            str_starts_with($routeName, 'forms.'), str_starts_with($routeName, 'security-reports.'), str_starts_with($routeName, 'reports.') => 'reports-forms',
            str_starts_with($routeName, 'employees.') => 'employees',
            str_starts_with($routeName, 'policies.') => 'policies',
            str_starts_with($routeName, 'orientations.') => 'orientations',
            str_starts_with($routeName, 'pay-slips.') => 'pay-slips',
            str_starts_with($routeName, 'tax-docs.') => 'tax-docs',
            str_starts_with($routeName, 'numbers.') => 'numbers',
            str_starts_with($routeName, 'notice-board.') => 'notice-board',
            str_starts_with($routeName, 'post-esc.') => 'post-esc',
            str_starts_with($routeName, 'dispatches.') => 'dispatches',
            default => null,
        };

        $action = match (true) {
            str_contains($routeName, 'delete'), str_contains($routeName, 'destroy') => 'delete',
            str_contains($routeName, 'create'), str_contains($routeName, 'store') => 'create',
            str_contains($routeName, 'edit'), str_contains($routeName, 'update'), str_contains($routeName, 'approve'), str_contains($routeName, 'reject'), str_contains($routeName, 'assign') => 'update',
            $routeName === 'dashboard', str_ends_with($routeName, '.index'),
                in_array($routeName, ['reports.all', 'sites.tours.all', 'sites.tours', 'sites.nfcTags', 'schedules.ajax', 'open-shifts.claims'], true),
                str_starts_with($routeName, 'forms.'), str_starts_with($routeName, 'security-reports.') => 'list',
            default => 'view',
        };

        return [$module, $action];
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ReportDailyShiftForm;
use App\Models\ReportGeneralForm;
use App\Models\ReportIncidentForm;
use App\Models\ReportSecurityGuardDisciplinaryForm;
use Illuminate\Http\Request;

use App\Models\User;
use Carbon\Carbon;

class SecurityReportController extends Controller
{
    public function disciplinary(Request $request)
    {
        $query = ReportSecurityGuardDisciplinaryForm::with('user');

        if ($request->user_id) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->date_range) {
            $this->applyDateFilter($query, $request->date_range);
        }

        $reports = $query->latest()->paginate(10);
        $users = User::all();
        return view('admin.security-reports.disciplinary', compact('reports', 'users'));
    }

    public function incident(Request $request)
    {
        $query = ReportIncidentForm::with(['user', 'images']);

        if ($request->user_id) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->date_range) {
            $this->applyDateFilter($query, $request->date_range);
        }

        $reports = $query->latest()->paginate(10);
        $users = User::all();
        return view('admin.security-reports.incident', compact('reports', 'users'));
    }

    public function general(Request $request)
    {
        $query = ReportGeneralForm::with(['user', 'images']);

        if ($request->user_id) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->date_range) {
            $this->applyDateFilter($query, $request->date_range);
        }

        $reports = $query->latest()->paginate(10);
        $users = User::all();
        return view('admin.security-reports.general', compact('reports', 'users'));
    }

    public function dailyShift(Request $request)
    {
        $query = ReportDailyShiftForm::with(['user', 'patrolEntries']);

        if ($request->user_id) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->date_range) {
            $this->applyDateFilter($query, $request->date_range);
        }

        $reports = $query->latest()->paginate(10);
        $users = User::all();
        return view('admin.security-reports.daily-shift', compact('reports', 'users'));
    }

    private function applyDateFilter($query, $range)
    {
        switch ($range) {
            case 'today':
                $query->whereDate('created_at', Carbon::today());
                break;
            case 'yesterday':
                $query->whereDate('created_at', Carbon::yesterday());
                break;
            case 'current_week':
                $query->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
                break;
            case 'last_week':
                $query->whereBetween('created_at', [Carbon::now()->subWeek()->startOfWeek(), Carbon::now()->subWeek()->endOfWeek()]);
                break;
            case 'current_month':
                $query->whereMonth('created_at', Carbon::now()->month)
                      ->whereYear('created_at', Carbon::now()->year);
                break;
            case 'last_month':
                $query->whereMonth('created_at', Carbon::now()->subMonth()->month)
                      ->whereYear('created_at', Carbon::now()->subMonth()->year);
                break;
            case 'current_year':
                $query->whereYear('created_at', Carbon::now()->year);
                break;
            case 'last_year':
                $query->whereYear('created_at', Carbon::now()->subYear()->year);
                break;
        }
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ReportDailyShiftForm;
use App\Models\ReportGeneralForm;
use App\Models\ReportIncidentForm;
use App\Models\ReportSecurityGuardDisciplinaryForm;
use Illuminate\Http\Request;

class SecurityReportController extends Controller
{
    public function disciplinary(Request $request)
    {
        $reports = ReportSecurityGuardDisciplinaryForm::with('user')->latest()->paginate(10);
        return view('admin.security-reports.disciplinary', compact('reports'));
    }

    public function incident(Request $request)
    {
        $reports = ReportIncidentForm::with(['user', 'images'])->latest()->paginate(10);
        return view('admin.security-reports.incident', compact('reports'));
    }

    public function general(Request $request)
    {
        $reports = ReportGeneralForm::with(['user', 'images'])->latest()->paginate(10);
        return view('admin.security-reports.general', compact('reports'));
    }

    public function dailyShift(Request $request)
    {
        $reports = ReportDailyShiftForm::with(['user', 'patrolEntries'])->latest()->paginate(10);
        return view('admin.security-reports.daily-shift', compact('reports'));
    }
}

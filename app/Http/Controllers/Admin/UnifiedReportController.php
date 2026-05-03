<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ReportDailyShiftForm;
use App\Models\ReportGeneralForm;
use App\Models\ReportIncidentForm;
use App\Models\ReportSecurityGuardDisciplinaryForm;
use App\Models\Assessment;
use App\Models\DailyVehicleChecklist;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class UnifiedReportController extends Controller
{
    public function index(Request $request)
    {
        $type = $request->get('type', 'general'); // Default to general report
        $users = User::all();
        $data = [];
        $view = 'admin.unified-reports.partials.' . $type;

        switch ($type) {
            case 'disciplinary':
                $query = ReportSecurityGuardDisciplinaryForm::with('user');
                $this->applyFilters($query, $request);
                $data['reports'] = $query->latest()->paginate(10);
                break;
            case 'incident':
                $query = ReportIncidentForm::with(['user', 'images']);
                $this->applyFilters($query, $request);
                $data['reports'] = $query->latest()->paginate(10);
                break;
            case 'general':
                $query = ReportGeneralForm::with(['user', 'images']);
                $this->applyFilters($query, $request);
                $data['reports'] = $query->latest()->paginate(10);
                break;
            case 'daily-shift':
                $query = ReportDailyShiftForm::with(['user', 'patrolEntries']);
                $this->applyFilters($query, $request);
                $data['reports'] = $query->latest()->paginate(10);
                break;
            case 'assessments':
                $query = Assessment::with('user');
                $this->applyFilters($query, $request);
                $data['assessments'] = $query->latest()->paginate(10);
                break;
            case 'vehicle-checklist':
                $query = DailyVehicleChecklist::with('user');
                $this->applyFilters($query, $request);
                if ($request->document_status) {
                    if ($request->document_status == 'uploaded') {
                        $query->whereNotNull('documents');
                    } elseif ($request->document_status == 'not_uploaded') {
                        $query->whereNull('documents');
                    }
                }
                $data['checklists'] = $query->latest()->paginate(10);
                break;
        }

        return view('admin.unified-reports.index', compact('users', 'type', 'data'));
    }

    private function applyFilters($query, $request)
    {
        if ($request->user_id) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->date_range) {
            $range = $request->date_range;
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
}

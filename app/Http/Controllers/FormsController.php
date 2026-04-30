<?php

namespace App\Http\Controllers;

use App\Models\Assessment;
use App\Models\DailyVehicleChecklist;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class FormsController extends Controller
{
    public function assessments(Request $request)
    {
        $query = Assessment::with('user');

        if ($request->user_id) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->date_range) {
            $this->applyDateFilter($query, $request->date_range);
        }

        $assessments = $query->latest()->get();
        $users = User::all();

        return view('admin.forms.assessments', compact('assessments', 'users'));
    }

    public function dailyVehicleChecklist(Request $request)
    {
        $query = DailyVehicleChecklist::with('user');

        if ($request->user_id) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->date_range) {
            $this->applyDateFilter($query, $request->date_range);
        }

        if ($request->document_status) {
            if ($request->document_status == 'uploaded') {
                $query->whereNotNull('documents');
            } elseif ($request->document_status == 'not_uploaded') {
                $query->whereNull('documents');
            }
        }

        $checklists = $query->latest()->get();
        $users = User::all();

        return view('admin.forms.daily-vehicle-checklist', compact('checklists', 'users'));
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

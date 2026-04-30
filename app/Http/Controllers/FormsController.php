<?php

namespace App\Http\Controllers;

use App\Models\Assessment;
use App\Models\DailyVehicleChecklist;
use Illuminate\Http\Request;

class FormsController extends Controller
{
    public function assessments()
    {
        $assessments = Assessment::with('user')->latest()->get();
        return view('admin.forms.assessments', compact('assessments'));
    }

    public function dailyVehicleChecklist()
    {
        $checklists = DailyVehicleChecklist::with('user')->latest()->get();
        return view('admin.forms.daily-vehicle-checklist', compact('checklists'));
    }
}

<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Master\Tenant;

class MasterDashboardController extends Controller
{
    public function index()
    {
        $totalTenants  = Tenant::on('master')->count();
        $activeTenants = Tenant::on('master')->where('is_active', true)->count();
        $recentTenants = Tenant::on('master')->latest()->take(5)->get();

        return view('master.dashboard', compact('totalTenants', 'activeTenants', 'recentTenants'));
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Repositories\PaySlipRepository;
use Illuminate\Http\Request;

class PaySlipController extends Controller
{
    protected $paySlipRepo;

    public function __construct(PaySlipRepository $paySlipRepo)
    {
        $this->paySlipRepo = $paySlipRepo;
    }

    public function index(Request $request)
    {
        $paySlips = $this->paySlipRepo->getPaySlips($request)['paySlips'];
        $users = User::where('role', 'Employee')->orderBy('name')->get();
        
        return view('admin.pay-slips.index', compact('paySlips', 'users'));
    }
}

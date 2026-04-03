<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\Request;

class CompanyApiController extends Controller
{
    public function index()
    {
        $companies = Company::orderBy('id', 'desc')->get();
        return response()->json([
            'status' => true,
            'message' => 'Companies retrieved successfully',
            'data' => $companies
        ]);
    }
}

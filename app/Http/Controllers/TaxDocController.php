<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TaxDocController extends Controller
{
    public function index()
    {
        return view('admin.tax-docs.index');
    }
}

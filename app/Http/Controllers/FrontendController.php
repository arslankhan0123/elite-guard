<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FrontendController extends Controller
{
    public function privacyPolicy()
    {
        return view('frontend.privacy-policy.index');
    }

    public function termsConditions()
    {
        return view('frontend.terms-conditions.index');
    }
}

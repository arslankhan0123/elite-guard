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

    public function securityProtocol()
    {
        return view('frontend.security-protocol.index');
    }

    public function careerPortal()
    {
        return view('frontend.career-portal.index');
    }

    public function operationalFaq()
    {
        return view('frontend.operational-faq.index');
    }

    public function about()
    {
        return view('frontend.about.index');
    }

    public function services()
    {
        return view('frontend.services.index');
    }

    public function contact()
    {
        return view('frontend.contact.index');
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Site;
use Illuminate\Http\Request;

class SiteApiController extends Controller
{
    public function index()
    {
        $sites = Site::with('nfcTags')->orderBy('id', 'desc')->get();
        return response()->json([
            'status' => true,
            'message' => 'Sites retrieved successfully',
            'data' => $sites
        ]);
    }
}

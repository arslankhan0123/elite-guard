<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\NfcTag;
use Illuminate\Http\Request;

class TagsApiController extends Controller
{
    public function index()
    {
        $tags = NfcTag::with('site')->orderBy('id', 'desc')->get();
        return response()->json([
            'status' => true,
            'message' => 'Tags retrieved successfully',
            'data' => $tags
        ]);
    }
}

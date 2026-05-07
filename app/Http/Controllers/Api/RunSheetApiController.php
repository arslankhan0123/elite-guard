<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Repositories\RunSheetRepository;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RunSheetApiController extends Controller
{
    use ApiResponser;

    protected $runSheetRepo;

    public function __construct(RunSheetRepository $runSheetRepo)
    {
        $this->runSheetRepo = $runSheetRepo;
    }

    /**
     * Get run sheets for the authenticated user.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $date = $request->query('date'); // Optional date filter
        
        $data = $this->runSheetRepo->getUserRunSheets($user, $date);

        return $this->successResponse($data, 'Run sheets fetched successfully.');
    }
}

<?php

namespace App\Http\Controllers;

use App\Repositories\ReportRepository;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    protected $reportRepo;

    public function __construct(ReportRepository $reportRepo)
    {
        $this->reportRepo = $reportRepo;
    }

    public function index(Request $request)
    {
        $type        = $request->get('type', 'companies');
        $date_filter = $request->get('date_filter', '');

        $results = $this->reportRepo->getReport($type, $date_filter);

        return view('admin.reports.index', compact('results', 'type', 'date_filter'));
    }
}

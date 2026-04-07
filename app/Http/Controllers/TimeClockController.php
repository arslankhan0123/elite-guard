<?php

namespace App\Http\Controllers;

use App\Repositories\NfcTagsRepository;
use App\Repositories\TimeClockRepository;
use Illuminate\Http\Request;

class TimeClockController extends Controller
{
    protected $timeClockRepo;

    // Inject the repository via constructor
    public function __construct(TimeClockRepository $timeClockRepo)
    {
        $this->timeClockRepo = $timeClockRepo;
    }

    public function index()
    {
        $checkPoints = $this->timeClockRepo->getCheckPoints();
        return view('admin.timeClocks.index', compact('checkPoints'));
    }
}

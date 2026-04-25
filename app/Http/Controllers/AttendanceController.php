<?php

namespace App\Http\Controllers;

use App\Models\Site;
use App\Models\User;
use App\Repositories\AttendanceRepository;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    protected $attendanceRepo;

    public function __construct(AttendanceRepository $attendanceRepo)
    {
        $this->attendanceRepo = $attendanceRepo;
    }

    public function index(Request $request)
    {
        $attendances = $this->attendanceRepo->getAttendanceReport($request);
        $users = User::where('role', '!=', 'admin')->orderBy('name')->get();
        $sites = Site::orderBy('name')->get();

        return view('admin.attendance.index', compact('attendances', 'users', 'sites'));
    }
}

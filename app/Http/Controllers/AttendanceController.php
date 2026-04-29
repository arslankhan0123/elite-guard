<?php

namespace App\Http\Controllers;

use App\Models\Site;
use App\Models\User;
use App\Models\ShiftAttendance;
use App\Repositories\AttendanceRepository;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AttendanceExport;

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

    public function exportPdf(Request $request)
    {
        $ids = explode(',', $request->attendance_ids);
        $attendances = ShiftAttendance::with(['user', 'shift.site.company'])
            ->whereIn('id', $ids)
            ->orderBy('clock_in_at', 'desc')
            ->get();

        $pdf = Pdf::loadView('admin.attendance.pdf', compact('attendances'));
        return $pdf->download('attendance_report_' . now()->format('Y-m-d_H-i') . '.pdf');
    }

    public function exportExcel(Request $request)
    {
        $ids = explode(',', $request->attendance_ids);
        return Excel::download(new AttendanceExport($ids), 'attendance_report_' . now()->format('Y-m-d_H-i') . '.xlsx');
    }

    public function updateAdjustment(Request $request)
    {
        $request->validate([
            'attendance_id' => 'required|exists:shift_attendance,id',
            'manual_adjustment' => 'required|integer',
            'admin_note' => 'nullable|string',
        ]);

        $attendance = ShiftAttendance::findOrFail($request->attendance_id);
        $attendance->update([
            'manual_adjustment' => $request->manual_adjustment,
            'admin_note' => $request->admin_note
        ]);

        return back()->with('success', 'Attendance adjustment updated successfully.');
    }
}

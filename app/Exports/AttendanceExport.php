<?php

namespace App\Exports;

use App\Models\ShiftAttendance;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class AttendanceExport implements FromCollection, WithHeadings, WithMapping
{
    protected $ids;

    public function __construct(array $ids)
    {
        $this->ids = $ids;
    }

    public function collection()
    {
        return ShiftAttendance::with(['user', 'shift.site.company'])
            ->whereIn('id', $this->ids)
            ->orderBy('clock_in_at', 'desc')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Employee Name',
            'Employee Email',
            'Site',
            'Company',
            'Shift Name',
            'Clock In',
            'Clock Out',
            'Duration',
            'Status'
        ];
    }

    public function map($attendance): array
    {
        $duration = '-';
        if ($attendance->clock_in_at && $attendance->clock_out_at) {
            $diff = $attendance->clock_in_at->diff($attendance->clock_out_at);
            $duration = $diff->format('%hh %im');
        }

        return [
            $attendance->user->name,
            $attendance->user->email,
            $attendance->shift->site->name ?? 'N/A',
            $attendance->shift->site->company->name ?? 'N/A',
            $attendance->shift->shift_name ?? 'N/A',
            $attendance->clock_in_at ? $attendance->clock_in_at->format('Y-m-d H:i') : 'N/A',
            $attendance->clock_out_at ? $attendance->clock_out_at->format('Y-m-d H:i') : '-',
            $duration,
            $attendance->status == 'active' ? 'Clocked In' : 'Completed'
        ];
    }
}

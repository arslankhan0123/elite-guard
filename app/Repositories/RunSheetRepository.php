<?php

namespace App\Repositories;

use App\Models\RunSheet;
use App\Models\RunSheetScan;
use Carbon\Carbon;

class RunSheetRepository
{
    /**
     * Get run sheets assigned to a specific user.
     * Optionally filter by date.
     */
    public function getUserRunSheets($user, $date = null)
    {
        $query = RunSheet::with('site.nfcTags', 'site.company', 'scan')
            ->where('user_id', $user->id);

        if ($date) {
            $query->where('date', $date);
        } else {
            // Default to today's date if not provided
            $query->where('date', Carbon::today()->format('Y-m-d'));
        }

        $query->orderBy('start_time', 'asc');

        $runSheets = $query->get();

        return [
            'status' => true,
            'message' => 'Run sheets retrieved successfully',
            'total_run_sheets' => $runSheets->count(),
            'run_sheets' => $runSheets
        ];
    }

    /**
     * Get run sheets for the authenticated user for today.
     */
    public function getTodayRunSheets($user)
    {
        return $this->getUserRunSheets($user, Carbon::today()->format('Y-m-d'));
    }

    /**
     * Store a new scan record.
     */
    public function storeScan($data)
    {
        $runsheet = RunSheetScan::create([
            'run_sheet_id' => $data['run_sheet_id'],
            'nfc_tag_id' => $data['nfc_tag_id'],
            'user_id' => $data['user_id'],
            'date' => Carbon::now()->format('Y-m-d'),
            'time' => Carbon::now()->format('H:i:s'),
            'latitude' => $data['latitude'] ?? null,
            'longitude' => $data['longitude'] ?? null,
        ]);

        return [
            'status' => true,
            'message' => 'Scan recorded successfully',
            'runsheet' => $runsheet
        ];
    }

    /**
     * Check if a scan already exists for this run sheet.
     */
    public function isAlreadyScanned($data)
    {
        return RunSheetScan::where('run_sheet_id', $data['run_sheet_id'])->exists();
    }
}

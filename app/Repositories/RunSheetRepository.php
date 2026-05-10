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
        $query = RunSheet::with('site.nfcTags', 'site.company')
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
     * Check if the NFC tag has already been scanned for this run sheet today by the user.
     */
    public function isAlreadyScanned($data)
    {
        return RunSheetScan::where('user_id', $data['user_id'])
            ->where('run_sheet_id', $data['run_sheet_id'])
            ->where('nfc_tag_id', $data['nfc_tag_id'])
            ->where('date', Carbon::now()->format('Y-m-d'))
            ->exists();
    }
}

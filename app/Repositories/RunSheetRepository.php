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
    public function getUserRunSheets($user, $date = null, $shiftId = null)
    {
        $query = RunSheet::with('site.nfcTags', 'site.company', 'scans', 'shift.weeklyRunSheet', 'weeklyRunSheetEntry')
            ->where('user_id', $user->id);

        if ($shiftId) {
            $query->where(function ($q) use ($shiftId, $date) {
                $q->where('shift_id', $shiftId);
                if ($date) {
                    $q->orWhere(function ($sq) use ($date) {
                        $sq->whereNull('shift_id')->where('date', $date);
                    });
                }
            });
        } elseif ($date) {
            $query->where('date', $date);
        } else {
            // Default to today's date if not provided
            $query->where('date', Carbon::today()->format('Y-m-d'));
        }

        $query->orderByRaw('CAST(sequence AS UNSIGNED) ASC')->orderBy('start_time', 'asc');

        $runSheets = $query->get();

        $totalTags = $runSheets->sum(function ($runSheet) {
            return $runSheet->site?->nfcTags?->count() ?? 0;
        });

        $totalScannedTags = $runSheets->sum(function ($runSheet) {
            return $runSheet->scans?->count() ?? 0;
        });

        $totalEntries = $runSheets->count();
        $scannedEntries = 0;

        $runSheetsData = $runSheets->map(function ($runSheet) use (&$scannedEntries) {
            $scannedTagIds = $runSheet->scans ? $runSheet->scans->pluck('nfc_tag_id')->map(fn($id) => (int)$id)->toArray() : [];
            $sheetArray = $runSheet->toArray();

            if (isset($sheetArray['site']['nfc_tags'])) {
                foreach ($sheetArray['site']['nfc_tags'] as &$tag) {
                    $tag['scanned'] = in_array((int)$tag['id'], $scannedTagIds);
                }
            }

            if (isset($sheetArray['site']['nfcTags'])) {
                foreach ($sheetArray['site']['nfcTags'] as &$tag) {
                    $tag['scanned'] = in_array((int)$tag['id'], $scannedTagIds);
                }
            }

            $tags = $sheetArray['site']['nfc_tags'] ?? $sheetArray['site']['nfcTags'] ?? [];
            $isScanned = count($scannedTagIds) > 0;
            if ($isScanned) {
                $scannedEntries++;
            }
            $sheetArray['is_scanned'] = $isScanned;
            $sheetArray['total_tags'] = count($tags);
            $sheetArray['scanned_tags_count'] = count($scannedTagIds);

            $mainRoute = $runSheet->shift?->weeklyRunSheet;
            $sheetArray['weekly_run_sheet'] = $mainRoute;
            $sheetArray['main_route'] = $mainRoute;

            return $sheetArray;
        });

        return [
            'status' => true,
            'message' => 'Run sheets retrieved successfully',
            'total_run_sheets' => $runSheets->count(),
            'total_entries' => $totalEntries,
            'scanned_entries' => $scannedEntries,
            'total_tags' => $totalTags,
            'total_scanned_tags' => $totalScannedTags,
            'run_sheets' => $runSheetsData
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
        $scanDate = $data['date'] ?? Carbon::now()->format('Y-m-d');
        $scanTime = $data['time'] ?? Carbon::now()->format('H:i:s');

        $runsheet = RunSheetScan::create([
            'run_sheet_id' => $data['run_sheet_id'],
            'nfc_tag_id'   => $data['nfc_tag_id'],
            'user_id'      => $data['user_id'],
            'date'         => $scanDate,
            'time'         => $scanTime,
            'latitude'     => $data['latitude'] ?? null,
            'longitude'    => $data['longitude'] ?? null,
            'image'        => $data['image'] ?? null,
            'reason'       => $data['reason'] ?? null,
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
        $scanDate = isset($data['date']) ? Carbon::parse($data['date']) : Carbon::today();
        $dates = [
            $scanDate->toDateString(),
            $scanDate->copy()->subDay()->toDateString(),
            $scanDate->copy()->addDay()->toDateString(),
        ];

        return RunSheetScan::where('user_id', $data['user_id'])
            ->where('run_sheet_id', $data['run_sheet_id'])
            ->where('nfc_tag_id', $data['nfc_tag_id'])
            ->whereIn('date', $dates)
            ->exists();
    }
}

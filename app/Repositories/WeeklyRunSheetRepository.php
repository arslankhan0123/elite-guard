<?php

namespace App\Repositories;

use App\Models\WeeklyRunSheet;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class WeeklyRunSheetRepository
{
    public function getAllWeeklyRunSheets(): array
    {
        $runSheets = WeeklyRunSheet::with([
            'entries.site.company',
            'entries.site.nfcTags',
        ])
            ->orderByDesc('week_start_date')
            ->get();

        return [
            'status' => true,
            'message' => 'Weekly runsheets retrieved successfully',
            'run_sheets' => $runSheets,
        ];
    }

    public function getUserAssignedAllWeeklyRunSheets(User $user): array
    {
        $runSheets = $user->weeklyRunSheets()
            ->with([
                'entries.site.company',
                'entries.site.nfcTags',
            ])
            ->orderByDesc('weekly_run_sheets.week_start_date')
            ->get();

        return [
            'status' => true,
            'message' => 'Assigned weekly runsheets retrieved successfully',
            'total_run_sheets' => $runSheets->count(),
            'run_sheets' => $runSheets,
        ];
    }

    public function getUserAssignedWeeklyRunSheets(User $user, ?string $date = null, ?int $weeklyRunSheetId = null): array
    {
        $today = $date ? Carbon::parse($date) : Carbon::now(config('app.timezone', 'UTC'));
        $dayOfWeek = $today->dayOfWeekIso;
        $dateStr = $today->toDateString();

        $query = $user->weeklyRunSheets();
        if ($weeklyRunSheetId !== null) {
            $query->where('weekly_run_sheets.id', $weeklyRunSheetId);
        }

        $runSheets = $query
            ->whereHas('entries', fn ($query) => $query->where('day_of_week', $dayOfWeek))
            ->with(['entries' => fn ($query) => $query
                ->where('day_of_week', $dayOfWeek)
                ->with([
                    'site.company', 
                    'site.nfcTags',
                    'scans' => fn ($q) => $q->where('date', $dateStr)
                ])
                ->orderBy('sequence')
                ->orderBy('start_time')])
            ->orderBy('weekly_run_sheets.name')
            ->get();

        $runSheetsData = $runSheets->map(function ($runSheet) {
            $sheetArray = $runSheet->toArray();
            
            if (isset($sheetArray['entries'])) {
                foreach ($sheetArray['entries'] as &$entry) {
                    $scannedTagIds = collect($entry['scans'] ?? [])
                        ->pluck('nfc_tag_id')
                        ->map(fn($id) => (int)$id)
                        ->toArray();
                    
                    if (isset($entry['site']['nfc_tags'])) {
                        foreach ($entry['site']['nfc_tags'] as &$tag) {
                            $tag['scanned'] = in_array((int)$tag['id'], $scannedTagIds);
                        }
                    }

                    if (isset($entry['site']['nfcTags'])) {
                        foreach ($entry['site']['nfcTags'] as &$tag) {
                            $tag['scanned'] = in_array((int)$tag['id'], $scannedTagIds);
                        }
                    }

                    $entry['is_scanned'] = count($entry['scans'] ?? []) > 0;
                    $tags = $entry['site']['nfc_tags'] ?? $entry['site']['nfcTags'] ?? [];
                    $entry['total_tags'] = count($tags);
                    $entry['scanned_tags_count'] = count($scannedTagIds);
                }
            }
            return $sheetArray;
        });

        return [
            'status' => true,
            'message' => 'Today assigned weekly runsheets retrieved successfully',
            'date' => $dateStr,
            'day' => $today->format('l'),
            'total_run_sheets' => $runSheets->count(),
            'run_sheets' => $runSheetsData,
        ];
    }

    public function storeScan(array $data)
    {
        Log::info('Storing scan data: ', $data);
        $scan = \App\Models\WeeklyRunSheetScan::create($data);
        return [
            'status' => true,
            'message' => 'Scan recorded successfully',
            'scan' => $scan
        ];
    }

    public function isAlreadyScanned(array $data)
    {
        return \App\Models\WeeklyRunSheetScan::where('user_id', $data['user_id'])
            ->where('weekly_run_sheet_entry_id', $data['weekly_run_sheet_entry_id'])
            ->where('nfc_tag_id', $data['nfc_tag_id'])
            ->where('date', $data['date'])
            ->exists();
    }
}

<?php

namespace App\Traits;

use App\Models\Shift;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Auth;
use App\Models\Site;
use App\Models\SiteTour;
use App\Models\SiteTourItem;
use App\Models\User;
use Carbon\Carbon;

trait CommonTrait
{
    /**
     * Delete all physically stored documents associated with a user (Employee).
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function DeleteEmployeeDocuments($user)
    {
        if (!$user) {
            return;
        }

        // 1. Collect all document URLs to delete physical files
        $allFiles = [];
        
        // Load relations if not loaded
        if (!$user->relationLoaded('bankDetail')) {
            $user->load('bankDetail');
        }
        if (!$user->relationLoaded('licenseDetail')) {
            $user->load('licenseDetail');
        }

        if ($user->bankDetail) {
            $files = $user->bankDetail->void_cheque_file;
            if ($files) {
                $allFiles = array_merge($allFiles, is_array($files) ? $files : [$files]);
            }
        }
        
        if ($user->licenseDetail) {
            $fields = ['security_license_file', 'drivers_license_file', 'work_eligibility_file', 'other_documents_file'];
            foreach ($fields as $field) {
                $files = $user->licenseDetail->$field;
                if ($files) {
                    $allFiles = array_merge($allFiles, is_array($files) ? $files : [$files]);
                }
            }
        }
        
        if (!$user->relationLoaded('paySlips')) {
            $user->load('paySlips');
        }
        foreach ($user->paySlips as $paySlip) {
            $allFiles[] = $paySlip->file_path;
        }
        
        // 2. Delete physical files from public/documents/
        $baseUrl = rtrim(config('app.url'), '/');
        foreach ($allFiles as $fileUrl) {
            // Strip the base URL to get the relative path
            $relativePath = str_replace($baseUrl . '/', '', $fileUrl);
            // Ensure no leading slash remains after replacement
            $relativePath = ltrim($relativePath, '/');
            $fullPath = public_path($relativePath);
            
            if (File::exists($fullPath)) {
                File::delete($fullPath);
            }
        }
    }

    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371000; // in meters

        $latDelta = deg2rad($lat2 - $lat1);
        $lonDelta = deg2rad($lon2 - $lon1);

        $a = sin($latDelta / 2) * sin($latDelta / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($lonDelta / 2) * sin($lonDelta / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

    public function getUserShifts()
    {
        $userId = Auth::id();
        $user = User::find($userId);
        $user->systemId = 'EG-'.$user->id;

        $startOfWeek = Carbon::now()->startOfWeek(Carbon::MONDAY)->toDateString();
        $endOfWeek = Carbon::now()->endOfWeek(Carbon::SUNDAY)->toDateString();

        $shifts = \App\Models\Shift::whereHas('schedule', function ($query) use ($userId) {
                $query->where('user_id', $userId);
            })
            ->whereBetween('date', [$startOfWeek, $endOfWeek])->get();
        
        return $shifts;
    }

    public function getUserSites()
    {
        $userId = Auth::id();
        $user = User::find($userId);
        $user->systemId = 'EG-'.$user->id;

        $sites = $user->sites()->get();
        
        return $sites;
    }

    private function syncSiteTourForShift(Shift $shift, int $userId): void
    {
        $site = $shift->site;

        if (!$site || !$shift->date) {
            return;
        }

        $interval = (int) $site->interval;
        $tour = SiteTour::updateOrCreate(
            ['shift_id' => $shift->id],
            [
                'site_id' => $shift->site_id,
                'user_id' => $userId,
                'name' => $shift->shift_name ?: 'Regular Shift',
                'description' => 'This is the site tour for shift: ' . ($shift->shift_name ?: 'Regular Shift') . '.',
                'scheduled_days' => [Carbon::parse($shift->date)->format('l')],
                'is_continuous' => false,
                'schedule_type' => 'Repeat',
                'specific_times' => [],
                'max_duration' => [],
                'tag_type' => 'nfc',
                'tags' => $site->nfcTags->pluck('id')->values()->all(),
                'assigned_guards' => [$userId],
                'interval' => $interval,
                'open_time' => (int) $site->open_time,
                'grace_time' => (int) $site->grace_time,
            ]
        );

        $intendedItems = [];

        if ($interval > 0 && $shift->start_time && $shift->end_time) {
            $startTime = Carbon::parse($shift->date . ' ' . $shift->start_time);
            $endTime = Carbon::parse($shift->date . ' ' . $shift->end_time);

            if ($endTime->lt($startTime)) {
                $endTime->addDay();
            }

            $itemNumber = 1;

            while ($startTime->copy()->addMinutes(($itemNumber - 1) * $interval)->lt($endTime)) {
                $itemStart = $startTime->copy()->addMinutes(($itemNumber - 1) * $interval);

                if ($itemNumber > 1) {
                    $itemStart->addMinute();
                }

                $itemEnd = $startTime->copy()->addMinutes($itemNumber * $interval);

                if ($itemEnd->gt($endTime)) {
                    $itemEnd = $endTime->copy();
                }

                if ($itemStart->lt($itemEnd)) {
                    $key = $itemStart->format('H:i:s') . '|' . $itemEnd->format('H:i:s');
                    $intendedItems[$key] = [
                        'site_tour_id' => $tour->id,
                        'user_id' => $userId,
                        'site_id' => $shift->site_id,
                        'type' => null,
                        'status' => false,
                        'date' => $itemStart->format('Y-m-d'),
                        'start_time' => $itemStart->format('H:i:s'),
                        'end_time' => $itemEnd->format('H:i:s'),
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }

                $itemNumber++;
            }
        }

        foreach ($tour->items()->get() as $existingItem) {
            $key = Carbon::parse($existingItem->start_time)->format('H:i:s')
                . '|' . Carbon::parse($existingItem->end_time)->format('H:i:s');

            if (
                isset($intendedItems[$key])
                && $existingItem->date?->format('Y-m-d') === $intendedItems[$key]['date']
                && (int) $existingItem->site_id === (int) $shift->site_id
            ) {
                unset($intendedItems[$key]);
            } else {
                $existingItem->delete();
            }
        }

        if ($intendedItems) {
            SiteTourItem::insert(array_values($intendedItems));
        }
    }
}

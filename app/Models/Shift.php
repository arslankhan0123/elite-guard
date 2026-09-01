<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shift extends Model
{
    use HasFactory;

    protected $fillable = [
        'schedule_id',
        'site_id',
        'type',
        'weekly_run_sheet_id',
        'date',
        'shift_name',
        'start_time',
        'end_time',
    ];

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($shift) {
            $shift->deleteAssociatedScansAndTours();
        });
    }

    /**
     * Clean up site tours, site tour items, scans, images, runsheet scans, and attendances for this shift.
     */
    public function deleteAssociatedScansAndTours(): void
    {
        $userId = $this->schedule?->user_id;

        // 1. Delete site tours linked by shift_id
        $siteTours = SiteTour::where('shift_id', $this->id)->get();
        foreach ($siteTours as $siteTour) {
            $siteTour->delete();
        }

        // 2. Delete standalone SiteTourItems matching user_id, site_id, and date
        if ($userId && $this->site_id && $this->date) {
            $extraItems = SiteTourItem::where('user_id', $userId)
                ->where('site_id', $this->site_id)
                ->where('date', $this->date)
                ->get();

            foreach ($extraItems as $item) {
                $item->delete();
            }
        }

        // 3. Delete WeeklyRunSheetScans (and images) for this shift/date/user
        if ($this->type === 'runsheet' || $this->weekly_run_sheet_id) {
            $runSheetScansQuery = WeeklyRunSheetScan::query();
            if ($this->weekly_run_sheet_id) {
                $runSheetScansQuery->where('weekly_run_sheet_id', $this->weekly_run_sheet_id);
            }
            if ($userId) {
                $runSheetScansQuery->where('user_id', $userId);
            }
            if ($this->date) {
                $runSheetScansQuery->where('date', $this->date);
            }

            $runSheetScans = $runSheetScansQuery->get();
            foreach ($runSheetScans as $rsScan) {
                self::deleteStorageFile($rsScan->image);
                $rsScan->delete();
            }
        }

        // 4. Delete RunSheet & RunSheetScan records matching user_id, site_id, and date
        if ($userId && $this->site_id && $this->date) {
            $runSheets = RunSheet::where('user_id', $userId)
                ->where('site_id', $this->site_id)
                ->where('date', $this->date)
                ->get();

            foreach ($runSheets as $rs) {
                foreach ($rs->scans as $scan) {
                    $scan->delete();
                }
                $rs->delete();
            }
        }

        // 5. Delete SiteScan / SiteItemScan records matching user_id, site_id, and date
        if ($userId && $this->site_id && $this->date) {
            $siteItemScans = SiteItemScan::where('user_id', $userId)
                ->where('site_id', $this->site_id)
                ->where('date', $this->date)
                ->get();

            foreach ($siteItemScans as $sis) {
                self::deleteStorageFile($sis->image);
                $sis->delete();
            }

            $siteScans = SiteScan::where('user_id', $userId)
                ->where('site_id', $this->site_id)
                ->where('date', $this->date)
                ->get();

            foreach ($siteScans as $ss) {
                self::deleteStorageFile($ss->image);
                $ss->delete();
            }
        }

        // 6. Delete ShiftAttendance records for this shift
        $this->attendances()->delete();
    }

    /**
     * Helper to delete a file from public disk storage
     */
    public static function deleteStorageFile(?string $urlOrPath): void
    {
        if (empty($urlOrPath)) {
            return;
        }

        $parsedPath = parse_url($urlOrPath, PHP_URL_PATH);
        if ($parsedPath) {
            $relativePath = ltrim(str_replace('/storage/', '', $parsedPath), '/');
            if ($relativePath && \Illuminate\Support\Facades\Storage::disk('public')->exists($relativePath)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($relativePath);
            }
        }
    }

    /**
     * Get the schedule that owns the shift.
     */
    public function schedule()
    {
        return $this->belongsTo(Schedule::class);
    }

    /**
     * Get the site assigned in this shift.
     */
    public function site()
    {
        return $this->belongsTo(Site::class);
    }

    /**
     * Get the weekly runsheet assigned in this shift.
     */
    public function weeklyRunSheet()
    {
        return $this->belongsTo(WeeklyRunSheet::class, 'weekly_run_sheet_id');
    }

    /**
     * Get all attendance records for this shift.
     */
    public function attendances()
    {
        return $this->hasMany(ShiftAttendance::class);
    }
}

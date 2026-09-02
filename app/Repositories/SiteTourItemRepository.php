<?php

namespace App\Repositories;

use App\Models\SiteTourItem;
use App\Models\SiteTourItemScan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class SiteTourItemRepository
{
    // Get site tour items assigned to a specific user
    public function getUserSiteTourItems($user, $start_date = null, $end_date = null, $shift_id = null)
    {
        $query = SiteTourItem::where('user_id', $user->id)
            ->with(['site.nfcTags', 'siteTour.shift']);
            
        // If a specific shift_id is provided, filter strictly by that shift.
        // This prevents overnight-shift tours from leaking into the next shift's results.
        if ($shift_id) {
            $query->whereHas('siteTour', function ($q) use ($shift_id) {
                $q->where('shift_id', $shift_id);
            });

            // Still respect date bounds if provided, for extra safety
            if ($start_date && $end_date) {
                // For overnight shifts the item date may be start_date - 1 or end_date + 1, so widen by 1 day
                $rangeStart = \Carbon\Carbon::parse($start_date)->subDay()->toDateString();
                $rangeEnd   = \Carbon\Carbon::parse($end_date)->addDay()->toDateString();
                $query->whereBetween('date', [$rangeStart, $rangeEnd]);
            }
        } else {
            // Fallback: date-based filtering with overnight shift support
            if ($start_date && $end_date) {
                $yesterday = \Carbon\Carbon::parse($start_date)->subDay()->toDateString();
                $query->where(function ($q) use ($start_date, $end_date, $yesterday) {
                    $q->whereBetween('date', [$start_date, $end_date])
                      ->orWhere(function ($sub) use ($yesterday) {
                          $sub->where('date', $yesterday)
                              ->whereHas('siteTour.shift', function ($shiftQuery) {
                                  $shiftQuery->whereColumn('end_time', '<', 'start_time');
                              });
                      });
                });
            } elseif ($start_date) {
                $yesterday = \Carbon\Carbon::parse($start_date)->subDay()->toDateString();
                $query->where(function ($q) use ($start_date, $yesterday) {
                    $q->where('date', '>=', $start_date)
                      ->orWhere(function ($sub) use ($yesterday) {
                          $sub->where('date', $yesterday)
                              ->whereHas('siteTour.shift', function ($shiftQuery) {
                                  $shiftQuery->whereColumn('end_time', '<', 'start_time');
                              });
                      });
                });
            } elseif ($end_date) {
                $query->where('date', '<=', $end_date);
            }
        }

        $items = $query->orderBy('date', 'asc')
            ->orderBy('start_time', 'asc')
            ->get();
            
        $itemIds = $items->pluck('id')->toArray();
        $scans = \App\Models\SiteTourItemScan::whereIn('site_tour_item_id', $itemIds)
            ->where('user_id', $user->id)
            ->get()
            ->groupBy('site_tour_item_id');

        foreach ($items as $item) {
            // Calculate new start and end times based on siteTour's open_time and grace_time
            $openTime = 0;
            $graceTime = 0;
            if ($item->siteTour) {
                $openTime = (int) ($item->siteTour->open_time ?? 0);
                $graceTime = (int) ($item->siteTour->grace_time ?? 0);
            }

            if ($item->start_time) {
                $startTimeObj = \Carbon\Carbon::parse($item->start_time);
                $newStart = $startTimeObj->copy()->subMinutes($openTime)->format('H:i:s');
                $newEnd = $startTimeObj->copy()->addMinutes($graceTime)->format('H:i:s');

                $item->setAttribute('new_start_time', $newStart);
                $item->setAttribute('new_end_time', $newEnd);
            } else {
                $item->setAttribute('new_start_time', null);
                $item->setAttribute('new_end_time', null);
            }

            $totalTags = 0;
            $scannedTagsCount = 0;

            if ($item->site && $item->site->relationLoaded('nfcTags')) {
                $itemScans = isset($scans[$item->id]) ? $scans[$item->id]->keyBy('nfc_tag_id') : collect();
                
                $totalTags = $item->site->nfcTags->count();
                $scannedTagsCount = $itemScans->count();

                // Clone the site and tags so they don't overwrite each other across different tour items
                $siteClone = clone $item->site;
                $nfcTagsClone = $siteClone->nfcTags->map(function ($tag) use ($itemScans) {
                    $clonedTag = clone $tag;
                    $clonedTag->setAttribute('is_scanned', $itemScans->has($clonedTag->id));
                    return $clonedTag;
                });
                
                $siteClone->setRelation('nfcTags', $nfcTagsClone);
                $item->setRelation('site', $siteClone);
            }

            $item->setAttribute('total_tags', $totalTags);
            $item->setAttribute('scanned_tags_count', $scannedTagsCount);
        }
        
        // Extract shift info once (all items share the same SiteTour -> Shift)
        $shiftInfo = null;
        $firstShift = $items->first()?->siteTour?->shift ?? null;
        if ($firstShift) {
            $shiftInfo = [
                'id'         => $firstShift->id,
                'shift_name' => $firstShift->shift_name,
                'start_time' => $firstShift->start_time,
                'end_time'   => $firstShift->end_time,
            ];
        }

        // Extract SiteTour info once (open_time & grace_time are same for all items)
        $siteTourInfo = null;
        $firstSiteTour = $items->first()?->siteTour ?? null;
        
        $totalItems = $items->count();
        $scannedItems = $items->filter(function ($item) {
            return $item->getAttribute('scanned_tags_count') > 0;
        })->count();

        if ($firstSiteTour) {
            $siteTourInfo = [
                'id'            => $firstSiteTour->id,
                'name'          => $firstSiteTour->name,
                'open_time'     => $firstSiteTour->open_time,
                'grace_time'    => $firstSiteTour->grace_time,
                'total_items'   => $totalItems,
                'scanned_items' => $scannedItems,
            ];
        }

        return [
            'status'                  => true,
            'message'                 => 'Assigned site tour items retrieved successfully',
            'shift'                   => $shiftInfo,
            'site_tour'               => $siteTourInfo,
            'total_site_tour_items'   => $totalItems,
            'scanned_site_tour_items' => $scannedItems,
            'site_tour_items'         => $items,
        ];
    }
    
    public function storeScan($request, $user)
    {
        $data = $request->only([
            'site_tour_item_id',
            'nfc_tag_id',
            'site_id',
            'date',
            'time',
        ]);
        
        $data['user_id'] = $user->id;

        $uploadedImages = [];

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('documents/SiteTourScans', 'public');
            $data['image'] = Storage::disk('public')->url($path);
        }

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                if ($file->isValid()) {
                    $path = $file->store('documents/SiteTourScans', 'public');
                    $uploadedImages[] = Storage::disk('public')->url($path);
                }
            }
        }

        $scan = DB::transaction(function () use ($data, $request, $uploadedImages) {
            $siteTourItem = SiteTourItem::findOrFail($data['site_tour_item_id']);
            if ($request->exists('reason')) {
                $siteTourItem->update([
                    'reason' => $request->input('reason'),
                ]);
            }

            $scanRecord = SiteTourItemScan::create($data);

            foreach ($uploadedImages as $imageUrl) {
                \App\Models\SiteTourItemImage::create([
                    'site_tour_item_id' => $data['site_tour_item_id'],
                    'image_path' => $imageUrl,
                ]);
            }

            return $scanRecord;
        });

        $scan->load('siteTourItem.images');

        return [
            'status' => true,
            'message' => 'NFC tag scanned and saved successfully',
            'data' => $scan
        ];
    }
}

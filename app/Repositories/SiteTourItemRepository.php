<?php

namespace App\Repositories;

use App\Models\SiteTourItem;

class SiteTourItemRepository
{
    // Get site tour items assigned to a specific user
    public function getUserSiteTourItems($user, $start_date = null, $end_date = null)
    {
        $query = SiteTourItem::where('user_id', $user->id)
            ->with(['site.nfcTags', 'siteTour']);
            
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

        $items = $query->orderBy('date', 'desc')
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
        
        return [
            'status' => true,
            'message' => 'Assigned site tour items retrieved successfully',
            'site_tour_items' => $items
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

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('documents/SiteTourScans', 'public');
            $data['image'] = \Illuminate\Support\Facades\Storage::disk('public')->url($path);
        }

        $scan = \App\Models\SiteTourItemScan::create($data);

        return [
            'status' => true,
            'message' => 'NFC tag scanned and saved successfully',
            'data' => $scan
        ];
    }
}

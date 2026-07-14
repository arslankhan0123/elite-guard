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
            $query->whereBetween('date', [$start_date, $end_date]);
        } elseif ($start_date) {
            $query->where('date', '>=', $start_date);
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
            if ($item->site && $item->site->relationLoaded('nfcTags')) {
                $itemScans = isset($scans[$item->id]) ? $scans[$item->id]->keyBy('nfc_tag_id') : collect();
                
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

        $scan = \App\Models\SiteTourItemScan::create($data);

        return [
            'status' => true,
            'message' => 'NFC tag scanned and saved successfully',
            'data' => $scan
        ];
    }
}

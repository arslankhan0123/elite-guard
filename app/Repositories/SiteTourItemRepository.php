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
        
        return [
            'status' => true,
            'message' => 'Assigned site tour items retrieved successfully',
            'site_tour_items' => $items
        ];
    }
}

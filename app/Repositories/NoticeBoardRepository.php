<?php

namespace App\Repositories;

use App\Models\NoticeBoard;

class NoticeBoardRepository
{
    /**
     * Get all notices
     */
    public function getNoticeBoard()
    {
        $notices = NoticeBoard::orderBy('date', 'desc')->get();
        return [
            'status' => true,
            'message' => 'Notice Board Data fetched successfully.',
            'notices' => $notices
        ];
    }
}

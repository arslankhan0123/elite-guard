<?php

namespace App\Repositories;

use App\Models\EmployeeOfferLetter;
use App\Models\Orientation;
use App\Models\SignedOrientation;
use App\Models\OrientationAttempt;
use Illuminate\Support\Facades\Auth;

class OfferLetterRepository
{
    /**
     * Get all orientations.
     */
    public function getUserOfferLetter()
    {
        $user = Auth::user();
        $offerLetter = EmployeeOfferLetter::where('user_id', $user->id)->first();
        return [
            'status' => true,
            'message' => 'Offer letter retrieved successfully',
            'offerLetter' => $offerLetter
        ];
    }
}

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

    public function acceptedOfferLetter($request)
    {
        $user = Auth::user();
        $offerLetter = EmployeeOfferLetter::where('user_id', $user->id)->first();

        if (!$offerLetter) {
            return [
                'status' => false,
                'message' => 'No offer letter found for the user',
                'offerLetter' => null
            ];
        }

        if ($offerLetter->is_accepted) {
            return [
                'status' => false,
                'message' => 'Offer letter has already been accepted',
                'offerLetter' => $offerLetter
            ];
        }

        $offerLetter->is_accepted = $request->input('is_accepted', true);
        $offerLetter->signed_at = now();
        $offerLetter->signature = $request->input('signature');
        $offerLetter->save();

        return [
            'status' => true,
            'message' => 'Offer letter signed successfully',
            'offerLetter' => $offerLetter
        ];
    }
}

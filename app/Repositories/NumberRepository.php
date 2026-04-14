<?php

namespace App\Repositories;

use App\Models\Number;

class NumberRepository
{
    /**
     * Get all numbers
     */
    public function getAllNumbers()
    {
        $numbers = Number::orderBy('id', 'desc')->get();
        return [
            'status' => true,
            'message' => 'Numbers retrieved successfully',
            'numbers' => $numbers
        ];
    }

    /**
     * Get Application numbers
     */
    public function getApplicationNumbers()
    {
        $numbers = Number::where('type', 'Application')->orderBy('id', 'desc')->get();
        return [
            'status' => true,
            'message' => 'Numbers retrieved successfully',
            'numbers' => $numbers
        ];
    }

    /**
     * Find a number by ID
     */
    public function findNumberById($id)
    {
        return Number::find($id);
    }

    /**
     * Create a new number
     */
    public function createNumber($request)
    {
        $data = $request->all();
        return Number::create($data);
    }

    /**
     * Update an existing number
     */
    public function updateNumber($request, $id)
    {
        $number = Number::find($id);
        if ($number) {
            $data = $request->all();
            $number->update($data);
            return $number;
        }
        return null;
    }

    /**
     * Delete a number
     */
    public function deleteNumber($id)
    {
        $number = Number::find($id);
        if ($number) {
            return $number->delete();
        }
        return false;
    }
}

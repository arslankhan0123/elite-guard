<?php

namespace App\Repositories;

use App\Models\Availability;
use Illuminate\Support\Facades\Auth;

class AvailabilityRepository
{
    /**
     * Get all availabilities for the authenticated user (API).
     */
    public function getUserAvailabilities($status = null)
    {
        $user = Auth::user();

        $query = Availability::where('user_id', $user->id)
            ->orderByRaw("CASE WHEN status = 'pending' THEN 0 ELSE 1 END")
            ->orderByDesc('date');

        if ($status) {
            $query->where('status', $status);
        }

        $availabilities = $query->get();

        $pendingCount = Availability::where('user_id', $user->id)
            ->where('status', 'pending')
            ->count();

        $approvedCount = Availability::where('user_id', $user->id)
            ->where('status', 'approved')
            ->count();

        $rejectedCount = Availability::where('user_id', $user->id)
            ->where('status', 'rejected')
            ->count();

        return [
            'status' => true,
            'message' => 'Availabilities retrieved successfully',
            'availabilities' => $availabilities,
            'pending_count' => $pendingCount,
            'approved_count' => $approvedCount,
            'rejected_count' => $rejectedCount,
        ];
    }

    /**
     * Create a new availability (API).
     */
    public function createAvailability(array $data)
    {
        $user = Auth::user();

        $availability = Availability::create([
            'user_id' => $user->id,
            'date' => $data['date'],
            'shift' => $data['shift'],
            'user_notes' => $data['user_notes'] ?? null,
            'status' => 'pending'
        ]);

        return [
            'status' => true,
            'message' => 'Availability submitted successfully',
            'availability' => $availability
        ];
    }

    /**
     * Update an availability (API).
     */
    public function updateAvailability($id, array $data)
    {
        $user = Auth::user();
        $availability = Availability::where('id', $id)
            ->where('user_id', $user->id)
            ->first();

        if (!$availability) {
            return [
                'status' => false,
                'message' => 'Availability not found or access denied.',
                'code' => 404
            ];
        }

        if ($availability->status !== 'pending') {
            return [
                'status' => false,
                'message' => 'Only pending availabilities can be updated.',
                'code' => 422
            ];
        }

        $availability->update([
            'date' => $data['date'] ?? $availability->date,
            'shift' => $data['shift'] ?? $availability->shift,
            'user_notes' => $data['user_notes'] ?? $availability->user_notes,
        ]);

        return [
            'status' => true,
            'message' => 'Availability updated successfully',
            'availability' => $availability
        ];
    }

    /**
     * Delete an availability (API).
     */
    public function deleteAvailability($id)
    {
        $user = Auth::user();
        $availability = Availability::where('id', $id)
            ->where('user_id', $user->id)
            ->first();

        if (!$availability) {
            return [
                'status' => false,
                'message' => 'Availability not found or access denied.',
                'code' => 404
            ];
        }

        if ($availability->status !== 'pending') {
            return [
                'status' => false,
                'message' => 'Only pending availabilities can be deleted.',
                'code' => 422
            ];
        }

        $availability->delete();

        return [
            'status' => true,
            'message' => 'Availability deleted successfully'
        ];
    }

    /**
     * Get all availabilities for Admin with optional filtering.
     */
    public function getAllForAdmin($filters = [])
    {
        $query = Availability::with('user')->orderByDesc('date');

        if (isset($filters['status']) && $filters['status'] !== 'all') {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        return $query->get();
    }

    /**
     * Update status and admin notes (Admin).
     */
    public function processAvailability($id, array $data)
    {
        $availability = Availability::findOrFail($id);

        $availability->update([
            'status' => $data['status'] ?? $availability->status,
            'admin_notes' => $data['admin_notes'] ?? $availability->admin_notes,
        ]);

        return $availability;
    }
}

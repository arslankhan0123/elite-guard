<?php

namespace App\Repositories;

use App\Models\Orientation;
use App\Models\SignedOrientation;
use Illuminate\Support\Facades\Auth;

class OrientationRepository
{
    /**
     * Get all orientations.
     */
    public function getAllOrientations()
    {
        $orientations = Orientation::orderBy('id', 'desc')->get();
        return [
            'status' => true,
            'message' => 'Orientations retrieved successfully',
            'orientations' => $orientations
        ];
    }

    /**
     * Find an orientation by ID.
     */
    public function findOrientationById($id)
    {
        return Orientation::findOrFail($id);
    }

    /**
     * Create a new orientation.
     */
    public function createOrientation($request)
    {
        $data = $request->all();
        if ($request->hasFile('document')) {
            $file = $request->file('document');
            $fileName = 'user_orientation_' . Auth::id() . '_' . time() . '_' . rand(1111, 9999) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('documents/orientations'), $fileName);
            $data['document'] = url('documents/orientations/' . $fileName);
        }
        return Orientation::create($data);
    }

    /**
     * Update an existing orientation.
     */
    public function updateOrientation($request, $id)
    {
        $orientation = $this->findOrientationById($id);
        $data = $request->all();

        if ($request->hasFile('document')) {
            // Delete old document if exists
            if ($orientation->document) {
                $oldPath = str_replace(url('/'), public_path(), $orientation->document);
                if (file_exists($oldPath)) {
                    @unlink($oldPath);
                }
            }
            $file = $request->file('document');
            $fileName = 'user_orientation_' . Auth::id() . '_' . time() . '_' . rand(1111, 9999) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('documents/orientations'), $fileName);
            $data['document'] = url('documents/orientations/' . $fileName);
        }

        $orientation->update($data);
        return $orientation;
    }

    /**
     * Delete an orientation.
     */
    public function deleteOrientation($id)
    {
        $orientation = $this->findOrientationById($id);
        if ($orientation->document) {
            $oldPath = str_replace(url('/'), public_path(), $orientation->document);
            if (file_exists($oldPath)) {
                @unlink($oldPath);
            }
        }
        return $orientation->delete();
    }

    /**
     * Store a signed orientation.
     */
    public function storeSignedOrientations($request)
    {
        $user_id = Auth::id();
        $orientation_id = $request->input('orientation_id');

        // Check for existing signature
        $exists = SignedOrientation::where('user_id', $user_id)
            ->where('orientation_id', $orientation_id)
            ->exists();

        if ($exists) {
            return [
                'status' => false,
                'message' => 'You have already signed this orientation.'
            ];
        }

        $data = [
            'user_id' => $user_id,
            'orientation_id' => $orientation_id,
            'agreed' => $request->input('agreed'),
            'signature' => $request->input('signature'),
        ];

        if ($request->hasFile('document')) {
            $file = $request->file('document');
            $fileName = 'user_' . $user_id . '_orientation_' . $orientation_id . '_' . time() . '_' . rand(1111, 9999) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('documents/signed_orientations'), $fileName);
            $data['document'] = url('documents/signed_orientations/' . $fileName);
        }

        $signedOrientation = SignedOrientation::create($data);

        return [
            'status' => true,
            'message' => 'Orientation signed successfully.',
            'signedOrientation' => $signedOrientation
        ];
    }

    /**
     * Get all signed orientations for admin view.
     */
    public function getAllSignedOrientations()
    {
        $signedOrientations = SignedOrientation::with(['user', 'orientation'])->orderBy('id', 'desc')->get();
        return [
            'status' => true,
            'message' => 'Orientation signatures retrieved successfully.',
            'signedOrientations' => $signedOrientations
        ];
    }
}

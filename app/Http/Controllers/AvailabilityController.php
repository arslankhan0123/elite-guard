<?php

namespace App\Http\Controllers;

use App\Repositories\AvailabilityRepository;
use Illuminate\Http\Request;

class AvailabilityController extends Controller
{
    protected $availabilityRepo;

    public function __construct(AvailabilityRepository $availabilityRepo)
    {
        $this->availabilityRepo = $availabilityRepo;
    }

    /**
     * Display a listing of all availabilities (Admin).
     */
    public function index(Request $request)
    {
        $statusFilter = $request->query('status', 'all');
        $availabilities = $this->availabilityRepo->getAllForAdmin(['status' => $statusFilter]);
        $pendingCount = \App\Models\Availability::where('status', 'pending')->count();

        return view('admin.availabilities.index', compact('availabilities', 'statusFilter', 'pendingCount'));
    }

    /**
     * Process an availability (Approve/Reject) (Admin).
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected',
            'admin_notes' => 'nullable|string',
        ]);

        $this->availabilityRepo->processAvailability($id, $request->all());

        return back()->with('success', 'Availability processed successfully.');
    }

    /**
     * Delete an availability (Admin).
     */
    public function destroy($id)
    {
        $availability = \App\Models\Availability::findOrFail($id);
        $availability->delete();

        return back()->with('success', 'Availability deleted successfully.');
    }
}

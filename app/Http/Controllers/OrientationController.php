<?php

namespace App\Http\Controllers;

use App\Repositories\OrientationRepository;
use Illuminate\Http\Request;

class OrientationController extends Controller
{
    protected $orientationRepo;

    public function __construct(OrientationRepository $orientationRepo)
    {
        $this->orientationRepo = $orientationRepo;
    }

    /**
     * Display a listing of the orientations.
     */
    public function index()
    {
        $orientations = $this->orientationRepo->getAllOrientations();
        return view('admin.orientations.index', compact('orientations'));
    }

    /**
     * Show the form for creating a new orientation.
     */
    public function create()
    {
        return view('admin.orientations.create');
    }

    /**
     * Store a newly created orientation in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|string|max:255|unique:orientations,type',
            'status' => 'required|boolean',
            'document' => 'required|file|mimes:pdf,doc,docx,txt,png,jpg,jpeg|max:5120', // Max 5MB
            'description' => 'nullable|string',
        ], [
            'type.unique' => 'This orientation type is already saved.',
        ]);

        $this->orientationRepo->createOrientation($request);

        return redirect()->route('orientations.index')->with('success', 'Orientation created successfully.');
    }

    /**
     * Show the form for editing the specified orientation.
     */
    public function edit($id)
    {
        $orientation = $this->orientationRepo->findOrientationById($id);
        return view('admin.orientations.edit', compact('orientation'));
    }

    /**
     * Update the specified orientation in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'type' => 'required|string|max:255|unique:orientations,type,' . $id,
            'status' => 'required|boolean',
            'document' => 'nullable|file|mimes:pdf,doc,docx,txt,png,jpg,jpeg|max:5120',
            'description' => 'nullable|string',
        ], [
            'type.unique' => 'This orientation type is already saved.',
        ]);

        $this->orientationRepo->updateOrientation($request, $id);

        return redirect()->route('orientations.index')->with('success', 'Orientation updated successfully.');
    }

    /**
     * Remove the specified orientation from storage.
     */
    public function delete($id)
    {
        $this->orientationRepo->deleteOrientation($id);
        return redirect()->route('orientations.index')->with('success', 'Orientation deleted successfully.');
    }
}

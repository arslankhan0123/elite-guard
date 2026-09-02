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
            // 'document' => 'required|file|mimes:pdf,doc,docx,txt,png,jpg,jpeg|max:5120', // Max 5MB
            'description' => 'nullable|string',
            'passing_percentage' => 'required|integer|min:0|max:100',
            'questions' => 'nullable|array',
            'questions.*.text' => 'required_with:questions|string',
            'questions.*.options' => 'required_with:questions|array|min:2',
            'questions.*.options.*.text' => 'required_with:questions|string',
        ], [
            'type.unique' => 'This orientation type is already saved.',
            'questions.*.options.min' => 'Each question must have at least 2 options.',
        ]);

        $orientation = $this->orientationRepo->createOrientation($request);

        // Dispatch FCM Push Notification to all devices
        try {
            $users = \App\Models\User::whereNotNull('fcm_token')->get();
            if ($users->isNotEmpty() && $orientation) {
                \Illuminate\Support\Facades\Notification::send($users, new \App\Notifications\OrientationNotification($orientation, false));
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Orientation FCM Notification failed: " . $e->getMessage());
        }

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
            // 'document' => 'nullable|file|mimes:pdf,doc,docx,txt,png,jpg,jpeg|max:5120',
            'description' => 'nullable|string',
            'passing_percentage' => 'required|integer|min:0|max:100',
            'questions' => 'nullable|array',
            'questions.*.text' => 'required_with:questions|string',
            'questions.*.options' => 'required_with:questions|array|min:2',
            'questions.*.options.*.text' => 'required_with:questions|string',
        ], [
            'type.unique' => 'This orientation type is already saved.',
            'questions.*.options.min' => 'Each question must have at least 2 options.',
        ]);

        $orientation = $this->orientationRepo->updateOrientation($request, $id);

        // Dispatch FCM Push Notification for update
        try {
            $users = \App\Models\User::whereNotNull('fcm_token')->get();
            if ($users->isNotEmpty() && $orientation) {
                \Illuminate\Support\Facades\Notification::send($users, new \App\Notifications\OrientationNotification($orientation, true));
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Orientation FCM Notification update failed: " . $e->getMessage());
        }

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

    /**
     * Show users who attempted the orientation.
     */
    public function attempts($id)
    {
        $orientation = $this->orientationRepo->findOrientationById($id);
        $attempts = \App\Models\OrientationAttempt::where('orientation_id', $id)
            ->with('user')
            ->orderBy('id', 'desc')
            ->get();
        return view('admin.orientations.attempts', compact('orientation', 'attempts'));
    }

    /**
     * Show a specific attempt result.
     */
    public function showAttempt($id, $attempt_id)
    {
        $orientation = $this->orientationRepo->findOrientationById($id);
        $attempt = \App\Models\OrientationAttempt::with('user')->findOrFail($attempt_id);
        
        $validationResult = $this->orientationRepo->validateQuizAnswers($orientation, is_array($attempt->answers) ? $attempt->answers : json_decode($attempt->answers, true));
        
        return view('admin.orientations.attempt_show', compact('orientation', 'attempt', 'validationResult'));
    }
}

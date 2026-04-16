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
        $orientations = Orientation::with(['questions.options'])->orderBy('id', 'desc')->get();
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
        return Orientation::with(['questions.options'])->findOrFail($id);
    }

    /**
     * Create a new orientation.
     */
    public function createOrientation($request)
    {
        $data = $request->only(['type', 'status', 'description', 'passing_percentage']);
        
        if ($request->hasFile('document')) {
            $file = $request->file('document');
            $fileName = 'user_orientation_' . Auth::id() . '_' . time() . '_' . rand(1111, 9999) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('documents/orientations'), $fileName);
            $data['document'] = url('documents/orientations/' . $fileName);
        }

        $orientation = Orientation::create($data);

        // Handle Questions
        if ($request->has('questions')) {
            foreach ($request->questions as $qData) {
                if (empty($qData['text'])) continue;
                $question = $orientation->questions()->create(['question_text' => $qData['text']]);
                
                if (isset($qData['options'])) {
                    foreach ($qData['options'] as $oIndex => $oData) {
                        if (empty($oData['text'])) continue;
                        $question->options()->create([
                            'option_text' => $oData['text'],
                            'is_correct' => isset($qData['correct_option']) && $qData['correct_option'] == $oIndex
                        ]);
                    }
                }
            }
        }

        return $orientation;
    }

    /**
     * Update an existing orientation.
     */
    public function updateOrientation($request, $id)
    {
        $orientation = $this->findOrientationById($id);
        $data = $request->only(['type', 'status', 'description', 'passing_percentage']);

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

        // Handle Questions (Delete old ones and re-create for simplicity in this case)
        $orientation->questions()->delete();
        if ($request->has('questions')) {
            foreach ($request->questions as $qData) {
                if (empty($qData['text'])) continue;
                $question = $orientation->questions()->create(['question_text' => $qData['text']]);
                
                if (isset($qData['options'])) {
                    foreach ($qData['options'] as $oIndex => $oData) {
                        if (empty($oData['text'])) continue;
                        $question->options()->create([
                            'option_text' => $oData['text'],
                            'is_correct' => isset($qData['correct_option']) && $qData['correct_option'] == $oIndex
                        ]);
                    }
                }
            }
        }

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
        $orientation = Orientation::with('questions.options')->findOrFail($orientation_id);

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

        // Validate Quiz if questions exist
        $answers = $request->input('answers', []);
        $totalQuestions = $orientation->questions->count();
        $correctAnswersCount = 0;

        if ($totalQuestions > 0) {
            foreach ($orientation->questions as $question) {
                // Find matching answer for this question
                $userAnswer = collect($answers)->first(function ($ans) use ($question) {
                    return isset($ans['question_id']) && $ans['question_id'] == $question->id;
                });

                if ($userAnswer && isset($userAnswer['option_id'])) {
                    $selectedOption = $question->options->find($userAnswer['option_id']);
                    if ($selectedOption && $selectedOption->is_correct) {
                        $correctAnswersCount++;
                    }
                }
            }

            $score = ($correctAnswersCount / $totalQuestions) * 100;
            if ($score < $orientation->passing_percentage) {
                return [
                    'status' => false,
                    'message' => 'You did not achieve the required passing score of ' . $orientation->passing_percentage . '%. Your score: ' . round($score, 2) . '%',
                    'score' => $score,
                    'passing_percentage' => $orientation->passing_percentage
                ];
            }
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

        // Save individual answers
        foreach ($orientation->questions as $question) {
            $userAnswer = collect($answers)->first(function ($ans) use ($question) {
                return isset($ans['question_id']) && $ans['question_id'] == $question->id;
            });

            if ($userAnswer && isset($userAnswer['option_id'])) {
                $selectedOption = $question->options->find($userAnswer['option_id']);
                $signedOrientation->answers()->create([
                    'question_id' => $question->id,
                    'option_id' => $userAnswer['option_id'],
                    'is_correct' => $selectedOption ? $selectedOption->is_correct : false,
                ]);
            }
        }

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
        $signedOrientations = SignedOrientation::with(['user', 'orientation', 'answers.question', 'answers.option'])->orderBy('id', 'desc')->get();
        return [
            'status' => true,
            'message' => 'Orientation signatures retrieved successfully.',
            'signedOrientations' => $signedOrientations
        ];
    }
}

<?php

namespace App\Repositories;

use App\Models\Policy;
use App\Models\SignedPolicy;
use Illuminate\Support\Facades\Auth;

class PolicyRepository
{
    /**
     * Get all policies.
     */
    public function getAllPolicies()
    {
        $policies = Policy::orderBy('id', 'desc')->get();
        return [
            'status' => true,
            'message' => 'Policies retrieved successfully',
            'policies' => $policies
        ];
    }

    /**
     * Find a policy by ID.
     */
    public function findPolicyById($id)
    {
        return Policy::findOrFail($id);
    }

    /**
     * Create a new policy.
     */
    public function createPolicy($request)
    {
        $data = $request->all();
        if ($request->hasFile('document')) {
            $file = $request->file('document');
            $fileName = 'user_' . Auth::id() . '_' . time() . '_' . rand(1111, 9999) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('documents/policies'), $fileName);
            $data['document'] = url('documents/policies/' . $fileName);
        }
        return Policy::create($data);
    }

    /**
     * Update an existing policy.
     */
    public function updatePolicy($request, $id)
    {
        $policy = $this->findPolicyById($id);
        $data = $request->all();

        if ($request->hasFile('document')) {
            // Delete old document if exists from public folder
            if ($policy->document) {
                $oldPath = str_replace(url('/'), public_path(), $policy->document);
                if (file_exists($oldPath)) {
                    @unlink($oldPath);
                }
            }
            $file = $request->file('document');
            $fileName = 'user_' . Auth::id() . '_' . time() . '_' . rand(1111, 9999) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('documents/policies'), $fileName);
            $data['document'] = url('documents/policies/' . $fileName);
        }

        $policy->update($data);
        return $policy;
    }

    /**
     * Delete a policy.
     */
    public function deletePolicy($id)
    {
        $policy = $this->findPolicyById($id);
        if ($policy->document) {
            $oldPath = str_replace(url('/'), public_path(), $policy->document);
            if (file_exists($oldPath)) {
                @unlink($oldPath);
            }
        }
        return $policy->delete();
    }

    /**
     * Store a signed policy.
     */
    public function storeSignedPolicies($request)
    {
        $user_id = Auth::id();
        $policy_id = $request->input('policy_id');

        // Check if the user has already signed this policy
        $exists = SignedPolicy::where('user_id', $user_id)
            ->where('policy_id', $policy_id)
            ->exists();

        if ($exists) {
            return [
                'status' => false,
                'message' => 'You have already signed this policy.'
            ];
        }

        $data = [
            'user_id' => $user_id,
            'policy_id' => $policy_id,
            'agreed' => $request->input('agreed'),
            'signature' => $request->input('signature'),
        ];

        if ($request->hasFile('document')) {
            $file = $request->file('document');
            // Custom naming: user_[id]_policy_[id]_[timestamp]_[rand].[extension]
            $fileName = 'user_' . $user_id . '_policy_' . $policy_id . '_' . time() . '_' . rand(1111, 9999) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('documents/signed_policies'), $fileName);
            $data['document'] = url('documents/signed_policies/' . $fileName);
        }

        $signedPolicy = SignedPolicy::create($data);

        return [
            'status' => true,
            'message' => 'Policy signed successfully.',
            'signedPolicy' => $signedPolicy
        ];
    }

    /**
     * Get all signed policies for admin view.
     */
    public function getAllSignedPolicies()
    {
        $signedPolicies = SignedPolicy::with(['user', 'policy'])->orderBy('id', 'desc')->get();
        return [
            'status' => true,
            'message' => 'Policy signed successfully.',
            'signedPolicies' => $signedPolicies
        ];
    }
}

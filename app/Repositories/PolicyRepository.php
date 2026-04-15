<?php

namespace App\Repositories;

use App\Models\Policy;

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
            $fileName = time() . '_' . $file->getClientOriginalName();
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
            $fileName = time() . '_' . $file->getClientOriginalName();
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
}

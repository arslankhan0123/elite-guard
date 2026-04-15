<?php

namespace App\Http\Controllers;

use App\Repositories\PolicyRepository;
use Illuminate\Http\Request;

class PolicyController extends Controller
{
    protected $policyRepo;

    public function __construct(PolicyRepository $policyRepo)
    {
        $this->policyRepo = $policyRepo;
    }

    /**
     * Display a listing of the policies.
     */
    public function index()
    {
        $policies = $this->policyRepo->getAllPolicies();
        return view('admin.policies.index', compact('policies'));
    }

    /**
     * Show the form for creating a new policy.
     */
    public function create()
    {
        return view('admin.policies.create');
    }

    /**
     * Store a newly created policy in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|string|max:255|unique:policies,type',
            'status' => 'required|boolean',
            'document' => 'required|file|mimes:pdf,doc,docx,txt,png,jpg,jpeg|max:5120', // Max 5MB
        ], [
            'type.unique' => 'This policy type is already saved.',
        ]);

        $this->policyRepo->createPolicy($request);

        return redirect()->route('policies.index')->with('success', 'Policy created successfully.');
    }

    /**
     * Show the form for editing the specified policy.
     */
    public function edit($id)
    {
        $policy = $this->policyRepo->findPolicyById($id);
        return view('admin.policies.edit', compact('policy'));
    }

    /**
     * Update the specified policy in storage.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'type' => 'required|string|max:255|unique:policies,type,' . $id,
            'status' => 'required|boolean',
            'document' => 'nullable|file|mimes:pdf,doc,docx,txt,png,jpg,jpeg|max:5120',
        ], [
            'type.unique' => 'This policy type is already saved.',
        ]);

        $this->policyRepo->updatePolicy($request, $id);

        return redirect()->route('policies.index')->with('success', 'Policy updated successfully.');
    }

    /**
     * Remove the specified policy from storage.
     */
    public function delete($id)
    {
        $this->policyRepo->deletePolicy($id);
        return redirect()->route('policies.index')->with('success', 'Policy deleted successfully.');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Site;
use App\Models\User;
use App\Models\Dispatch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DispatchController extends Controller
{
    public function index(Request $request)
    {
        $query = Dispatch::with(['company', 'site']);

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('caller_name', 'like', "%{$search}%")
                  ->orWhere('incident_location', 'like', "%{$search}%")
                  ->orWhere('incident_type', 'like', "%{$search}%");
            });
        }

        $dispatches = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('admin.dispatches.index', compact('dispatches'));
    }

    public function create()
    {
        $companies = Company::orderBy('name')->get();
        $sites = Site::orderBy('name')->get();
        $guards = User::where('role', 'Employee')->orWhereHas('employee')->orderBy('name')->get();

        return view('admin.dispatches.create', compact('companies', 'sites', 'guards'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'company_id' => 'required|exists:companies,id',
            'site_id' => 'required|exists:sites,id',
            'assigned_guard_ids' => 'nullable|array',
            'assigned_guard_ids.*' => 'exists:users,id',
            'priority' => 'required|in:Low,Medium,High,Emergency',
            'caller_type' => 'required|in:Client,Guard,Emergency Services,Other',
            'caller_name' => 'required|string|max:255',
            'incident_location' => 'required|string|max:255',
            'incident_type' => 'required|string|max:255',
            'incident_date' => 'required|date',
            'incident_time' => 'required',
            'incident_details' => 'required|string',
            'action_taken' => 'nullable|string',
            'internal_notes' => 'nullable|string',
            'attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx,zip|max:10240',
        ]);

        $data = $request->all();

        if ($request->hasFile('attachment')) {
            $data['attachment_path'] = $request->file('attachment')->store('documents/dispatch_attachments', 'public');
        }

        Dispatch::create($data);

        return redirect()->route('dispatches.index')->with('success', 'Dispatch Task created successfully.');
    }

    public function show($id)
    {
        $dispatch = Dispatch::with(['company', 'site'])->findOrFail($id);
        return view('admin.dispatches.show', compact('dispatch'));
    }

    public function edit($id)
    {
        $dispatch = Dispatch::findOrFail($id);
        $companies = Company::orderBy('name')->get();
        $sites = Site::orderBy('name')->get();
        $guards = User::where('role', 'Employee')->orWhereHas('employee')->orderBy('name')->get();

        return view('admin.dispatches.edit', compact('dispatch', 'companies', 'sites', 'guards'));
    }

    public function update(Request $request, $id)
    {
        $dispatch = Dispatch::findOrFail($id);

        $request->validate([
            'company_id' => 'required|exists:companies,id',
            'site_id' => 'required|exists:sites,id',
            'assigned_guard_ids' => 'nullable|array',
            'assigned_guard_ids.*' => 'exists:users,id',
            'priority' => 'required|in:Low,Medium,High,Emergency',
            'caller_type' => 'required|in:Client,Guard,Emergency Services,Other',
            'caller_name' => 'required|string|max:255',
            'incident_location' => 'required|string|max:255',
            'incident_type' => 'required|string|max:255',
            'incident_date' => 'required|date',
            'incident_time' => 'required',
            'incident_details' => 'required|string',
            'action_taken' => 'nullable|string',
            'internal_notes' => 'nullable|string',
            'status' => 'required|in:Pending,In Progress,Completed,Cancelled',
            'attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx,zip|max:10240',
        ]);

        $data = $request->all();

        if ($request->hasFile('attachment')) {
            if ($dispatch->attachment_path) {
                Storage::disk('public')->delete($dispatch->attachment_path);
            }
            $data['attachment_path'] = $request->file('attachment')->store('documents/dispatch_attachments', 'public');
        }

        $dispatch->update($data);

        return redirect()->route('dispatches.index')->with('success', 'Dispatch Task updated successfully.');
    }

    public function destroy($id)
    {
        $dispatch = Dispatch::findOrFail($id);
        if ($dispatch->attachment_path) {
            Storage::disk('public')->delete($dispatch->attachment_path);
        }
        $dispatch->delete();

        return redirect()->route('dispatches.index')->with('success', 'Dispatch Task deleted successfully.');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class EmployeeController extends Controller
{
    public function index()
    {
        $employees = Employee::with('user')->get();
        return view('admin.employees.index', compact('employees'));
    }

    public function create()
    {
        return view('admin.employees.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|string',
            'phone' => 'nullable|string',
            'address' => 'nullable|string',
            'cnic' => 'nullable|string',
            'gender' => 'nullable|string',
            'joining_date' => 'nullable|date',
        ]);

        DB::transaction(function () use ($request) {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'real_password' => $request->password, // Storing for admin visibility as per existing pattern
                'role' => $request->role,
            ]);

            Employee::create([
                'user_id' => $user->id,
                'phone' => $request->phone,
                'address' => $request->address,
                'cnic' => $request->cnic,
                'gender' => $request->gender,
                'joining_date' => $request->joining_date,
                'status' => true,
            ]);
        });

        return redirect()->route('employees.index')->with('success', 'Employee created successfully!');
    }

    public function edit($id)
    {
        $employee = Employee::with('user')->findOrFail($id);
        return view('admin.employees.edit', compact('employee'));
    }

    public function update(Request $request, $id)
    {
        $employee = Employee::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $employee->user_id,
            'role' => 'required|string',
            'phone' => 'nullable|string',
            'address' => 'nullable|string',
            'cnic' => 'nullable|string',
            'gender' => 'nullable|string',
            'joining_date' => 'nullable|date',
        ]);

        DB::transaction(function () use ($request, $employee) {
            $user = $employee->user;
            $user->update([
                'name' => $request->name,
                'email' => $request->email,
                'role' => $request->role,
            ]);

            if ($request->filled('password')) {
                $user->update([
                    'password' => Hash::make($request->password),
                    'real_password' => $request->password,
                ]);
            }

            $employee->update([
                'phone' => $request->phone,
                'address' => $request->address,
                'cnic' => $request->cnic,
                'gender' => $request->gender,
                'joining_date' => $request->joining_date,
            ]);
        });

        return redirect()->route('employees.index')->with('success', 'Employee updated successfully!');
    }

    public function delete($id)
    {
        $employee = Employee::findOrFail($id);
        $user = $employee->user;
        
        DB::transaction(function () use ($employee, $user) {
            $employee->delete();
            $user->delete();
        });

        return redirect()->route('employees.index')->with('success', 'Employee deleted successfully!');
    }
}

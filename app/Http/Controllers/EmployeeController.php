<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Company;
use App\Http\Requests\StoreEmployeeRequest;
use App\Http\Requests\UpdateEmployeeRequest;
use Illuminate\Support\Facades\Storage;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     * 
     * Shows all employees with pagination and eager loads company and user relationships.
     */
    public function index()
    {
        $employees = Employee::with(['company', 'user'])->latest()->paginate(10);
        return view('employees.index', compact('employees'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $companies = Company::all();
        return view('employees.create', compact('companies'));
    }

    /**
     * Store a newly created resource in storage.
     * 
     * Validates and creates a new employee with optional profile picture upload.
     * Profile pictures are stored in storage/app/public/profile_pictures directory.
     */
    public function store(StoreEmployeeRequest $request)
    {
        $validated = $request->validated();
        
        // Handle profile picture upload if provided
        if ($request->hasFile('profile_picture')) {
            $validated['profile_picture'] = $request->file('profile_picture')->store('profile_pictures', 'public');
        }
        
        Employee::create($validated);
        
        return redirect()->route('employees.index')
            ->with('success', 'Employee created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Employee $employee)
    {
        $employee->load(['company', 'user']);
        return view('employees.show', compact('employee'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Employee $employee)
    {
        $companies = Company::all();
        return view('employees.edit', compact('employee', 'companies'));
    }

    /**
     * Update the specified resource in storage.
     * 
     * Updates employee details and handles profile picture replacement.
     * If a new profile picture is uploaded, the old picture file is automatically deleted.
     */
    public function update(UpdateEmployeeRequest $request, Employee $employee)
    {
        $validated = $request->validated();
        
        // Handle profile picture upload and replace old picture if exists
        if ($request->hasFile('profile_picture')) {
            // Delete old profile picture file from storage
            if ($employee->profile_picture) {
                Storage::disk('public')->delete($employee->profile_picture);
            }
            $validated['profile_picture'] = $request->file('profile_picture')->store('profile_pictures', 'public');
        }
        
        $employee->update($validated);
        
        return redirect()->route('employees.index')
            ->with('success', 'Employee updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     * 
     * Deletes the employee and their associated profile picture file.
     */
    public function destroy(Employee $employee)
    {
        // Delete profile picture file from storage if it exists
        if ($employee->profile_picture) {
            Storage::disk('public')->delete($employee->profile_picture);
        }
        
        $employee->delete();
        
        return redirect()->route('employees.index')
            ->with('success', 'Employee deleted successfully.');
    }
    
    /**
     * Delete employee's profile picture.
     * 
     * Removes the profile picture file and sets profile_picture to null.
     * Employee will then fall back to company logo or default initials.
     */
    public function deleteProfilePicture(Employee $employee)
    {
        if ($employee->profile_picture) {
            Storage::disk('public')->delete($employee->profile_picture);
            $employee->update(['profile_picture' => null]);
        }
        
        return redirect()->back()
            ->with('success', 'Profile picture deleted successfully.');
    }
}

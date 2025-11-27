<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Company;
use App\Http\Requests\StoreEmployeeRequest;
use App\Http\Requests\UpdateEmployeeRequest;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     * 
     * Shows all employees with pagination and eager loads company and user relationships.
     */
    public function index(Request $request)
    {
        $query = Employee::with(['company', 'user']);
        
        // Search functionality
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhereHas('company', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }
        
        // Filter by company
        if ($request->has('company_id') && $request->company_id != '') {
            $query->where('company_id', $request->company_id);
        }
        
        // Sorting functionality
        $sortField = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');
        
        $allowedSorts = ['first_name', 'last_name', 'email', 'phone', 'created_at', 'company_id'];
        if (in_array($sortField, $allowedSorts)) {
            if ($sortField === 'company_id') {
                // Sort by company name instead of company_id
                $query->leftJoin('companies', 'employees.company_id', '=', 'companies.id')
                      ->orderBy('companies.name', $sortDirection)
                      ->select('employees.*');
            } else {
                $query->orderBy($sortField, $sortDirection);
            }
        } else {
            $query->latest();
        }
        
        $employees = $query->paginate(10)->appends($request->query());
        $companies = Company::orderBy('name')->get();
        
        return view('employees.index', compact('employees', 'companies'));
    }
    
    /**
     * Export employees to CSV
     */
    public function export(Request $request)
    {
        $query = Employee::with(['company', 'user']);
        
        // Apply same filters as index
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhereHas('company', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }
        
        if ($request->has('company_id') && $request->company_id != '') {
            $query->where('company_id', $request->company_id);
        }
        
        $employees = $query->get();
        
        $filename = 'employees_' . date('Y-m-d_His') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];
        
        $callback = function() use ($employees) {
            $file = fopen('php://output', 'w');
            
            // CSV Headers
            fputcsv($file, ['ID', 'First Name', 'Last Name', 'Company', 'Email', 'Phone', 'Is Admin', 'Created At']);
            
            // CSV Data
            foreach ($employees as $employee) {
                fputcsv($file, [
                    $employee->id,
                    $employee->first_name,
                    $employee->last_name,
                    $employee->company ? $employee->company->name : '',
                    $employee->email ?? '',
                    $employee->phone ?? '',
                    $employee->user_id ? 'Yes' : 'No',
                    $employee->created_at->format('Y-m-d H:i:s'),
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $companies = Company::orderBy('name')->get();
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
        $companies = Company::orderBy('name')->get();
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

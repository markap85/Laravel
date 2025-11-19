<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Http\Requests\StoreCompanyRequest;
use App\Http\Requests\UpdateCompanyRequest;
use Illuminate\Support\Facades\Storage;

class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $companies = Company::latest()->paginate(10);
        return view('companies.index', compact('companies'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('companies.create');
    }

    /**
     * Store a newly created resource in storage.
     * 
     * Validates and creates a new company with optional logo upload.
     * Logo images are stored in storage/app/public/logos directory.
     */
    public function store(StoreCompanyRequest $request)
    {
        $validated = $request->validated();
        
        // Handle logo upload if provided
        if ($request->hasFile('logo')) {
            $validated['logo'] = $request->file('logo')->store('logos', 'public');
        }
        
        Company::create($validated);
        
        return redirect()->route('companies.index')
            ->with('success', 'Company created successfully.');
    }

    /**
     * Display the specified resource.
     * 
     * Shows company details along with all associated employees.
     * Eager loads the employees relationship for efficient querying.
     */
    public function show(Company $company)
    {
        $company->load('employees');
        return view('companies.show', compact('company'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Company $company)
    {
        return view('companies.edit', compact('company'));
    }

    /**
     * Update the specified resource in storage.
     * 
     * Updates company details and handles logo replacement.
     * If a new logo is uploaded, the old logo file is automatically deleted.
     */
    public function update(UpdateCompanyRequest $request, Company $company)
    {
        $validated = $request->validated();
        
        // Handle logo upload and replace old logo if exists
        if ($request->hasFile('logo')) {
            // Delete old logo file from storage
            if ($company->logo) {
                Storage::disk('public')->delete($company->logo);
            }
            $validated['logo'] = $request->file('logo')->store('logos', 'public');
        }
        
        $company->update($validated);
        
        return redirect()->route('companies.index')
            ->with('success', 'Company updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     * 
     * Deletes the company and its associated logo file.
     * Note: Associated employees are NOT deleted; their company_id is set to null.
     */
    public function destroy(Company $company)
    {
        // Delete logo file from storage if it exists
        if ($company->logo) {
            Storage::disk('public')->delete($company->logo);
        }
        
        $company->delete();
        
        return redirect()->route('companies.index')
            ->with('success', 'Company deleted successfully.');
    }
    
    /**
     * Toggle admin access for a company.
     * 
     * When disabled, all employees from this company will be demoted from admin status.
     */
    public function toggleAdmin(Company $company)
    {
        $newStatus = !$company->allow_admin;
        
        // If disabling admin access, demote all employees from this company
        if (!$newStatus) {
            $adminEmployees = $company->employees()->whereNotNull('user_id')->get();
            
            foreach ($adminEmployees as $employee) {
                // Don't demote master admin
                if ($employee->user && $employee->user->email !== 'admin@admin.com') {
                    $user = $employee->user;
                    $employee->update(['user_id' => null]);
                    $user->delete();
                }
            }
        }
        
        $company->update(['allow_admin' => $newStatus]);
        
        $message = $newStatus 
            ? 'Admin access enabled for ' . $company->name . '. Employees can now be promoted to admin.'
            : 'Admin access disabled for ' . $company->name . '. All admin employees have been demoted.';
        
        return redirect()->back()->with('success', $message);
    }
    
    /**
     * Delete the company's logo.
     * 
     * Removes the logo file from storage and updates the company record.
     * Company name initials will be displayed as a fallback.
     */
    public function deleteLogo(Company $company)
    {
        if ($company->logo) {
            Storage::disk('public')->delete($company->logo);
            $company->update(['logo' => null]);
            
            return redirect()->back()->with('success', 'Logo deleted successfully.');
        }
        
        return redirect()->back()->with('error', 'No logo to delete.');
    }
}

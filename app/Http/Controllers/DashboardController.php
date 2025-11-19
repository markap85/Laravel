<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Employee;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        // Get total counts
        $totalCompanies = Company::count();
        $totalEmployees = Employee::count();
        
        // Get 5 most recent companies
        $recentCompanies = Company::latest()->take(5)->get();
        
        // Get 5 most recent employees
        $recentEmployees = Employee::with('company')->latest()->take(5)->get();
        
        return view('dashboard', compact(
            'totalCompanies',
            'totalEmployees',
            'recentCompanies',
            'recentEmployees'
        ));
    }
}

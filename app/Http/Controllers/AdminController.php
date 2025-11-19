<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    /**
     * Display a listing of admin users.
     */
    public function index()
    {
        // Get all employees that have a linked user account (admins)
        $admins = Employee::with(['user', 'company'])
            ->whereNotNull('user_id')
            ->latest()
            ->paginate(10);
            
        return view('admins.index', compact('admins'));
    }

    /**
     * Promote an employee to admin status.
     */
    public function promote(Employee $employee)
    {
        // Check if employee already has a user account
        if ($employee->user_id) {
            return redirect()->back()->with('error', 'This employee is already an admin.');
        }
        
        // Check if employee's company allows admin access
        if ($employee->company && !$employee->company->allow_admin) {
            return redirect()->back()->with('error', 'Admin access is not enabled for ' . $employee->company->name . '.');
        }
        
        // Create user account for the employee
        $user = User::create([
            'name' => $employee->first_name . ' ' . $employee->last_name,
            'email' => $employee->email ?? $employee->first_name . '.' . $employee->last_name . '@admin.local',
            'password' => Hash::make('password'), // Default password
            'is_admin' => true,
        ]);
        
        // Link employee to user account
        $employee->update(['user_id' => $user->id]);
        
        return redirect()->route('admins.index')
            ->with('success', 'Employee promoted to admin successfully. Default password is "password".');
    }

    /**
     * Demote an admin back to regular employee.
     */
    public function demote(Employee $employee)
    {
        // Prevent demoting the master admin
        if ($employee->user && $employee->user->email === 'admin@admin.com') {
            return redirect()->back()->with('error', 'Cannot demote the master admin account.');
        }
        
        if (!$employee->user_id) {
            return redirect()->back()->with('error', 'This employee is not an admin.');
        }
        
        // Delete the user account
        $user = $employee->user;
        $employee->update(['user_id' => null]);
        $user->delete();
        
        return redirect()->route('admins.index')
            ->with('success', 'Admin demoted to regular employee successfully.');
    }
}

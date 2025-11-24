<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AccountController;

Route::get('/', function () {
    return redirect('/login');
});

Auth::routes(['register' => false]);

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// Admin-only routes
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Company-specific routes (must be before resource routes)
    Route::get('/companies/export', [CompanyController::class, 'export'])->name('companies.export');
    Route::post('/companies/{company}/toggle-admin', [CompanyController::class, 'toggleAdmin'])->name('companies.toggleAdmin');
    Route::post('/companies/{company}/delete-logo', [CompanyController::class, 'deleteLogo'])->name('companies.deleteLogo');
    Route::resource('companies', CompanyController::class);
    
    // Employee-specific routes (must be before resource routes)
    Route::get('/employees/export', [EmployeeController::class, 'export'])->name('employees.export');
    Route::post('/employees/{employee}/delete-profile-picture', [EmployeeController::class, 'deleteProfilePicture'])->name('employees.deleteProfilePicture');
    Route::resource('employees', EmployeeController::class);
    
    // Admin management routes
    Route::get('/admins', [AdminController::class, 'index'])->name('admins.index');
    Route::post('/admins/{employee}/promote', [AdminController::class, 'promote'])->name('admins.promote');
    Route::delete('/admins/{employee}/demote', [AdminController::class, 'demote'])->name('admins.demote');
    
    // Account management routes
    Route::get('/account/edit', [AccountController::class, 'edit'])->name('account.edit');
    Route::put('/account', [AccountController::class, 'update'])->name('account.update');
});

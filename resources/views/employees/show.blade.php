@extends('layouts.app')

@section('content')
<div class="container">
    <x-breadcrumb :items="[
        ['title' => 'Employees', 'url' => route('employees.index')],
        ['title' => $employee->first_name . ' ' . $employee->last_name, 'url' => route('employees.show', $employee)]
    ]" />
    
    <div class="print-only mb-3">
        <h2>{{ $employee->first_name }} {{ $employee->last_name }}</h2>
        <p class="text-muted">Employee Details Report - Generated {{ now()->format('F d, Y h:i A') }}</p>
    </div>
    
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-person"></i> Employee Details</span>
                    <div class="no-print">
                        <button onclick="window.print()" class="btn btn-secondary btn-sm me-1">
                            <i class="bi bi-printer"></i> Print
                        </button>
                        <a href="{{ route('employees.edit', $employee) }}" class="btn btn-primary btn-sm">
                            <i class="bi bi-pencil"></i> Edit
                        </a>
                        <a href="{{ route('employees.index') }}" class="btn btn-secondary btn-sm">
                            <i class="bi bi-arrow-left"></i> Back to List
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-4"><strong>Profile Picture:</strong></div>
                        <div class="col-md-8">
                            @if($employee->profile_picture)
                                <div>
                                    <img src="{{ asset('storage/' . $employee->profile_picture) }}" 
                                         alt="{{ $employee->first_name }}" 
                                         style="width: 150px; height: 150px; object-fit: cover;" 
                                         class="rounded-circle img-thumbnail">
                                    
                                    <form action="{{ route('employees.deleteProfilePicture', $employee) }}" 
                                          method="POST" 
                                          class="d-inline-block mt-2"
                                          onsubmit="return confirm('Are you sure you want to delete this profile picture?');">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-danger">
                                            <i class="bi bi-trash"></i> Delete Profile Picture
                                        </button>
                                    </form>
                                </div>
                            @elseif($employee->company && $employee->company->logo)
                                <img src="{{ asset('storage/' . $employee->company->logo) }}" 
                                     alt="{{ $employee->company->name }}" 
                                     style="width: 150px; height: 150px; object-fit: cover;" 
                                     class="rounded-circle img-thumbnail">
                                <small class="text-muted d-block mt-2">Using company logo</small>
                            @else
                                <div style="width: 150px; height: 150px;" 
                                     class="bg-secondary rounded-circle d-flex align-items-center justify-content-center text-white img-thumbnail">
                                    <h1 class="mb-0">{{ substr($employee->first_name, 0, 1) }}{{ substr($employee->last_name, 0, 1) }}</h1>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4"><strong>First Name:</strong></div>
                        <div class="col-md-8">{{ $employee->first_name }}</div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4"><strong>Last Name:</strong></div>
                        <div class="col-md-8">{{ $employee->last_name }}</div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4"><strong>Full Name:</strong></div>
                        <div class="col-md-8">
                            {{ $employee->first_name }} {{ $employee->last_name }}
                            @if($employee->user_id)
                                <span class="badge bg-success ms-2">Admin User</span>
                            @endif
                        </div>
                    </div>

                    @if($employee->user_id)
                    <div class="row mb-3">
                        <div class="col-md-4"><strong>Admin Account:</strong></div>
                        <div class="col-md-8">
                            {{ $employee->user->email }}
                            @if($employee->user->email === 'admin@admin.com')
                                <span class="badge bg-danger ms-2">Master Admin</span>
                            @endif
                        </div>
                    </div>
                    @endif

                    <div class="row mb-3">
                        <div class="col-md-4"><strong>Company:</strong></div>
                        <div class="col-md-8">
                            @if($employee->company)
                                <a href="{{ route('companies.show', $employee->company) }}">{{ $employee->company->name }}</a>
                            @else
                                <span class="text-muted">No company assigned</span>
                            @endif
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4"><strong>Email:</strong></div>
                        <div class="col-md-8">
                            @if($employee->email)
                                <a href="mailto:{{ $employee->email }}">{{ $employee->email }}</a>
                            @else
                                <span class="text-muted">N/A</span>
                            @endif
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4"><strong>Phone:</strong></div>
                        <div class="col-md-8">
                            @if($employee->phone)
                                <a href="tel:{{ $employee->phone }}">{{ $employee->phone }}</a>
                            @else
                                <span class="text-muted">N/A</span>
                            @endif
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4"><strong>Created:</strong></div>
                        <div class="col-md-8">{{ $employee->created_at->format('F d, Y h:i A') }}</div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4"><strong>Last Updated:</strong></div>
                        <div class="col-md-8">{{ $employee->updated_at->format('F d, Y h:i A') }}</div>
                    </div>

                    <hr>

                    <form action="{{ route('employees.destroy', $employee) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this employee?')">Delete Employee</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@extends('layouts.app')

@section('content')
<div class="container">
    <x-breadcrumb :items="[
        ['title' => 'Companies', 'url' => route('companies.index')],
        ['title' => $company->name, 'url' => route('companies.show', $company)]
    ]" />
    
    <div class="print-only mb-3">
        <h2>{{ $company->name }}</h2>
        <p class="text-muted">Company Details Report - Generated {{ now()->format('F d, Y h:i A') }}</p>
    </div>
    
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-building"></i> Company Details</span>
                    <div class="no-print">
                        <button onclick="window.print()" class="btn btn-secondary btn-sm me-1">
                            <i class="bi bi-printer"></i> Print
                        </button>
                        <a href="{{ route('companies.edit', $company) }}" class="btn btn-warning btn-sm">
                            <i class="bi bi-pencil"></i> Edit
                        </a>
                        <a href="{{ route('companies.index') }}" class="btn btn-secondary btn-sm">
                            <i class="bi bi-arrow-left"></i> Back to List
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-4"><strong>Company Name:</strong></div>
                        <div class="col-md-8">{{ $company->name }}</div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4"><strong>Email:</strong></div>
                        <div class="col-md-8">{{ $company->email ?? 'N/A' }}</div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4"><strong>Website:</strong></div>
                        <div class="col-md-8">
                            @if($company->website)
                                <a href="{{ $company->website }}" target="_blank">{{ $company->website }}</a>
                            @else
                                N/A
                            @endif
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4"><strong>Logo:</strong></div>
                        <div class="col-md-8">
                            @if($company->logo)
                                <div>
                                    <img src="{{ asset('storage/' . $company->logo) }}" alt="{{ $company->name }}" style="width: 200px; height: 200px; object-fit: cover;" class="img-thumbnail rounded-circle">
                                    <div class="mt-2">
                                        <form action="{{ route('companies.deleteLogo', $company) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this logo? The company initials will be displayed instead.')">
                                            @csrf
                                            <button type="submit" class="btn btn-danger btn-sm">
                                                <i class="bi bi-trash"></i> Delete Logo
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @else
                                <div style="width: 200px; height: 200px;" 
                                     class="bg-primary rounded-circle d-flex align-items-center justify-content-center text-white">
                                    <h1 class="mb-0"><strong>{{ substr($company->name, 0, 1) }}</strong></h1>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4"><strong>Admin Access:</strong></div>
                        <div class="col-md-8">
                            @if($company->allow_admin)
                                <span class="badge bg-success">Enabled</span>
                                <p class="text-muted mb-0 mt-2">Employees from this company can be promoted to admin.</p>
                            @else
                                <span class="badge bg-secondary">Disabled</span>
                                <p class="text-muted mb-0 mt-2">Employees from this company cannot be promoted to admin.</p>
                            @endif
                            <div class="mt-2">
                                <form action="{{ route('companies.toggleAdmin', $company) }}" method="POST" class="d-inline">
                                    @csrf
                                    @if($company->allow_admin)
                                        <button type="submit" class="btn btn-warning btn-sm" 
                                                onclick="return confirm('Disable admin access for {{ $company->name }}? All admin employees will be demoted.')">
                                            Disable Admin Access
                                        </button>
                                    @else
                                        <button type="submit" class="btn btn-success btn-sm">
                                            Enable Admin Access
                                        </button>
                                    @endif
                                </form>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4"><strong>Created:</strong></div>
                        <div class="col-md-8">{{ $company->created_at->format('F d, Y h:i A') }}</div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4"><strong>Last Updated:</strong></div>
                        <div class="col-md-8">{{ $company->updated_at->format('F d, Y h:i A') }}</div>
                    </div>

                    <hr>

                    <form action="{{ route('companies.destroy', $company) }}" method="POST" class="d-inline no-print" id="delete-company-form">
                        @csrf
                        @method('DELETE')
                        <button type="button" class="btn btn-danger" 
                                onclick="if(confirm('Are you sure you want to delete {{ addslashes($company->name) }}? This will also remove all associated employees. This action cannot be undone.')) { document.getElementById('delete-company-form').submit(); }">
                            <i class="bi bi-trash"></i> Delete Company
                        </button>
                    </form>
                </div>
            </div>

            @if($company->employees->count() > 0)
            <div class="card mt-4">
                <div class="card-header">
                    <strong>Employees ({{ $company->employees->count() }})</strong>
                </div>
                <div class="card-body">
                    <div class="list-group">
                        @foreach($company->employees as $employee)
                            <a href="{{ route('employees.show', $employee) }}" class="list-group-item list-group-item-action">
                                <div class="d-flex align-items-center">
                                    <div class="me-3">
                                        @if($employee->profile_picture)
                                            <img src="{{ asset('storage/' . $employee->profile_picture) }}" 
                                                 alt="{{ $employee->first_name }}" 
                                                 style="width: 50px; height: 50px; object-fit: cover;"
                                                 class="rounded-circle">
                                        @elseif($company->logo)
                                            <img src="{{ asset('storage/' . $company->logo) }}" 
                                                 alt="{{ $company->name }}" 
                                                 style="width: 50px; height: 50px; object-fit: cover;"
                                                 class="rounded-circle">
                                        @else
                                            <div style="width: 50px; height: 50px;" 
                                                 class="bg-info rounded-circle d-flex align-items-center justify-content-center text-white">
                                                <strong>{{ substr($employee->first_name, 0, 1) }}{{ substr($employee->last_name, 0, 1) }}</strong>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-0">{{ $employee->first_name }} {{ $employee->last_name }}</h6>
                                        <small class="text-muted">
                                            @if($employee->email)
                                                {{ $employee->email }}
                                            @endif
                                            @if($employee->email && $employee->phone)
                                                | 
                                            @endif
                                            @if($employee->phone)
                                                {{ $employee->phone }}
                                            @endif
                                        </small>
                                    </div>
                                    <div>
                                        <i class="bi bi-chevron-right"></i>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
            @else
            <div class="card mt-4">
                <div class="card-header">
                    <strong>Employees</strong>
                </div>
                <div class="card-body text-center text-muted">
                    <p class="mb-0">No employees assigned to this company yet.</p>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

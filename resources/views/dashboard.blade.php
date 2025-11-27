@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">Welcome, {{ Auth::user()->name }}!</h4>
                </div>

                <div class="card-body">
                    <!-- Statistics Cards -->
                    <div class="row mb-4">
                        <div class="col-md-6 mb-3 mb-md-0">
                            <div class="card bg-primary text-white shadow-dark">
                                <div class="card-body text-center">
                                    <h2 class="display-4 text-white">{{ $totalCompanies }}</h2>
                                    <p class="mb-3">Total Companies</p>
                                    <a href="{{ route('companies.create') }}" class="btn btn-light btn-sm d-inline-flex align-items-center gap-1">
                                        <i class="bi bi-plus-circle"></i> Add Company
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card bg-success text-white shadow-dark">
                                <div class="card-body text-center">
                                    <h2 class="display-4 text-white">{{ $totalEmployees }}</h2>
                                    <p class="mb-3">Total Employees</p>
                                    <a href="{{ route('employees.create') }}" class="btn btn-light btn-sm d-inline-flex align-items-center gap-1">
                                        <i class="bi bi-plus-circle"></i> Add Employee
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Lists -->
                    <div class="row">
                        <!-- Recent Companies -->
                        <div class="col-md-6 mb-3 mb-md-0">
                            <div class="card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <strong>Recent Companies</strong>
                                    <a href="{{ route('companies.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
                                </div>
                                <div class="card-body p-0">
                                    @if($recentCompanies->count() > 0)
                                        <ul class="list-group list-group-flush">
                                            @foreach($recentCompanies as $company)
                                                <li class="list-group-item">
                                                    <div class="d-flex align-items-center">
                                                        <div class="me-3">
                                                            @if($company->logo)
                                                                <img src="{{ asset('storage/' . $company->logo) }}" 
                                                                     alt="{{ $company->name }}" 
                                                                     style="width: 50px; height: 50px; object-fit: cover;"
                                                                     class="rounded-circle">
                                                            @else
                                                                <div style="width: 50px; height: 50px;" 
                                                                     class="bg-secondary rounded-circle d-flex align-items-center justify-content-center text-white">
                                                                    <strong>{{ substr($company->name, 0, 1) }}</strong>
                                                                </div>
                                                            @endif
                                                        </div>
                                                        <div class="flex-grow-1">
                                                            <a href="{{ route('companies.show', $company) }}" class="text-decoration-none">
                                                                <strong>{{ $company->name }}</strong>
                                                            </a>
                                                            <br>
                                                            <small class="text-muted">
                                                                Created {{ $company->created_at->diffForHumans() }}
                                                            </small>
                                                        </div>
                                                    </div>
                                                </li>
                                            @endforeach
                                        </ul>
                                    @else
                                        <p class="text-muted p-3">No companies yet. <a href="{{ route('companies.create') }}">Create one now</a></p>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Recent Employees -->
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <strong>Recent Employees</strong>
                                    <a href="{{ route('employees.index') }}" class="btn btn-sm btn-outline-success">View All</a>
                                </div>
                                <div class="card-body p-0">
                                    @if($recentEmployees->count() > 0)
                                        <ul class="list-group list-group-flush">
                                            @foreach($recentEmployees as $employee)
                                                <li class="list-group-item">
                                                    <div class="d-flex align-items-center">
                                                        <div class="me-3">
                                                            @if($employee->profile_picture)
                                                                <img src="{{ asset('storage/' . $employee->profile_picture) }}" 
                                                                     alt="{{ $employee->first_name }}" 
                                                                     style="width: 50px; height: 50px; object-fit: cover;"
                                                                     class="rounded-circle">
                                                            @elseif($employee->company && $employee->company->logo)
                                                                <img src="{{ asset('storage/' . $employee->company->logo) }}" 
                                                                     alt="{{ $employee->company->name }}" 
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
                                                            <a href="{{ route('employees.show', $employee) }}" class="text-decoration-none">
                                                                <strong>{{ $employee->first_name }} {{ $employee->last_name }}</strong>
                                                            </a>
                                                            @if($employee->company)
                                                                <br>
                                                                <small class="text-muted">{{ $employee->company->name }}</small>
                                                            @endif
                                                            <br>
                                                            <small class="text-muted">
                                                                Created {{ $employee->created_at->diffForHumans() }}
                                                            </small>
                                                        </div>
                                                    </div>
                                                </li>
                                            @endforeach
                                        </ul>
                                    @else
                                        <p class="text-muted p-3">No employees yet. <a href="{{ route('employees.create') }}">Create one now</a></p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

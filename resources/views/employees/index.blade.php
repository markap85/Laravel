@extends('layouts.app')

@section('content')
<div class="container">
    <x-breadcrumb :items="[
        ['title' => 'Employees', 'url' => route('employees.index')]
    ]" />
    
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-people"></i> Employees</span>
                    <div class="d-flex gap-2">
                        <a href="{{ route('employees.export', request()->query()) }}" class="btn btn-success btn-sm no-print">
                            <i class="bi bi-file-earmark-spreadsheet"></i> Export CSV
                        </a>
                        <a href="{{ route('employees.create') }}" class="btn btn-primary btn-sm no-print">
                            <i class="bi bi-plus-circle"></i> Add New Employee
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Search and Filter Bar -->
                    <div class="row mb-3 no-print">
                        <div class="col-md-8">
                            <form method="GET" action="{{ route('employees.index') }}" class="d-flex gap-2">
                                <div class="flex-grow-1">
                                    <input type="text" 
                                           name="search" 
                                           class="form-control" 
                                           placeholder="Search by name, email, phone, or company..." 
                                           value="{{ request('search') }}">
                                </div>
                                <input type="hidden" name="company_id" value="{{ request('company_id') }}">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-search"></i> Search
                                </button>
                                @if(request('search') || request('company_id'))
                                    <a href="{{ route('employees.index') }}" class="btn btn-secondary">
                                        <i class="bi bi-x-circle"></i> Clear
                                    </a>
                                @endif
                            </form>
                        </div>
                        <div class="col-md-4">
                            <form method="GET" action="{{ route('employees.index') }}">
                                <input type="hidden" name="search" value="{{ request('search') }}">
                                <select name="company_id" class="form-select" onchange="this.form.submit()">
                                    <option value="">All Companies</option>
                                    @foreach($companies as $company)
                                        <option value="{{ $company->id }}" {{ request('company_id') == $company->id ? 'selected' : '' }}>
                                            {{ $company->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </form>
                        </div>
                    </div>

                    @if($employees->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped sortable-table">
                                <thead>
                                    <tr>
                                        <th>Photo</th>
                                        <th data-sort="first_name" class="{{ request('sort') == 'first_name' ? 'sort-' . request('direction', 'asc') : '' }}">
                                            First Name
                                        </th>
                                        <th data-sort="last_name" class="{{ request('sort') == 'last_name' ? 'sort-' . request('direction', 'asc') : '' }}">
                                            Last Name
                                        </th>
                                        <th data-sort="company_id" class="{{ request('sort') == 'company_id' ? 'sort-' . request('direction', 'asc') : '' }}">
                                            Company
                                        </th>
                                        <th data-sort="email" class="{{ request('sort') == 'email' ? 'sort-' . request('direction', 'asc') : '' }}">
                                            Email
                                        </th>
                                        <th data-sort="phone" class="{{ request('sort') == 'phone' ? 'sort-' . request('direction', 'asc') : '' }}">
                                            Phone
                                        </th>
                                        <th class="actions-column no-print">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($employees as $employee)
                                        <tr>
                                            <td>
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
                                                         class="bg-secondary rounded-circle d-flex align-items-center justify-content-center text-white">
                                                        <strong>{{ substr($employee->first_name, 0, 1) }}{{ substr($employee->last_name, 0, 1) }}</strong>
                                                    </div>
                                                @endif
                                            </td>
                                            <td>{{ $employee->first_name }}</td>
                                            <td>
                                                {{ $employee->last_name }}
                                                @if($employee->user_id)
                                                    <span class="badge bg-success ms-1"><i class="bi bi-shield-check"></i> Admin</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($employee->company)
                                                    <a href="{{ route('companies.show', $employee->company) }}">{{ $employee->company->name }}</a>
                                                    @if($employee->company->allow_admin)
                                                        <span class="badge bg-success ms-1" title="Admin access enabled">
                                                            <i class="bi bi-shield-check"></i>
                                                        </span>
                                                    @endif
                                                @else
                                                    <span class="text-muted">No Company</span>
                                                @endif
                                            </td>
                                            <td>{{ $employee->email ?? 'N/A' }}</td>
                                            <td>{{ $employee->phone ?? 'N/A' }}</td>
                                            <td class="no-print">
                                                <div class="d-flex gap-1 flex-wrap">
                                                    <a href="{{ route('employees.show', $employee) }}" class="btn btn-info btn-sm" title="View Employee">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                    <a href="{{ route('employees.edit', $employee) }}" class="btn btn-warning btn-sm" title="Edit Employee">
                                                        <i class="bi bi-pencil-square"></i>
                                                    </a>
                                                    <form action="{{ route('employees.destroy', $employee) }}" method="POST" class="d-inline" id="delete-form-{{ $employee->id }}">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="button" class="btn btn-danger btn-sm" title="Delete Employee" 
                                                                onclick="if(confirm('Are you sure you want to delete {{ addslashes($employee->first_name . ' ' . $employee->last_name) }}? This action cannot be undone.')) { document.getElementById('delete-form-{{ $employee->id }}').submit(); }">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-between align-items-center no-print">
                            <div class="text-muted">
                                Showing {{ $employees->firstItem() }} to {{ $employees->lastItem() }} of {{ $employees->total() }} employees
                            </div>
                            <div>
                                {{ $employees->links() }}
                            </div>
                        </div>
                    @else
                        <div class="empty-state">
                            <div class="empty-state-icon">
                                <i class="bi bi-people"></i>
                            </div>
                            <h3 class="empty-state-title">No Employees Found</h3>
                            <p class="empty-state-description">
                                @if(request('search') || request('company_id'))
                                    No employees match your search criteria. Try adjusting your filters.
                                @else
                                    Get started by adding your first employee to the system.
                                @endif
                            </p>
                            @if(request('search') || request('company_id'))
                                <a href="{{ route('employees.index') }}" class="btn btn-secondary">
                                    <i class="bi bi-x-circle"></i> Clear Filters
                                </a>
                            @else
                                <a href="{{ route('employees.create') }}" class="btn btn-primary">
                                    <i class="bi bi-plus-circle"></i> Add Your First Employee
                                </a>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

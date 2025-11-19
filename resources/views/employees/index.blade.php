@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>Employees</span>
                    <a href="{{ route('employees.create') }}" class="btn btn-primary btn-sm">Add New Employee</a>
                </div>

                <div class="card-body">
                    @if($employees->count() > 0)
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Photo</th>
                                    <th>First Name</th>
                                    <th>Last Name</th>
                                    <th>Company</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th width="200px">Actions</th>
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
                                                <span class="badge bg-success ms-1">Admin</span>
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
                                        <td>
                                            <div class="d-flex gap-1 flex-wrap">
                                                <a href="{{ route('employees.show', $employee) }}" class="btn btn-info btn-sm" title="View Employee">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <a href="{{ route('employees.edit', $employee) }}" class="btn btn-warning btn-sm" title="Edit Employee">
                                                    <i class="bi bi-pencil-square"></i>
                                                </a>
                                                <form action="{{ route('employees.destroy', $employee) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm" title="Delete Employee" onclick="return confirm('Are you sure you want to delete this employee?')">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        <div class="d-flex justify-content-center">
                            {{ $employees->links() }}
                        </div>
                    @else
                        <p class="text-muted">No employees found. <a href="{{ route('employees.create') }}">Create one now</a></p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

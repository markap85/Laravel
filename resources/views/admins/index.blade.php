@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>Admin Users</span>
                </div>

                <div class="card-body">
                    @if($admins->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Photo</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Company</th>
                                        <th>Account Email</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($admins as $admin)
                                        <tr>
                                            <td>
                                                @if($admin->profile_picture)
                                                    <img src="{{ asset('storage/' . $admin->profile_picture) }}" 
                                                         class="rounded-circle object-fit-cover" 
                                                         width="50" 
                                                         height="50" 
                                                         alt="{{ $admin->first_name }}">
                                                @elseif($admin->company && $admin->company->logo)
                                                    <img src="{{ asset('storage/' . $admin->company->logo) }}" 
                                                         class="rounded-circle object-fit-cover" 
                                                         width="50" 
                                                         height="50" 
                                                         alt="{{ $admin->company->name }}">
                                                @else
                                                    <div style="width: 50px; height: 50px;" 
                                                         class="bg-info rounded-circle d-flex align-items-center justify-content-center text-white">
                                                        <strong>{{ substr($admin->first_name, 0, 1) }}{{ substr($admin->last_name, 0, 1) }}</strong>
                                                    </div>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('employees.show', $admin) }}">
                                                    {{ $admin->first_name }} {{ $admin->last_name }}
                                                </a>
                                                @if($admin->user && $admin->user->email === 'admin@admin.com')
                                                    <span class="badge bg-danger ms-2">Master Admin</span>
                                                @endif
                                            </td>
                                            <td>{{ $admin->email ?? 'N/A' }}</td>
                                            <td>
                                                @if($admin->company)
                                                    <a href="{{ route('companies.show', $admin->company) }}">
                                                        {{ $admin->company->name }}
                                                    </a>
                                                @else
                                                    <span class="text-muted">N/A</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($admin->user)
                                                    {{ $admin->user->email }}
                                                @else
                                                    <span class="text-muted">No account</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($admin->user && $admin->user->email !== 'admin@admin.com')
                                                    <form action="{{ route('admins.demote', $admin) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-warning btn-sm" 
                                                                onclick="return confirm('Are you sure you want to demote this admin? Their user account will be deleted.')">
                                                            Demote
                                                        </button>
                                                    </form>
                                                @else
                                                    <span class="text-muted">Protected</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-3">
                            {{ $admins->links() }}
                        </div>
                    @else
                        <p class="text-center text-muted">No admin users found.</p>
                    @endif

                    <hr class="my-4">

                    <h5>Promote Employee to Admin</h5>
                    <p class="text-muted">Select an employee from companies with admin access enabled to grant them admin privileges:</p>
                    
                    @php
                        $nonAdminEmployees = \App\Models\Employee::with('company')
                            ->whereNull('user_id')
                            ->whereHas('company', function($query) {
                                $query->where('allow_admin', true);
                            })
                            ->get();
                    @endphp

                    @if($nonAdminEmployees->count() > 0)
                        <div class="list-group">
                            @foreach($nonAdminEmployees as $employee)
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <div class="d-flex align-items-center">
                                        <div class="me-3">
                                            @if($employee->profile_picture)
                                                <img src="{{ asset('storage/' . $employee->profile_picture) }}" 
                                                     class="rounded-circle object-fit-cover" 
                                                     width="40" 
                                                     height="40" 
                                                     alt="{{ $employee->first_name }}">
                                            @elseif($employee->company && $employee->company->logo)
                                                <img src="{{ asset('storage/' . $employee->company->logo) }}" 
                                                     class="rounded-circle object-fit-cover" 
                                                     width="40" 
                                                     height="40" 
                                                     alt="{{ $employee->company->name }}">
                                            @else
                                                <div style="width: 40px; height: 40px;" 
                                                     class="bg-secondary rounded-circle d-flex align-items-center justify-content-center text-white">
                                                    <strong>{{ substr($employee->first_name, 0, 1) }}{{ substr($employee->last_name, 0, 1) }}</strong>
                                                </div>
                                            @endif
                                        </div>
                                        <div>
                                            <strong>{{ $employee->first_name }} {{ $employee->last_name }}</strong>
                                            @if($employee->company)
                                                <br><small class="text-muted">{{ $employee->company->name }}</small>
                                            @endif
                                            @if($employee->email)
                                                <br><small class="text-muted">{{ $employee->email }}</small>
                                            @endif
                                        </div>
                                    </div>
                                    <form action="{{ route('admins.promote', $employee) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-success btn-sm" 
                                                onclick="return confirm('Are you sure you want to promote {{ $employee->first_name }} {{ $employee->last_name }} to admin? A user account will be created with the default password \'password\'.')">
                                            Promote to Admin
                                        </button>
                                    </form>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="alert alert-info">
                            <strong>No eligible employees.</strong>
                            <p class="mb-0">All employees are either already admins or belong to companies without admin access enabled.</p>
                            <p class="mb-0 mt-2">
                                <a href="{{ route('companies.index') }}" class="alert-link">Enable admin access for companies</a> to allow their employees to be promoted.
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

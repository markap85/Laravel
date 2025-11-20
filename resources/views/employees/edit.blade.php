@extends('layouts.app')

@section('content')
<div class="container">
    <x-breadcrumb :items="[
        ['title' => 'Employees', 'url' => route('employees.index')],
        ['title' => $employee->first_name . ' ' . $employee->last_name, 'url' => route('employees.show', $employee)],
        ['title' => 'Edit', 'url' => route('employees.edit', $employee)]
    ]" />
    
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-pencil-square"></i> Edit Employee</span>
                    <div>
                        <a href="{{ route('employees.show', $employee) }}" class="btn btn-info btn-sm me-1">
                            <i class="bi bi-eye"></i> View
                        </a>
                        <a href="{{ route('employees.index') }}" class="btn btn-secondary btn-sm">
                            <i class="bi bi-arrow-left"></i> Back to List
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <form action="{{ route('employees.update', $employee) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="form-group mb-3">
                            <label for="first_name">First Name <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control @error('first_name') is-invalid @enderror" 
                                   id="first_name" 
                                   name="first_name" 
                                   value="{{ old('first_name', $employee->first_name) }}" 
                                   required>
                            @error('first_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="last_name">Last Name <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control @error('last_name') is-invalid @enderror" 
                                   id="last_name" 
                                   name="last_name" 
                                   value="{{ old('last_name', $employee->last_name) }}" 
                                   required>
                            @error('last_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="company_id">Company</label>
                            <select name="company_id" 
                                    id="company_id" 
                                    class="form-control @error('company_id') is-invalid @enderror">
                                <option value="">-- No Company --</option>
                                @foreach($companies as $company)
                                    <option value="{{ $company->id }}" 
                                        {{ old('company_id', $employee->company_id) == $company->id ? 'selected' : '' }}>
                                        {{ $company->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('company_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="email">Email</label>
                            <input type="email" 
                                   class="form-control @error('email') is-invalid @enderror" 
                                   id="email" 
                                   name="email" 
                                   value="{{ old('email', $employee->email) }}">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="phone">Phone</label>
                            <input type="text" 
                                   class="form-control @error('phone') is-invalid @enderror" 
                                   id="phone" 
                                   name="phone" 
                                   value="{{ old('phone', $employee->phone) }}" 
                                   placeholder="+44 7123 456789">
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="profile_picture">Profile Picture (minimum 100x100 pixels)</label>
                            
                            @if($employee->profile_picture)
                                <div class="mb-2">
                                    <p class="mb-1">Current Profile Picture:</p>
                                    <img src="{{ asset('storage/' . $employee->profile_picture) }}" 
                                         alt="{{ $employee->first_name }}" 
                                         style="width: 100px; height: 100px; object-fit: cover;" 
                                         class="rounded-circle">
                                    
                                    <div class="mt-2">
                                        <button type="button" class="btn btn-sm btn-danger" onclick="if(confirm('Are you sure you want to delete this profile picture?')) { document.getElementById('delete-profile-picture-form').submit(); }">
                                            <i class="bi bi-trash"></i> Delete Profile Picture
                                        </button>
                                    </div>
                                </div>
                            @elseif($employee->company && $employee->company->logo)
                                <div class="mb-2">
                                    <p class="mb-1">Using Company Logo:</p>
                                    <img src="{{ asset('storage/' . $employee->company->logo) }}" 
                                         alt="{{ $employee->company->name }}" 
                                         style="width: 100px; height: 100px; object-fit: cover;" 
                                         class="rounded-circle">
                                </div>
                            @endif
                            
                            <input type="file" 
                                   class="form-control @error('profile_picture') is-invalid @enderror" 
                                   id="profile_picture" 
                                   name="profile_picture" 
                                   accept="image/*">
                            @error('profile_picture')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Leave empty to keep current picture. Accepted formats: JPEG, PNG, JPG, GIF. Minimum dimensions: 100x100 pixels.</small>
                        </div>

                        <div class="form-group mt-4">
                            <button type="submit" class="btn btn-primary">Update Employee</button>
                            <a href="{{ route('employees.index') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
            
            @if($employee->profile_picture)
            <!-- Hidden form for deleting profile picture (cannot be nested in main form) -->
            <form id="delete-profile-picture-form" action="{{ route('employees.deleteProfilePicture', $employee) }}" method="POST" style="display: none;">
                @csrf
            </form>
            @endif
        </div>
    </div>
</div>
@endsection

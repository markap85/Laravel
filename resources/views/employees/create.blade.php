@extends('layouts.app')

@section('content')
<div class="container">
    <x-breadcrumb :items="[
        ['title' => 'Employees', 'url' => route('employees.index')],
        ['title' => 'Create New Employee', 'url' => route('employees.create')]
    ]" />
    
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-person-add"></i> Create New Employee</span>
                    <a href="{{ route('employees.index') }}" class="btn btn-secondary btn-sm">
                        <i class="bi bi-arrow-left"></i> Back to List
                    </a>
                </div>

                <div class="card-body">
                    <form action="{{ route('employees.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="form-group mb-3">
                            <label for="first_name">First Name <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control @error('first_name') is-invalid @enderror" 
                                   id="first_name" 
                                   name="first_name" 
                                   value="{{ old('first_name') }}" 
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
                                   value="{{ old('last_name') }}" 
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
                                    <option value="{{ $company->id }}" {{ old('company_id') == $company->id ? 'selected' : '' }}>
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
                                   value="{{ old('email') }}">
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
                                   value="{{ old('phone') }}" 
                                   placeholder="+44 7123 456789">
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="profile_picture">Profile Picture (minimum 100x100 pixels)</label>
                            <input type="file" 
                                   class="form-control @error('profile_picture') is-invalid @enderror" 
                                   id="profile_picture" 
                                   name="profile_picture" 
                                   accept="image/*">
                            @error('profile_picture')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Accepted formats: JPEG, PNG, JPG, GIF. Minimum dimensions: 100x100 pixels.</small>
                        </div>

                        <div class="form-group mt-4">
                            <button type="submit" class="btn btn-primary">Create Employee</button>
                            <a href="{{ route('employees.index') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

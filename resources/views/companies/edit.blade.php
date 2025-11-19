@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Edit Company</div>

                <div class="card-body">
                    <form action="{{ route('companies.update', $company) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="form-group mb-3">
                            <label for="name">Company Name <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control @error('name') is-invalid @enderror" 
                                   id="name" 
                                   name="name" 
                                   value="{{ old('name', $company->name) }}" 
                                   required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="email">Email</label>
                            <input type="email" 
                                   class="form-control @error('email') is-invalid @enderror" 
                                   id="email" 
                                   name="email" 
                                   value="{{ old('email', $company->email) }}">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-3">
                            <label for="logo">Logo (minimum 100x100 pixels)</label>
                            
                            @if($company->logo)
                                <div class="mb-2">
                                    <p class="mb-1">Current Logo:</p>
                                    <img src="{{ asset('storage/' . $company->logo) }}" alt="{{ $company->name }}" style="max-width: 100px; max-height: 100px;">
                                    <div class="mt-2">
                                        <button type="button" class="btn btn-danger btn-sm" onclick="if(confirm('Are you sure you want to delete this logo? The company initials will be displayed instead.')) { document.getElementById('delete-logo-form').submit(); }">
                                            <i class="bi bi-trash"></i> Delete Logo
                                        </button>
                                    </div>
                                </div>
                            @endif
                            
                            <input type="file" 
                                   class="form-control @error('logo') is-invalid @enderror" 
                                   id="logo" 
                                   name="logo" 
                                   accept="image/*">
                            @error('logo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">Leave empty to keep current logo. Accepted formats: JPEG, PNG, JPG, GIF. Minimum dimensions: 100x100 pixels.</small>
                        </div>

                        <div class="form-group mb-3">
                            <label for="website">Website</label>
                            <input type="url" 
                                   class="form-control @error('website') is-invalid @enderror" 
                                   id="website" 
                                   name="website" 
                                   value="{{ old('website', $company->website) }}" 
                                   placeholder="https://example.com">
                            @error('website')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mt-4">
                            <button type="submit" class="btn btn-primary">Update Company</button>
                            <a href="{{ route('companies.index') }}" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
            
            @if($company->logo)
            <!-- Hidden form for deleting logo (cannot be nested in main form) -->
            <form id="delete-logo-form" action="{{ route('companies.deleteLogo', $company) }}" method="POST" style="display: none;">
                @csrf
            </form>
            @endif
        </div>
    </div>
</div>
@endsection

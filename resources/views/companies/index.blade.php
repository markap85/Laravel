@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>Companies</span>
                    <a href="{{ route('companies.create') }}" class="btn btn-primary btn-sm">Add New Company</a>
                </div>

                <div class="card-body">
                    @if($companies->count() > 0)
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Logo</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Website</th>
                                    <th>Admin Access</th>
                                    <th width="200px">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($companies as $company)
                                    <tr>
                                        <td>
                                            @if($company->logo)
                                                <img src="{{ asset('storage/' . $company->logo) }}" alt="{{ $company->name }}" style="width: 50px; height: 50px; object-fit: cover;" class="rounded-circle">
                                            @else
                                                <div style="width: 50px; height: 50px;" 
                                                     class="bg-primary rounded-circle d-flex align-items-center justify-content-center text-white">
                                                    <strong>{{ substr($company->name, 0, 1) }}</strong>
                                                </div>
                                            @endif
                                        </td>
                                        <td>{{ $company->name }}</td>
                                        <td>{{ $company->email ?? 'N/A' }}</td>
                                        <td>
                                            @if($company->website)
                                                <a href="{{ $company->website }}" target="_blank">{{ Str::limit($company->website, 30) }}</a>
                                            @else
                                                N/A
                                            @endif
                                        </td>
                                        <td>
                                            @if($company->allow_admin)
                                                <span class="badge bg-success">Enabled</span>
                                            @else
                                                <span class="badge bg-secondary">Disabled</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="d-flex gap-1 flex-wrap">
                                                <a href="{{ route('companies.show', $company) }}" class="btn btn-info btn-sm" title="View">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <a href="{{ route('companies.edit', $company) }}" class="btn btn-warning btn-sm" title="Edit">
                                                    <i class="bi bi-pencil-square"></i>
                                                </a>
                                                
                                                <form action="{{ route('companies.toggleAdmin', $company) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @if($company->allow_admin)
                                                        <button type="submit" class="btn btn-secondary btn-sm" 
                                                                title="Disable Admin Access"
                                                                onclick="return confirm('Disable admin access for {{ $company->name }}? All admin employees will be demoted.')">
                                                            <i class="bi bi-shield-x"></i>
                                                        </button>
                                                    @else
                                                        <button type="submit" class="btn btn-success btn-sm" title="Enable Admin Access">
                                                            <i class="bi bi-shield-check"></i>
                                                        </button>
                                                    @endif
                                                </form>
                                                
                                                <form action="{{ route('companies.destroy', $company) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm" 
                                                            title="Delete"
                                                            onclick="return confirm('Are you sure you want to delete this company?')">
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
                            {{ $companies->links() }}
                        </div>
                    @else
                        <p class="text-muted">No companies found. <a href="{{ route('companies.create') }}">Create one now</a></p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

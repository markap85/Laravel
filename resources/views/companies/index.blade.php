@extends('layouts.app')

@section('content')
<div class="container">
    <x-breadcrumb :items="[
        ['title' => 'Companies', 'url' => route('companies.index')]
    ]" />
    
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-building"></i> Companies</span>
                    <div class="d-flex gap-2">
                        <a href="{{ route('companies.export', request()->query()) }}" class="btn btn-success btn-sm no-print">
                            <i class="bi bi-file-earmark-spreadsheet"></i> Export CSV
                        </a>
                        <a href="{{ route('companies.create') }}" class="btn btn-primary btn-sm no-print">
                            <i class="bi bi-plus-circle"></i> Add New Company
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <!-- Search Bar -->
                    <div class="row mb-3 no-print">
                        <div class="col-md-12">
                            <form method="GET" action="{{ route('companies.index') }}" class="d-flex gap-2">
                                <div class="flex-grow-1">
                                    <input type="text" 
                                           name="search" 
                                           class="form-control" 
                                           placeholder="Search companies by name, email, or website..." 
                                           value="{{ request('search') }}">
                                </div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-search"></i> Search
                                </button>
                                @if(request('search'))
                                    <a href="{{ route('companies.index') }}" class="btn btn-secondary">
                                        <i class="bi bi-x-circle"></i> Clear
                                    </a>
                                @endif
                            </form>
                        </div>
                    </div>

                    @if($companies->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped sortable-table">
                                <thead>
                                    <tr>
                                        <th>Logo</th>
                                        <th data-sort="name" class="{{ request('sort') == 'name' ? 'sort-' . request('direction', 'asc') : '' }}">
                                            Name
                                        </th>
                                        <th data-sort="email" class="{{ request('sort') == 'email' ? 'sort-' . request('direction', 'asc') : '' }}">
                                            Email
                                        </th>
                                        <th data-sort="website" class="{{ request('sort') == 'website' ? 'sort-' . request('direction', 'asc') : '' }}">
                                            Website
                                        </th>
                                        <th>Admin Access</th>
                                        <th class="actions-column no-print">Actions</th>
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
                                                    <span class="badge bg-success"><i class="bi bi-check-circle"></i> Enabled</span>
                                                @else
                                                    <span class="badge bg-secondary"><i class="bi bi-x-circle"></i> Disabled</span>
                                                @endif
                                            </td>
                                            <td class="no-print">
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
                                                    
                                                    <form action="{{ route('companies.destroy', $company) }}" method="POST" class="d-inline" id="delete-form-{{ $company->id }}">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="button" class="btn btn-danger btn-sm" 
                                                                title="Delete"
                                                                onclick="if(confirm('Are you sure you want to delete {{ addslashes($company->name) }}? This action cannot be undone.')) { document.getElementById('delete-form-{{ $company->id }}').submit(); }">
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
                                Showing {{ $companies->firstItem() }} to {{ $companies->lastItem() }} of {{ $companies->total() }} companies
                            </div>
                            <div>
                                {{ $companies->links() }}
                            </div>
                        </div>
                    @else
                        <div class="empty-state">
                            <div class="empty-state-icon">
                                <i class="bi bi-building"></i>
                            </div>
                            <h3 class="empty-state-title">No Companies Found</h3>
                            <p class="empty-state-description">
                                @if(request('search'))
                                    No companies match your search criteria. Try adjusting your search terms.
                                @else
                                    Get started by adding your first company to the system.
                                @endif
                            </p>
                            @if(request('search'))
                                <a href="{{ route('companies.index') }}" class="btn btn-secondary">
                                    <i class="bi bi-x-circle"></i> Clear Search
                                </a>
                            @else
                                <a href="{{ route('companies.create') }}" class="btn btn-primary">
                                    <i class="bi bi-plus-circle"></i> Add Your First Company
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

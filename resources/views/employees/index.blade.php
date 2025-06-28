@extends('layouts.app')

@section('title', 'Employee Management - Employee Management System')

@push('styles')
<style>
    .employee-card {
        transition: transform 0.2s, box-shadow 0.2s;
        border: none;
        border-radius: 10px;
    }

    .employee-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    }

    .search-section {
        background: linear-gradient(135deg, #0e0e0e 0%, #101010 100%);
        border-radius: 10px;
        color: white;
    }

    .btn-add-employee {
        background: linear-gradient(135deg, #070707 0%, #000000 100%);
        border: none;
        font-weight: 600;
    }

    .btn-add-employee:hover {
        background: linear-gradient(135deg, #950d1c 0%, #aa3131 100%);
        transform: translateY(-1px);
    }

    .status-badge {
        font-size: 0.75rem;
        padding: 0.25rem 0.5rem;
    }

    .employee-avatar {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        color: white;
        font-size: 1.2rem;
    }

    .pagination {
        margin-bottom: 0;
    }

    .pagination .page-link {
        padding: 0.5rem 0.75rem;
        margin: 0 0.125rem;
        font-size: 0.875rem;
        border-radius: 6px;
        border: 1px solid #dee2e6;
        color: #fff;
        background-color: #101010;
        transition: all 0.15s ease-in-out;
    }

    .pagination .page-link:hover {
        background-color: #6a6c78;
        border-color: #667eea;
        color: #fff;
        transform: translateY(-1px);
    }

    .pagination .page-item.active .page-link {
        background-color: #6a6c78;
        border-color: #667eea;
        color: #fff;
        box-shadow: 0 2px 4px rgba(102, 126, 234, 0.3);
    }

    .pagination .page-item.disabled .page-link {
        color: #6c757d;
        background-color: #fff;
        border-color: #dee2e6;
        opacity: 0.5;
    }

    /* Fix oversized arrow buttons */
    .pagination .page-link[rel="prev"],
    .pagination .page-link[rel="next"] {
        font-size: 0.75rem;
        padding: 0.5rem 0.625rem;
    }

    /* Alternative: Replace arrows with icons */
    .pagination .page-link[rel="prev"]:before {
        content: "‹";
        font-size: 1.2rem;
        font-weight: bold;
    }

    .pagination .page-link[rel="next"]:before {
        content: "›";
        font-size: 1.2rem;
        font-weight: bold;
    }

    .pagination .page-link[rel="prev"],
    .pagination .page-link[rel="next"] {
        font-size: 0;
    }

    /* Responsive adjustments */
    @media (max-width: 576px) {
        .pagination .page-link {
            padding: 0.375rem 0.5rem;
            font-size: 0.75rem;
        }

        .pagination .page-link[rel="prev"],
        .pagination .page-link[rel="next"] {
            padding: 0.375rem 0.4rem;
        }
    }
</style>
@endpush

@section('content')
<!-- Navigation -->
<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
    <div class="container-fluid">
        <a class="navbar-brand" href="{{ route('dashboard') }}">
            <i class="bi bi-building me-2"></i>
            Employee Management System
        </a>

        <div class="navbar-nav ms-auto">
            <div class="nav-item dropdown">
                <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown">
                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-2"
                        style="width: 32px; height: 32px;">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    {{ auth()->user()->name }}
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <form method="POST" action="{{ route('logout') }}" class="d-inline">
                            @csrf
                            <button type="submit" class="dropdown-item text-danger">
                                <i class="bi bi-box-arrow-right me-2"></i>Logout
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</nav>

<!-- Main Content -->
<div class="container-fluid py-4" style="background-color: #f8f9fa; min-height: calc(100vh - 76px);">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
            <li class="breadcrumb-item active">Employees</li>
        </ol>
    </nav>

    <!-- Page Header -->

    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">
                        <i class="bi bi-people me-2"></i>Employee Management
                    </h1>
                    <p class="text-muted mb-0">Manage all employee records and information</p>
                </div>
                <div>
                    @if( auth()->user()->role == "hr_admin" || auth()->user()->role == "manager")
                    <a href="{{ route('employees.create') }}" class="btn btn-primary btn-add-employee">
                        <i class="bi bi-person-plus me-2"></i>Add New Employee
                    </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Success/Error Messages -->
    @if (session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        <i class="bi bi-check-circle-fill me-2"></i>
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <!-- Search and Filter Section -->
    <div class="card mb-4 border-0 shadow-sm">
        <div class="search-section p-4">
            <h5 class="mb-3">
                <i class="bi bi-search me-2"></i>Search & Filter Employees
            </h5>

            <form method="GET" action="{{ route('employees.index') }}">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="search" class="form-label text-white">Search by Name or Email</label>
                        <input type="text"
                            class="form-control"
                            id="search"
                            name="search"
                            value="{{ request('search') }}"
                            placeholder="Enter name or email">
                    </div>

                    <div class="col-md-3 mb-3">
                        <label for="department" class="form-label text-white">Department</label>
                        <select class="form-select" id="department" name="department">
                            <option value="">All Departments</option>
                            @foreach($departments as $department)
                            <option value="{{ $department->id }}"
                                {{ request('department') == $department->id ? 'selected' : '' }}>
                                {{ $department->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3 mb-3">
                        <label for="status" class="form-label text-white">Status</label>
                        <select class="form-select" id="status" name="status">
                            <option value="">All Status</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>

                    <div class="col-md-2 mb-3">
                        <label class="form-label text-white">&nbsp;</label>
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-light">
                                <i class="bi bi-search me-1"></i>Search
                            </button>
                        </div>
                    </div>
                </div>

                @if(request()->hasAny(['search', 'department', 'status']))
                <div class="mt-2">
                    <a href="{{ route('employees.index') }}" class="btn btn-outline-light btn-sm">
                        <i class="bi bi-x-circle me-1"></i>Clear Filters
                    </a>
                </div>
                @endif
            </form>
        </div>
    </div>

    <!-- Employee Statistics -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle bg-primary text-white p-2 me-3">
                            <i class="bi bi-people"></i>
                        </div>
                        <div>
                            <h4 class="mb-0">{{ $employees->total() }}</h4>
                            <small class="text-muted">Total Employees</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle bg-success text-white p-2 me-3">
                            <i class="bi bi-person-check"></i>
                        </div>
                        <div>
                            <h4 class="mb-0">{{ $employees->where('status', 'active')->count() }}</h4>
                            <small class="text-muted">Active</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle bg-warning text-white p-2 me-3">
                            <i class="bi bi-person-x"></i>
                        </div>
                        <div>
                            <h4 class="mb-0">{{ $employees->where('status', 'inactive')->count() }}</h4>
                            <small class="text-muted">Inactive</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle bg-info text-white p-2 me-3">
                            <i class="bi bi-building"></i>
                        </div>
                        <div>
                            <h4 class="mb-0">{{ $departments->count() }}</h4>
                            <small class="text-muted">Departments</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Employee List -->
    @if($employees->count() > 0)
    <div class="row">
        @foreach($employees as $employee)
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card employee-card h-100 shadow-sm">
                <div class="card-body">
                    <!-- Employee Header -->
                    <div class="d-flex align-items-center mb-3">
                        <div class="employee-avatar me-3"
                            style="background: linear-gradient(135deg, 
                                            {{ ['#667eea', '#f093fb', '#4facfe', '#43e97b', '#fa709a'][($employee->id % 5)] }} 0%, 
                                            {{ ['#764ba2', '#f093fb', '#00f2fe', '#40e0d0', '#c471f5'][($employee->id % 5)] }} 100%);">
                            {{ strtoupper(substr($employee->first_name, 0, 1) . substr($employee->last_name, 0, 1)) }}
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="card-title mb-1">
                                {{ $employee->first_name }} {{ $employee->last_name }}
                            </h6>
                            <p class="text-muted mb-0 small">{{ $employee->email }}</p>
                        </div>
                        <div>
                            <span class="badge status-badge {{ $employee->status == 'active' ? 'bg-success' : 'bg-secondary' }}">
                                {{ ucfirst($employee->status) }}
                            </span>
                        </div>
                    </div>

                    <!-- Employee Details -->
                    <div class="mb-3">
                        @if($employee->role)
                        <div class="d-flex align-items-center mb-2">
                            <i class="bi bi-person-badge text-primary me-2"></i>
                            <span class="small">{{ $employee->role->name }}</span>
                        </div>
                        @endif

                        @if($employee->department)
                        <div class="d-flex align-items-center mb-2">
                            <i class="bi bi-building text-info me-2"></i>
                            <span class="small">{{ $employee->department->name }}</span>
                        </div>
                        @endif

                        @if($employee->manager)
                        <div class="d-flex align-items-center mb-2">
                            <i class="bi bi-person-lines-fill text-warning me-2"></i>
                            <span class="small">Manager: {{ $employee->manager->first_name }} {{ $employee->manager->last_name }}</span>
                        </div>
                        @endif

                        <div class="d-flex align-items-center mb-2">
                            <i class="bi bi-calendar-event text-success me-2"></i>
                            <span class="small">Hired: {{ $employee->hire_date->format('M j, Y') }}</span>
                        </div>

                        @if($employee->phone)
                        <div class="d-flex align-items-center">
                            <i class="bi bi-telephone text-secondary me-2"></i>
                            <span class="small">{{ $employee->phone }}</span>
                        </div>
                        @endif
                    </div>

                    <!-- Action Buttons -->
                    <div class="d-flex gap-2">
                        <a href="{{ route('employees.show', $employee->id) }}"
                            class="btn btn-outline-primary btn-sm flex-fill">
                            <i class="bi bi-eye me-1"></i>View
                        </a>

                        @if( auth()->user()->role == "hr_admin" || auth()->user()->role == "manager")
                        <a href="{{ route('employees.edit', $employee->id) }}"
                            class="btn btn-outline-success btn-sm flex-fill">
                            <i class="bi bi-pencil me-1"></i>Edit
                        </a>
                        <form method="POST" action="{{ route('employees.destroy', $employee->id) }}"
                            class="d-inline flex-fill"
                            onsubmit="return confirm('Are you sure you want to delete {{ $employee->first_name }} {{ $employee->last_name }}?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger btn-sm w-100">
                                <i class="bi bi-trash me-1"></i>Delete
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-between align-items-center mt-4">
        <div>
            <small class="text-muted">
                Showing {{ $employees->firstItem() ?? 0 }} to {{ $employees->lastItem() ?? 0 }}
                of {{ $employees->total() }} results
            </small>
        </div>
        <div>
            @if ($employees->hasPages())
            <nav>
                <ul class="pagination pagination-sm mb-0">
                    @if ($employees->onFirstPage())
                    <li class="page-item disabled">
                        <span class="page-link"><i class="bi bi-chevron-left"></i></span>
                    </li>
                    @else
                    <li class="page-item">
                        <a class="page-link" href="{{ $employees->previousPageUrl() }}">
                            <i class="bi bi-chevron-left"></i>
                        </a>
                    </li>
                    @endif

                    @foreach ($employees->getUrlRange(max(1, $employees->currentPage() - 2), min($employees->lastPage(), $employees->currentPage() + 2)) as $page => $url)
                    @if ($page == $employees->currentPage())
                    <li class="page-item active"><span class="page-link">{{ $page }}</span></li>
                    @else
                    <li class="page-item"><a class="page-link" href="{{ $url }}">{{ $page }}</a></li>
                    @endif
                    @endforeach

                    @if ($employees->hasMorePages())
                    <li class="page-item">
                        <a class="page-link" href="{{ $employees->nextPageUrl() }}">
                            <i class="bi bi-chevron-right"></i>
                        </a>
                    </li>
                    @else
                    <li class="page-item disabled">
                        <span class="page-link"><i class="bi bi-chevron-right"></i></span>
                    </li>
                    @endif
                </ul>
            </nav>
            @endif
        </div>
    </div>
    @else
    <!-- Empty State -->
    <div class="card border-0 shadow-sm">
        <div class="card-body text-center py-5">
            <div class="mb-4">
                <i class="bi bi-people" style="font-size: 4rem; color: #dee2e6;"></i>
            </div>
            <h4 class="text-muted mb-3">No Employees Found</h4>
            <p class="text-muted mb-4">
                @if(request()->hasAny(['search', 'department', 'status']))
                No employees match your current filters. Try adjusting your search criteria.
                @else
                Get started by adding your first employee to the system.
                @endif
            </p>

            <div class="d-flex justify-content-center gap-3">
                @if(request()->hasAny(['search', 'department', 'status']))
                <a href="{{ route('employees.index') }}" class="btn btn-outline-primary">
                    <i class="bi bi-arrow-clockwise me-2"></i>Clear Filters
                </a>
                @endif
                <a href="{{ route('employees.create') }}" class="btn btn-primary btn-add-employee">
                    <i class="bi bi-person-plus me-2"></i>Add First Employee
                </a>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Auto-submit form when filters change
        const filterForm = document.querySelector('form[method="GET"]');
        const filterInputs = filterForm.querySelectorAll('select[name="department"], select[name="status"]');

        filterInputs.forEach(input => {
            input.addEventListener('change', function() {
                filterForm.submit();
            });
        });

        // Highlight search terms
        const searchTerm = '{{ request("search") }}';
        if (searchTerm) {
            const regex = new RegExp(`(${searchTerm})`, 'gi');
            const cards = document.querySelectorAll('.employee-card');

            cards.forEach(card => {
                const nameElement = card.querySelector('.card-title');
                const emailElement = card.querySelector('.text-muted');

                if (nameElement) {
                    nameElement.innerHTML = nameElement.textContent.replace(regex, '<mark>$1</mark>');
                }
                if (emailElement) {
                    emailElement.innerHTML = emailElement.textContent.replace(regex, '<mark>$1</mark>');
                }
            });
        }
    });
</script>
@endpush
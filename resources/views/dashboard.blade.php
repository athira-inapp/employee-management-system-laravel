@extends('layouts.app')

@section('title', 'Dashboard - Employee Management System')

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
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                    {{ $user->name }}
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <h6 class="dropdown-header">
                            <i class="bi bi-person-circle me-2"></i>Account Info
                        </h6>
                    </li>
                    <li><span class="dropdown-item-text"><strong>Role:</strong> {{ ucfirst($user->user_role) }}</span></li>
                    <li><span class="dropdown-item-text"><strong>Email:</strong> {{ $user->email }}</span></li>
                    <li><span class="dropdown-item-text"><strong>Last Login:</strong> {{ $user->last_login ? $user->last_login->format('M j, Y g:i A') : 'Never' }}</span></li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
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
<div class="container-fluid py-4">
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

    <!-- Welcome Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 bg-primary text-white">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col">
                            <h1 class="card-title mb-2">
                                Welcome back, {{ explode(' ', $user->name)[0] }}! 👋
                            </h1>
                            <p class="card-text mb-0">
                                <i class="bi bi-calendar3 me-2"></i>
                                Today is {{ now()->format('l, F j, Y') }}
                            </p>
                        </div>
                        <div class="col-auto">
                            <div class="rounded-circle bg-white bg-opacity-25 p-3">
                                <i class="bi bi-person-workspace" style="font-size: 2rem;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle bg-success text-white p-3 me-3">
                            <i class="bi bi-people"></i>
                        </div>
                        <div>
                            <h3 class="mb-0">{{ \App\Models\Employee::count() }}</h3>
                            <p class="text-muted mb-0">Total Employees</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle bg-info text-white p-3 me-3">
                            <i class="bi bi-building"></i>
                        </div>
                        <div>
                            <h3 class="mb-0">{{ \App\Models\Department::count() }}</h3>
                            <p class="text-muted mb-0">Departments</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle bg-warning text-white p-3 me-3">
                            <i class="bi bi-person-badge"></i>
                        </div>
                        <div>
                            <h3 class="mb-0">{{ \App\Models\Role::count() }}</h3>
                            <p class="text-muted mb-0">Roles</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle bg-danger text-white p-3 me-3">
                            <i class="bi bi-person-check"></i>
                        </div>
                        <div>
                            <h3 class="mb-0">{{ \App\Models\Employee::where('status', 'active')->count() }}</h3>
                            <p class="text-muted mb-0">Active Employees</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-lightning-charge me-2"></i>Quick Actions
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @if( auth()->user()->role == "hr_admin")
                        <div class="col-md-4 mb-3">
                            <a href="{{ route('employees.create') }}" class="btn btn-outline-primary w-100 p-3">
                                <i class="bi bi-person-plus d-block mb-2" style="font-size: 1.5rem;"></i>
                                Add New Employee
                            </a>
                        </div>
                        @endif

                        @if( auth()->user()->role != "hr_admin")
                        <div class="col-md-4 mb-3">
                            <a href="{{ route('employees.show', auth()->user()->employee_id) }}" class="btn btn-outline-primary w-100 p-3">
                                <i class="bi bi-person-plus d-block mb-2" style="font-size: 1.5rem;"></i>
                                My Profile
                            </a>
                        </div>
                        @endif
                        <div class="col-md-4 mb-3">
                            <a href="{{ route('employees.index') }}" class="btn btn-outline-success w-100 p-3">
                                <i class="bi bi-search d-block mb-2" style="font-size: 1.5rem;"></i>
                                View All Employees
                            </a>
                        </div>
                        <!-- <div class="col-md-4 mb-3">
                            <a href="#" class="btn btn-outline-info w-100 p-3">
                                <i class="bi bi-graph-up d-block mb-2" style="font-size: 1.5rem;"></i>
                                Generate Reports
                            </a>
                        </div> -->
                        <div class="col-md-4 mb-3">
                            <a href="{{ route('leave-requests.index') }}" class="btn btn-outline-warning w-100 p-3">
                                <i class="bi bi-calendar-x d-block mb-2" style="font-size: 1.5rem;"></i>
                                Leave Management
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@push('styles')
<style>
    .bg-primary {
        background-color: rgb(15 16 16) !important;
    }
</style>
@endpush
@extends('layouts.app')

@section('title', $employee->first_name . ' ' . $employee->last_name . ' - Employee Profile')

@push('styles')
<style>
    .profile-header {
        background: linear-gradient(135deg, #000000 0%, #000000 100%);
        color: white;
        border-radius: 15px;
        padding: 2rem;
        margin-bottom: 2rem;
    }

    .employee-avatar {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        color: white;
        font-size: 2.5rem;
        background: rgba(255, 255, 255, 0.2);
        border: 4px solid rgba(255, 255, 255, 0.3);
    }

    .info-card {
        background: #fff;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        margin-bottom: 1.5rem;
        border: none;
    }

    .info-card-header {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-bottom: 1px solid #dee2e6;
        padding: 1rem 1.5rem;
        border-radius: 10px 10px 0 0;
        font-weight: 600;
    }

    .status-badge {
        font-size: 0.9rem;
        padding: 0.5rem 1rem;
        border-radius: 25px;
    }

    .btn-action {
        padding: 10px 20px;
        border-radius: 8px;
        font-weight: 600;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .btn-edit {
        background: linear-gradient(135deg, #ffc107 0%, #ff8c00 100%);
        color: white;
        border: none;
    }

    .btn-edit:hover {
        background: linear-gradient(135deg, #e0a800 0%, #e07b00 100%);
        color: white;
        transform: translateY(-1px);
    }

    .subordinate-card {
        border: 1px solid #e9ecef;
        border-radius: 8px;
        padding: 1rem;
        margin-bottom: 1rem;
        transition: all 0.2s;
    }

    .subordinate-card:hover {
        border-color: #667eea;
        box-shadow: 0 2px 8px rgba(102, 126, 234, 0.15);
    }

    .timeline-item {
        border-left: 3px solid #667eea;
        padding-left: 1rem;
        margin-bottom: 1rem;
        position: relative;
    }

    .timeline-item::before {
        content: '';
        position: absolute;
        left: -6px;
        top: 0;
        width: 9px;
        height: 9px;
        background: #667eea;
        border-radius: 50%;
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
            <li class="breadcrumb-item"><a href="{{ route('employees.index') }}">Employees</a></li>
            <li class="breadcrumb-item active">{{ $employee->first_name }} {{ $employee->last_name }}</li>
        </ol>
    </nav>

    <!-- Success/Error Messages -->
    @if (session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        <i class="bi bi-check-circle-fill me-2"></i>
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <!-- Employee Profile Header -->
    <div class="profile-header">
        <div class="row align-items-center">
            <div class="col-auto">
                <div class="employee-avatar">
                    {{ strtoupper(substr($employee->first_name, 0, 1) . substr($employee->last_name, 0, 1)) }}
                </div>
            </div>
            <div class="col">
                <h1 class="mb-2">{{ $employee->first_name }} {{ $employee->last_name }}</h1>
                <div class="row">
                    <div class="col-md-6">
                        <p class="mb-1">
                            <i class="bi bi-envelope me-2"></i>{{ $employee->email }}
                        </p>
                        @if($employee->phone)
                        <p class="mb-1">
                            <i class="bi bi-telephone me-2"></i>{{ $employee->phone }}
                        </p>
                        @endif
                        @if($employee->role)
                        <p class="mb-1">
                            <i class="bi bi-person-badge me-2"></i>{{ $employee->role->name }}
                        </p>
                        @endif
                    </div>
                    <div class="col-md-6">
                        @if($employee->department)
                        <p class="mb-1">
                            <i class="bi bi-building me-2"></i>{{ $employee->department->name }}
                        </p>
                        @endif
                        <p class="mb-1">
                            <i class="bi bi-calendar-event me-2"></i>Hired: {{ $employee->hire_date->format('M j, Y') }}
                        </p>
                        <p class="mb-0">
                            <i class="bi bi-clock me-2"></i>{{ $employee->hire_date->diffForHumans() }}
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-auto">
                <div class="d-flex flex-column gap-2">
                    <span class="status-badge {{ $employee->status == 'active' ? 'bg-success' : 'bg-secondary' }} text-white">
                        <i class="bi bi-{{ $employee->status == 'active' ? 'check-circle' : 'x-circle' }} me-2"></i>
                        {{ ucfirst($employee->status) }}
                    </span>
                    <div class="d-flex gap-2">
                        @if( auth()->user()->role == "hr_admin" || auth()->user()->role == "manager")
                        <a href="{{ route('employees.edit', $employee->id) }}" class="btn btn-action btn-edit">
                            <i class="bi bi-pencil"></i>Edit
                        </a>
                        @endif

                        <a href="{{ route('employees.index') }}" class="btn btn-outline-light">
                            <i class="bi bi-arrow-left"></i>Back
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Left Column -->
        <div class="col-md-8">
            <!-- Personal Information -->
            <div class="info-card">
                <div class="info-card-header">
                    <i class="bi bi-person me-2"></i>Personal Information
                </div>
                <div class="card-body p-4">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="fw-bold text-muted">Full Name</label>
                                <p class="mb-0">{{ $employee->first_name }} {{ $employee->last_name }}</p>
                            </div>
                            <div class="mb-3">
                                <label class="fw-bold text-muted">Email Address</label>
                                <p class="mb-0">
                                    <a href="mailto:{{ $employee->email }}" class="text-decoration-none">
                                        {{ $employee->email }}
                                    </a>
                                </p>
                            </div>
                            <div class="mb-3">
                                <label class="fw-bold text-muted">Phone Number</label>
                                <p class="mb-0">
                                    @if($employee->phone)
                                    <a href="tel:{{ $employee->phone }}" class="text-decoration-none">
                                        {{ $employee->phone }}
                                    </a>
                                    @else
                                    <span class="text-muted">Not provided</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="fw-bold text-muted">Address</label>
                                <p class="mb-0">
                                    @if($employee->address)
                                    {{ $employee->address }}
                                    @else
                                    <span class="text-muted">Not provided</span>
                                    @endif
                                </p>
                            </div>
                            <div class="mb-3">
                                <label class="fw-bold text-muted">Employee Status</label>
                                <p class="mb-0">
                                    <span class="badge {{ $employee->status == 'active' ? 'bg-success' : 'bg-secondary' }}">
                                        {{ ucfirst($employee->status) }}
                                    </span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Employment Information -->
            <div class="info-card">
                <div class="info-card-header">
                    <i class="bi bi-briefcase me-2"></i>Employment Information
                </div>
                <div class="card-body p-4">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="fw-bold text-muted">Department</label>
                                <p class="mb-0">
                                    @if($employee->department)
                                    <span class="badge bg-info text-dark">
                                        <i class="bi bi-building me-1"></i>
                                        {{ $employee->department->name }}
                                    </span>
                                    @else
                                    <span class="text-muted">Not assigned</span>
                                    @endif
                                </p>
                            </div>
                            <div class="mb-3">
                                <label class="fw-bold text-muted">Job Role</label>
                                <p class="mb-0">
                                    @if($employee->role)
                                    <span class="badge bg-primary">
                                        <i class="bi bi-person-badge me-1"></i>
                                        {{ $employee->role->name }}
                                    </span>
                                    @else
                                    <span class="text-muted">Not assigned</span>
                                    @endif
                                </p>
                            </div>
                            <div class="mb-3">
                                <label class="fw-bold text-muted">Direct Manager</label>
                                <p class="mb-0">
                                    @if($employee->manager)
                                    <a href="{{ route('employees.show', $employee->manager->id) }}" class="text-decoration-none">
                                        <i class="bi bi-person-lines-fill me-1"></i>
                                        {{ $employee->manager->first_name }} {{ $employee->manager->last_name }}
                                    </a>
                                    @else
                                    <span class="text-muted">No direct manager</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="fw-bold text-muted">Hire Date</label>
                                <p class="mb-0">
                                    <i class="bi bi-calendar-event text-success me-2"></i>
                                    {{ $employee->hire_date->format('F j, Y') }}
                                </p>
                            </div>
                            <div class="mb-3">
                                <label class="fw-bold text-muted">Years of Service</label>
                                <p class="mb-0">
                                    <i class="bi bi-clock text-info me-2"></i>
                                    {{ $employee->hire_date->diffForHumans(null, true) }}
                                </p>
                            </div>
                            <div class="mb-3">
                                <label class="fw-bold text-muted">Current Salary</label>
                                <p class="mb-0">
                                    @if($employee->salary)
                                    <i class="bi bi-currency-dollar text-success me-1"></i>
                                    ${{ number_format($employee->salary, 2) }}
                                    @else
                                    <span class="text-muted">Not disclosed</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Team Members (Subordinates) -->
            @if($employee->subordinates->count() > 0)
            <div class="info-card">
                <div class="info-card-header">
                    <i class="bi bi-people me-2"></i>Direct Reports ({{ $employee->subordinates->count() }})
                </div>
                <div class="card-body p-4">
                    <div class="row">
                        @foreach($employee->subordinates as $subordinate)
                        <div class="col-md-6 mb-3">
                            <div class="subordinate-card">
                                <div class="d-flex align-items-center">
                                    <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center me-3"
                                        style="width: 40px; height: 40px;">
                                        {{ strtoupper(substr($subordinate->first_name, 0, 1) . substr($subordinate->last_name, 0, 1)) }}
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">
                                            <a href="{{ route('employees.show', $subordinate->id) }}" class="text-decoration-none">
                                                {{ $subordinate->first_name }} {{ $subordinate->last_name }}
                                            </a>
                                        </h6>
                                        <small class="text-muted">
                                            @if($subordinate->role)
                                            {{ $subordinate->role->name }}
                                            @endif
                                            @if($subordinate->department && $subordinate->role) • @endif
                                            @if($subordinate->department)
                                            {{ $subordinate->department->name }}
                                            @endif
                                        </small>
                                    </div>
                                    <div>
                                        <span class="badge {{ $subordinate->status == 'active' ? 'bg-success' : 'bg-secondary' }} badge-sm">
                                            {{ ucfirst($subordinate->status) }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Right Column -->
        <div class="col-md-4">
            <!-- Quick Stats -->
            <div class="info-card">
                <div class="info-card-header">
                    <i class="bi bi-graph-up me-2"></i>Quick Stats
                </div>
                <div class="card-body p-4">
                    <div class="row text-center">
                        <div class="col-12 mb-3">
                            <div class="bg-light rounded p-3">
                                <h4 class="mb-1 text-primary">{{ $employee->subordinates->count() }}</h4>
                                <small class="text-muted">Direct Reports</small>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="bg-light rounded p-3">
                                <h5 class="mb-1 text-success">
                                    {{ round($employee->hire_date->diffInYears(),0) }}
                                </h5>
                                <small class="text-muted">Years</small>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="bg-light rounded p-3">
                                <h5 class="mb-1 text-info">
                                    {{ $employee->hire_date->diffInMonths() % 12 }}
                                </h5>
                                <small class="text-muted">Months</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Timeline -->
            <div class="info-card">
                <div class="info-card-header">
                    <i class="bi bi-clock-history me-2"></i>Timeline
                </div>
                <div class="card-body p-4">
                    <div class="timeline-item">
                        <small class="text-muted">{{ $employee->updated_at->format('M j, Y') }}</small>
                        <p class="mb-0 fw-bold">Profile Updated</p>
                        <small class="text-muted">Last modification to employee record</small>
                    </div>
                    <div class="timeline-item">
                        <small class="text-muted">{{ $employee->hire_date->format('M j, Y') }}</small>
                        <p class="mb-0 fw-bold">Joined Company</p>
                        <small class="text-muted">Started as {{ $employee->role ? $employee->role->name : 'Employee' }}</small>
                    </div>
                    <div class="timeline-item">
                        <small class="text-muted">{{ $employee->created_at->format('M j, Y') }}</small>
                        <p class="mb-0 fw-bold">Record Created</p>
                        <small class="text-muted">Employee profile added to system</small>
                    </div>
                </div>
            </div>

            <!-- Account Information -->
            @if($employee->user)
            <div class="info-card">
                <div class="info-card-header">
                    <i class="bi bi-person-circle me-2"></i>Account Information
                </div>
                <div class="card-body p-4">
                    <div class="mb-3">
                        <label class="fw-bold text-muted">System Username</label>
                        <p class="mb-0">{{ $employee->user->username ?: $employee->user->email }}</p>
                    </div>
                    <div class="mb-3">
                        <label class="fw-bold text-muted">User Role</label>
                        <p class="mb-0">
                            <span class="badge bg-{{ $employee->user->user_role == 'admin' ? 'danger' : ($employee->user->user_role == 'manager' ? 'warning' : 'info') }}">
                                {{ ucfirst($employee->user->user_role) }}
                            </span>
                        </p>
                    </div>
                    <div class="mb-3">
                        <label class="fw-bold text-muted">Account Status</label>
                        <p class="mb-0">
                            <span class="badge {{ $employee->user->is_active ? 'bg-success' : 'bg-danger' }}">
                                {{ $employee->user->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </p>
                    </div>
                    @if($employee->user->last_login)
                    <div class="mb-3">
                        <label class="fw-bold text-muted">Last Login</label>
                        <p class="mb-0">{{ $employee->user->last_login->format('M j, Y g:i A') }}</p>
                    </div>
                    @endif
                </div>
            </div>
            @else
            <div class="info-card">
                <div class="info-card-header">
                    <i class="bi bi-person-x me-2"></i>Account Information
                </div>
                <div class="card-body p-4 text-center">
                    <i class="bi bi-person-x text-muted" style="font-size: 2rem;"></i>
                    <p class="text-muted mt-2 mb-0">No system account linked</p>
                    <small class="text-muted">Employee doesn't have login access</small>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Add smooth scroll to subordinate links
        const subordinateLinks = document.querySelectorAll('a[href*="employees"]');
        subordinateLinks.forEach(link => {
            link.addEventListener('click', function() {
                // Add loading state if needed
                this.innerHTML = '<i class="bi bi-arrow-repeat spin me-1"></i>' + this.textContent;
            });
        });

        // Copy email/phone to clipboard on click
        const emailLinks = document.querySelectorAll('a[href^="mailto:"]');
        const phoneLinks = document.querySelectorAll('a[href^="tel:"]');

        [...emailLinks, ...phoneLinks].forEach(link => {
            link.addEventListener('click', function(e) {
                e.preventDefault();
                const text = this.textContent;
                navigator.clipboard.writeText(text).then(() => {
                    // Show tooltip or notification
                    const originalText = this.innerHTML;
                    this.innerHTML = '<i class="bi bi-check me-1"></i>Copied!';
                    setTimeout(() => {
                        this.innerHTML = originalText;
                    }, 1500);
                });
            });
        });
    });
</script>
@endpush
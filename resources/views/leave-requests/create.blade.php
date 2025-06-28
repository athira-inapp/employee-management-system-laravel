@extends('layouts.app')

@section('title', 'Apply for Leave - Employee Management System')

@push('styles')
<style>
    .form-section {
        background: #fff;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        margin-bottom: 2rem;
    }

    .section-header {
        background: linear-gradient(135deg, #000000 0%, #000000 100%);
        color: white;
        padding: 1rem 1.5rem;
        border-radius: 10px 10px 0 0;
        margin: 0;
    }

    .required-field::after {
        content: ' *';
        color: #dc3545;
    }

    .form-control:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.25rem rgba(102, 126, 234, 0.25);
    }

    .btn-apply {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        border: none;
        padding: 12px 30px;
        font-weight: 600;
        color: white;
    }

    .btn-apply:hover {
        background: linear-gradient(135deg, #218838 0%, #1ba085 100%);
        transform: translateY(-1px);
        color: white;
    }

    .leave-info-card {
        background: linear-gradient(135deg, #e3f2fd 0%, #f3e5f5 100%);
        border: none;
        border-radius: 10px;
    }

    .days-display {
        font-size: 2rem;
        font-weight: bold;
        color: #667eea;
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
            <li class="breadcrumb-item"><a href="{{ route('leave-requests.index') }}">Leave Management</a></li>
            <li class="breadcrumb-item active">{{ $user->isHRAdmin() ? 'Create Leave Request' : 'Apply for Leave' }}</li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">
                        <i class="bi bi-calendar-plus me-2"></i>
                        {{ $user->isHRAdmin() ? 'Create Leave Request' : 'Apply for Leave' }}
                    </h1>
                    <p class="text-muted mb-0">
                        {{ $user->isHRAdmin() ? 'Create a leave request for any employee' : 'Submit your leave application for manager approval' }}
                    </p>
                </div>
                <div>
                    <a href="{{ route('leave-requests.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-2"></i>Back to Leave Requests
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Error/Success Messages -->
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

    <div class="row">
        <div class="col-md-8">
            <!-- Leave Application Form -->
            <form method="POST" action="{{ route('leave-requests.store') }}" id="leaveRequestForm">
                @csrf

                <!-- Employee Selection (HR Only) -->
                @if($user->isHRAdmin())
                <div class="form-section">
                    <h5 class="section-header">
                        <i class="bi bi-person me-2"></i>Employee Selection
                    </h5>
                    <div class="p-4">
                        <div class="mb-3">
                            <label for="employee_id" class="form-label required-field">Select Employee</label>
                            <select class="form-select @error('employee_id') is-invalid @enderror"
                                id="employee_id"
                                name="employee_id"
                                required>
                                <option value="">Choose Employee</option>
                                @foreach($employees as $employee)
                                <option value="{{ $employee->id }}"
                                    {{ old('employee_id') == $employee->id ? 'selected' : '' }}>
                                    {{ $employee->first_name }} {{ $employee->last_name }}
                                    @if($employee->department)
                                    ({{ $employee->department->name }})
                                    @endif
                                </option>
                                @endforeach
                            </select>
                            @error('employee_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                @endif

                <!-- Leave Details Section -->
                <div class="form-section">
                    <h5 class="section-header">
                        <i class="bi bi-calendar-event me-2"></i>Leave Details
                    </h5>
                    <div class="p-4">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="leave_type" class="form-label required-field">Leave Type</label>
                                <select class="form-select @error('leave_type') is-invalid @enderror"
                                    id="leave_type"
                                    name="leave_type"
                                    required>
                                    <option value="">Select Leave Type</option>
                                    @foreach(\App\Models\LeaveRequest::LEAVE_TYPES as $key => $value)
                                    <option value="{{ $key }}" {{ old('leave_type') == $key ? 'selected' : '' }}>
                                        {{ $value }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('leave_type')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="start_date" class="form-label required-field">Start Date</label>
                                <input type="date"
                                    class="form-control @error('start_date') is-invalid @enderror"
                                    id="start_date"
                                    name="start_date"
                                    value="{{ old('start_date') }}"
                                    min="{{ date('Y-m-d') }}"
                                    required>
                                @error('start_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="end_date" class="form-label required-field">End Date</label>
                                <input type="date"
                                    class="form-control @error('end_date') is-invalid @enderror"
                                    id="end_date"
                                    name="end_date"
                                    value="{{ old('end_date') }}"
                                    min="{{ date('Y-m-d') }}"
                                    required>
                                @error('end_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="reason" class="form-label required-field">Reason for Leave</label>
                            <textarea class="form-control @error('reason') is-invalid @enderror"
                                id="reason"
                                name="reason"
                                rows="4"
                                placeholder="Please provide a detailed reason for your leave request..."
                                required>{{ old('reason') }}</textarea>
                            @error('reason')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                <small id="reasonCounter">0/1000 characters</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="form-section">
                    <div class="p-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <small class="text-muted">
                                    <i class="bi bi-info-circle me-1"></i>
                                    Fields marked with <span class="text-danger">*</span> are required
                                </small>
                            </div>
                            <div class="d-flex gap-2">
                                <a href="{{ route('leave-requests.index') }}" class="btn btn-secondary">
                                    <i class="bi bi-x-circle me-2"></i>Cancel
                                </a>
                                <button type="submit" class="btn btn-apply">
                                    <i class="bi bi-send me-2"></i>Submit Request
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Right Sidebar -->
        <div class="col-md-4">
            <!-- Leave Summary -->
            <div class="leave-info-card mb-4">
                <div class="card-body p-4">
                    <h6 class="card-title mb-3">
                        <i class="bi bi-calculator me-2"></i>Leave Summary
                    </h6>

                    <div class="text-center mb-3">
                        <div class="days-display" id="totalDays">0</div>
                        <small class="text-muted">Total Days</small>
                    </div>

                    <div id="leaveSummary" style="display: none;">
                        <div class="row text-center">
                            <div class="col-6">
                                <div class="border-end">
                                    <div class="fw-bold" id="startDateDisplay">-</div>
                                    <small class="text-muted">Start Date</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="fw-bold" id="endDateDisplay">-</div>
                                <small class="text-muted">End Date</small>
                            </div>
                        </div>

                        <hr>

                        <div class="d-flex justify-content-between">
                            <span>Leave Type:</span>
                            <span id="leaveTypeDisplay" class="fw-bold">-</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Leave Guidelines -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light">
                    <h6 class="mb-0">
                        <i class="bi bi-lightbulb me-2"></i>Leave Guidelines
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <h6 class="fw-bold">Leave Types:</h6>
                        <ul class="list-unstyled small">
                            <li><i class="bi bi-dot text-primary"></i><strong>Sick Leave:</strong> For illness or medical appointments</li>
                            <li><i class="bi bi-dot text-primary"></i><strong>Vacation:</strong> For personal time and holidays</li>
                            <li><i class="bi bi-dot text-primary"></i><strong>Personal:</strong> For personal matters</li>
                            <li><i class="bi bi-dot text-primary"></i><strong>Emergency:</strong> For urgent situations</li>
                            <li><i class="bi bi-dot text-primary"></i><strong>Maternity/Paternity:</strong> For new parents</li>
                        </ul>
                    </div>

                    <div class="mb-3">
                        <h6 class="fw-bold">Important Notes:</h6>
                        <ul class="list-unstyled small">
                            <li><i class="bi bi-check-circle text-success me-1"></i>Submit requests at least 48 hours in advance</li>
                            <li><i class="bi bi-check-circle text-success me-1"></i>Provide detailed reason for leave</li>
                            <li><i class="bi bi-check-circle text-success me-1"></i>Check team calendar for conflicts</li>
                            <li><i class="bi bi-check-circle text-success me-1"></i>Manager approval required</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const startDateInput = document.getElementById('start_date');
        const endDateInput = document.getElementById('end_date');
        const leaveTypeSelect = document.getElementById('leave_type');
        const reasonTextarea = document.getElementById('reason');
        const reasonCounter = document.getElementById('reasonCounter');

        // Update end date minimum when start date changes
        startDateInput.addEventListener('change', function() {
            endDateInput.min = this.value;
            if (endDateInput.value && endDateInput.value < this.value) {
                endDateInput.value = this.value;
            }
            updateLeaveSummary();
        });

        // Update summary when end date changes
        endDateInput.addEventListener('change', updateLeaveSummary);

        // Update summary when leave type changes
        leaveTypeSelect.addEventListener('change', updateLeaveSummary);

        // Character counter for reason
        reasonTextarea.addEventListener('input', function() {
            const length = this.value.length;
            reasonCounter.textContent = `${length}/1000 characters`;
            reasonCounter.className = length > 1000 ? 'text-danger' : 'text-muted';
        });

        function updateLeaveSummary() {
            const startDate = startDateInput.value;
            const endDate = endDateInput.value;
            const leaveType = leaveTypeSelect.value;
            const leaveTypeText = leaveTypeSelect.options[leaveTypeSelect.selectedIndex].text;

            if (startDate && endDate) {
                const start = new Date(startDate);
                const end = new Date(endDate);
                const diffTime = Math.abs(end - start);
                const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;

                document.getElementById('totalDays').textContent = diffDays;
                document.getElementById('startDateDisplay').textContent = formatDate(start);
                document.getElementById('endDateDisplay').textContent = formatDate(end);
                document.getElementById('leaveTypeDisplay').textContent = leaveType ? leaveTypeText : '-';
                document.getElementById('leaveSummary').style.display = 'block';
            } else {
                document.getElementById('totalDays').textContent = '0';
                document.getElementById('leaveSummary').style.display = 'none';
            }
        }

        function formatDate(date) {
            return date.toLocaleDateString('en-US', {
                month: 'short',
                day: 'numeric'
            });
        }

        // Form validation
        document.getElementById('leaveRequestForm').addEventListener('submit', function(e) {
            const startDate = new Date(startDateInput.value);
            const endDate = new Date(endDateInput.value);
            const today = new Date();
            today.setHours(0, 0, 0, 0);

            if (startDate < today) {
                e.preventDefault();
                alert('Start date cannot be in the past.');
                return false;
            }

            if (endDate < startDate) {
                e.preventDefault();
                alert('End date cannot be before start date.');
                return false;
            }

            if (reasonTextarea.value.length > 1000) {
                e.preventDefault();
                alert('Reason cannot exceed 1000 characters.');
                return false;
            }
        });
    });
</script>
@endpush
@extends('layouts.app')

@section('title', 'Leave Request Details - Employee Management System')

@push('styles')
<style>
    .leave-header {
        background: linear-gradient(135deg, #000000 0%, #000000 100%);
        color: white;
        border-radius: 15px;
        padding: 2rem;
        margin-bottom: 2rem;
    }

    .status-badge {
        font-size: 1rem;
        padding: 0.5rem 1rem;
        border-radius: 25px;
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

    .btn-approve {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        border: none;
        color: white;
        padding: 10px 20px;
        font-weight: 600;
    }

    .btn-approve:hover {
        background: linear-gradient(135deg, #218838 0%, #1ba085 100%);
        color: white;
        transform: translateY(-1px);
    }

    .btn-reject {
        background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
        border: none;
        color: white;
        padding: 10px 20px;
        font-weight: 600;
    }

    .btn-reject:hover {
        background: linear-gradient(135deg, #c82333 0%, #a71e2a 100%);
        color: white;
        transform: translateY(-1px);
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

    .leave-calendar {
        background: linear-gradient(135deg, #e3f2fd 0%, #f3e5f5 100%);
        border-radius: 10px;
        padding: 1.5rem;
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
            <li class="breadcrumb-item active">Request #{{ $leaveRequest->id }}</li>
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

    @if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show">
        <i class="bi bi-exclamation-triangle-fill me-2"></i>
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <!-- Leave Request Header -->
    <div class="leave-header">
        <div class="row align-items-center">
            <div class="col">
                <div class="d-flex align-items-center mb-2">
                    <h1 class="mb-0 me-3">{{ $leaveRequest->leave_type_name }}</h1>
                    <span class="status-badge {{ $leaveRequest->status_badge_class }} text-white">
                        <i class="bi bi-{{ $leaveRequest->status_icon }} me-2"></i>
                        {{ ucfirst($leaveRequest->status) }}
                    </span>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <p class="mb-1">
                            <i class="bi bi-person me-2"></i>
                            <strong>{{ $leaveRequest->employee->first_name }} {{ $leaveRequest->employee->last_name }}</strong>
                        </p>
                        @if($leaveRequest->employee->department)
                        <p class="mb-1">
                            <i class="bi bi-building me-2"></i>{{ $leaveRequest->employee->department->name }}
                        </p>
                        @endif
                        @if($leaveRequest->employee->role)
                        <p class="mb-1">
                            <i class="bi bi-person-badge me-2"></i>{{ $leaveRequest->employee->role->name }}
                        </p>
                        @endif
                    </div>
                    <div class="col-md-6">
                        <p class="mb-1">
                            <i class="bi bi-calendar-event me-2"></i>
                            {{ $leaveRequest->start_date->format('M j, Y') }} - {{ $leaveRequest->end_date->format('M j, Y') }}
                        </p>
                        <p class="mb-1">
                            <i class="bi bi-clock me-2"></i>{{ $leaveRequest->days_requested }} days
                        </p>
                        <p class="mb-0">
                            <i class="bi bi-send me-2"></i>Submitted {{ $leaveRequest->request_date->format('M j, Y g:i A') }}
                        </p>
                    </div>
                </div>
            </div>
            <div class="col-auto">
                <div class="d-flex gap-2">
                    @if(($user->isManager() || $user->isHRAdmin()) &&
                    $leaveRequest->status === 'pending' &&
                    $leaveRequest->employee_id !== auth()->user()->employee?->id)
                    {{-- Managers can only approve/reject subordinates' requests, not their own --}}
                    <button type="button" class="btn btn-approve"
                        onclick="approveRequest({{ $leaveRequest->id }})">
                        <i class="bi bi-check-circle me-2"></i>Approve
                    </button>
                    <button type="button" class="btn btn-reject"
                        onclick="rejectRequest({{ $leaveRequest->id }})">
                        <i class="bi bi-x-circle me-2"></i>Reject
                    </button>
                    @elseif($user->isManager() &&
                    $leaveRequest->status === 'pending' &&
                    $leaveRequest->employee_id === auth()->user()->employee?->id)
                    {{-- Show message for manager's own pending request --}}
                    <div class="alert alert-info mb-0 py-2">
                        <i class="bi bi-info-circle me-2"></i>
                        <small>Your request is awaiting approval from your manager or HR.</small>
                    </div>
                    @endif
                    <a href="{{ route('leave-requests.index') }}" class="btn btn-outline-light">
                        <i class="bi bi-arrow-left me-2"></i>Back to List
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Left Column -->
        <div class="col-md-8">
            <!-- Leave Details -->
            <div class="info-card">
                <div class="info-card-header">
                    <i class="bi bi-info-circle me-2"></i>Leave Request Details
                </div>
                <div class="card-body p-4">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="fw-bold text-muted">Request ID</label>
                                <p class="mb-0">#{{ $leaveRequest->id }}</p>
                            </div>
                            <div class="mb-3">
                                <label class="fw-bold text-muted">Leave Type</label>
                                <p class="mb-0">
                                    <span class="badge bg-info">{{ $leaveRequest->leave_type_name }}</span>
                                </p>
                            </div>
                            <div class="mb-3">
                                <label class="fw-bold text-muted">Start Date</label>
                                <p class="mb-0">{{ $leaveRequest->start_date->format('l, F j, Y') }}</p>
                            </div>
                            <div class="mb-3">
                                <label class="fw-bold text-muted">End Date</label>
                                <p class="mb-0">{{ $leaveRequest->end_date->format('l, F j, Y') }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="fw-bold text-muted">Duration</label>
                                <p class="mb-0">
                                    <i class="bi bi-calendar-range text-primary me-2"></i>
                                    {{ $leaveRequest->days_requested }} day(s)
                                </p>
                            </div>
                            <div class="mb-3">
                                <label class="fw-bold text-muted">Status</label>
                                <p class="mb-0">
                                    <span class="badge {{ $leaveRequest->status_badge_class }}">
                                        <i class="bi bi-{{ $leaveRequest->status_icon }} me-1"></i>
                                        {{ ucfirst($leaveRequest->status) }}
                                    </span>
                                </p>
                            </div>
                            <div class="mb-3">
                                <label class="fw-bold text-muted">Submitted Date</label>
                                <p class="mb-0">{{ $leaveRequest->request_date->format('M j, Y g:i A') }}</p>
                            </div>
                            @if($leaveRequest->response_date)
                            <div class="mb-3">
                                <label class="fw-bold text-muted">Response Date</label>
                                <p class="mb-0">{{ $leaveRequest->response_date->format('M j, Y g:i A') }}</p>
                            </div>
                            @endif
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="fw-bold text-muted">Reason for Leave</label>
                        <div class="p-3 bg-light rounded">
                            <p class="mb-0">{{ $leaveRequest->reason }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Employee Information -->
            <div class="info-card">
                <div class="info-card-header">
                    <i class="bi bi-person me-2"></i>Employee Information
                </div>
                <div class="card-body p-4">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="fw-bold text-muted">Full Name</label>
                                <p class="mb-0">
                                    <a href="{{ route('employees.show', $leaveRequest->employee->id) }}"
                                        class="text-decoration-none">
                                        {{ $leaveRequest->employee->first_name }} {{ $leaveRequest->employee->last_name }}
                                    </a>
                                </p>
                            </div>
                            <div class="mb-3">
                                <label class="fw-bold text-muted">Email</label>
                                <p class="mb-0">
                                    <a href="mailto:{{ $leaveRequest->employee->email }}" class="text-decoration-none">
                                        {{ $leaveRequest->employee->email }}
                                    </a>
                                </p>
                            </div>
                            @if($leaveRequest->employee->phone)
                            <div class="mb-3">
                                <label class="fw-bold text-muted">Phone</label>
                                <p class="mb-0">
                                    <a href="tel:{{ $leaveRequest->employee->phone }}" class="text-decoration-none">
                                        {{ $leaveRequest->employee->phone }}
                                    </a>
                                </p>
                            </div>
                            @endif
                        </div>
                        <div class="col-md-6">
                            @if($leaveRequest->employee->department)
                            <div class="mb-3">
                                <label class="fw-bold text-muted">Department</label>
                                <p class="mb-0">
                                    <span class="badge bg-info">
                                        <i class="bi bi-building me-1"></i>
                                        {{ $leaveRequest->employee->department->name }}
                                    </span>
                                </p>
                            </div>
                            @endif
                            @if($leaveRequest->employee->role)
                            <div class="mb-3">
                                <label class="fw-bold text-muted">Job Role</label>
                                <p class="mb-0">
                                    <span class="badge bg-primary">
                                        <i class="bi bi-person-badge me-1"></i>
                                        {{ $leaveRequest->employee->role->name }}
                                    </span>
                                </p>
                            </div>
                            @endif
                            <div class="mb-3">
                                <label class="fw-bold text-muted">Years of Service</label>
                                <p class="mb-0">
                                    <i class="bi bi-clock text-info me-2"></i>
                                    {{ $leaveRequest->employee->hire_date->diffForHumans(null, true) }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Approval/Response Information -->
            @if($leaveRequest->status !== 'pending')
            <div class="info-card">
                <div class="info-card-header">
                    <i class="bi bi-{{ $leaveRequest->status === 'approved' ? 'check-circle' : 'x-circle' }} me-2"></i>
                    {{ $leaveRequest->status === 'approved' ? 'Approval' : 'Rejection' }} Details
                </div>
                <div class="card-body p-4">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="fw-bold text-muted">{{ $leaveRequest->status === 'approved' ? 'Approved' : 'Rejected' }} By</label>
                                <p class="mb-0">
                                    @if($leaveRequest->approver)
                                    <a href="{{ route('employees.show', $leaveRequest->approver->id) }}"
                                        class="text-decoration-none">
                                        {{ $leaveRequest->approver->first_name }} {{ $leaveRequest->approver->last_name }}
                                    </a>
                                    @else
                                    System
                                    @endif
                                </p>
                            </div>
                            <div class="mb-3">
                                <label class="fw-bold text-muted">Response Date</label>
                                <p class="mb-0">{{ $leaveRequest->response_date->format('M j, Y g:i A') }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="fw-bold text-muted">Status</label>
                                <p class="mb-0">
                                    <span class="badge {{ $leaveRequest->status_badge_class }}">
                                        <i class="bi bi-{{ $leaveRequest->status_icon }} me-1"></i>
                                        {{ ucfirst($leaveRequest->status) }}
                                    </span>
                                </p>
                            </div>
                        </div>
                    </div>

                    @if($leaveRequest->manager_comments)
                    <div class="mb-3">
                        <label class="fw-bold text-muted">Manager Comments</label>
                        <div class="p-3 bg-light rounded">
                            <p class="mb-0">{{ $leaveRequest->manager_comments }}</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            @endif
        </div>

        <!-- Right Column -->
        <div class="col-md-4">
            <!-- Calendar View -->
            <div class="leave-calendar mb-4">
                <h6 class="fw-bold mb-3">
                    <i class="bi bi-calendar-event me-2"></i>Leave Period
                </h6>

                <div class="text-center mb-3">
                    <div class="display-6 fw-bold text-primary">{{ $leaveRequest->days_requested }}</div>
                    <small class="text-muted">Days Requested</small>
                </div>

                <div class="row text-center">
                    <div class="col-6">
                        <div class="border-end">
                            <div class="fw-bold">{{ $leaveRequest->start_date->format('M j') }}</div>
                            <small class="text-muted">Start Date</small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="fw-bold">{{ $leaveRequest->end_date->format('M j') }}</div>
                        <small class="text-muted">End Date</small>
                    </div>
                </div>
            </div>

            <!-- Timeline -->
            <div class="info-card">
                <div class="info-card-header">
                    <i class="bi bi-clock-history me-2"></i>Request Timeline
                </div>
                <div class="card-body p-4">
                    @if($leaveRequest->response_date)
                    <div class="timeline-item">
                        <small class="text-muted">{{ $leaveRequest->response_date->format('M j, Y g:i A') }}</small>
                        <p class="mb-0 fw-bold">Request {{ ucfirst($leaveRequest->status) }}</p>
                        <small class="text-muted">
                            @if($leaveRequest->approver)
                            By {{ $leaveRequest->approver->first_name }} {{ $leaveRequest->approver->last_name }}
                            @else
                            By system
                            @endif
                        </small>
                    </div>
                    @endif

                    <div class="timeline-item">
                        <small class="text-muted">{{ $leaveRequest->request_date->format('M j, Y g:i A') }}</small>
                        <p class="mb-0 fw-bold">Request Submitted</p>
                        <small class="text-muted">Leave application submitted for review</small>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            @if($leaveRequest->status === 'pending' &&
            (($user->isEmployee() && $leaveRequest->employee_id === $user->employee->id) || $user->isHRAdmin()))
            <div class="info-card">
                <div class="info-card-header">
                    <i class="bi bi-gear me-2"></i>Actions
                </div>
                <div class="card-body p-4">
                    <div class="d-grid">
                        <form method="POST" action="{{ route('leave-requests.destroy', $leaveRequest->id) }}"
                            onsubmit="return confirm('Are you sure you want to delete this leave request?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger">
                                <i class="bi bi-trash me-2"></i>Delete Request
                            </button>
                        </form>
                    </div>
                    <small class="text-muted mt-2 d-block">
                        You can only delete pending requests.
                    </small>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Approval Modal -->
<div class="modal fade" id="approvalModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="approvalModalTitle">Approve Leave Request</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="approvalForm" method="POST">
                @csrf
                @method('PATCH')
                <div class="modal-body">
                    <input type="hidden" id="approvalStatus" name="status" value="">

                    <div class="mb-3">
                        <label class="fw-bold">Request Summary:</label>
                        <div class="p-3 bg-light rounded">
                            <p class="mb-1"><strong>Employee:</strong> {{ $leaveRequest->employee->first_name }} {{ $leaveRequest->employee->last_name }}</p>
                            <p class="mb-1"><strong>Leave Type:</strong> {{ $leaveRequest->leave_type_name }}</p>
                            <p class="mb-1"><strong>Duration:</strong> {{ $leaveRequest->start_date->format('M j') }} - {{ $leaveRequest->end_date->format('M j, Y') }} ({{ $leaveRequest->days_requested }} days)</p>
                            <p class="mb-0"><strong>Reason:</strong> {{ Str::limit($leaveRequest->reason, 100) }}</p>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="manager_comments" class="form-label">Comments (Optional)</label>
                        <textarea class="form-control" id="manager_comments" name="manager_comments" rows="3"
                            placeholder="Add any comments about this decision..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn" id="approvalSubmitBtn">Approve</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function approveRequest(requestId) {
        document.getElementById('approvalModalTitle').textContent = 'Approve Leave Request';
        document.getElementById('approvalStatus').value = 'approved';
        document.getElementById('approvalSubmitBtn').textContent = 'Approve Request';
        document.getElementById('approvalSubmitBtn').className = 'btn btn-approve';
        document.getElementById('approvalForm').action = `/leave-requests/${requestId}/status`;

        new bootstrap.Modal(document.getElementById('approvalModal')).show();
    }

    function rejectRequest(requestId) {
        document.getElementById('approvalModalTitle').textContent = 'Reject Leave Request';
        document.getElementById('approvalStatus').value = 'rejected';
        document.getElementById('approvalSubmitBtn').textContent = 'Reject Request';
        document.getElementById('approvalSubmitBtn').className = 'btn btn-reject';
        document.getElementById('approvalForm').action = `/leave-requests/${requestId}/status`;

        new bootstrap.Modal(document.getElementById('approvalModal')).show();
    }
</script>
@endpush
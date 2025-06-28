@extends('layouts.app')

@section('title', 'Leave Management - Employee Management System')

@push('styles')
<style>
    .leave-card {
        transition: transform 0.2s, box-shadow 0.2s;
        border: none;
        border-radius: 10px;
        border-left: 4px solid;
    }

    .leave-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
    }

    .leave-card.pending {
        border-left-color: #ffc107;
    }

    .leave-card.approved {
        border-left-color: #28a745;
    }

    .leave-card.rejected {
        border-left-color: #dc3545;
    }

    .filter-section {
        background: linear-gradient(135deg, #0c0c0d 0%, #0d0d0d 100%);
        border-radius: 10px;
        color: white;
    }

    .btn-approve {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        border: none;
        color: white;
    }

    .btn-reject {
        background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
        border: none;
        color: white;
    }

    .btn-primary {
        background: black !important;
        border: 1px black !important;
    }

    .leave-type-badge {
        font-size: 0.75rem;
        padding: 0.25rem 0.75rem;
        border-radius: 15px;
    }

    /* Custom tab styling */
    .nav-tabs .nav-link {
        border: none;
        background: transparent;
        color: #6c757d;
        font-weight: 500;
        padding: 1rem 1.5rem;
        border-radius: 10px 10px 0 0;
    }

    .nav-tabs .nav-link.active {
        background: linear-gradient(135deg, #000000 0%, #040404 100%);
        color: white;
        border: none;
    }

    .nav-tabs .nav-link:hover {
        background-color: #f8f9fa;
        border: none;
    }

    .nav-tabs .nav-link.active:hover {
        background: linear-gradient(135deg, #473e3e 0%, #8a6969 100%);
        color: white;
    }

    .tab-content {
        background: white;
        border-radius: 0 0 10px 10px;
        min-height: 200px;
    }

    .badge-count {
        background: rgba(255, 255, 255, 0.2);
        color: white;
        font-size: 0.7rem;
        padding: 0.2rem 0.5rem;
        border-radius: 10px;
        margin-left: 0.5rem;
    }

    .pagination .page-link {
        font-size: 0.875rem;
        padding: 0.5rem 0.75rem;
        margin: 0 0.125rem;
        border-radius: 6px;
        color: #667eea;
    }

    .pagination .page-link:hover {
        background-color: #667eea;
        color: #fff;
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
            <li class="breadcrumb-item active">Leave Management</li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">
                        <i class="bi bi-calendar-x me-2"></i>Leave Management
                    </h1>
                    <p class="text-muted mb-0">
                        @if($user->isEmployee())
                        Manage your leave requests and view status
                        @elseif($user->isManager())
                        Manage your leave requests and approve team requests
                        @else
                        Comprehensive leave management for all employees
                        @endif
                    </p>
                </div>
                <div>

                    <a href="{{ route('leave-requests.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-2"></i>
                        {{ $user->isHRAdmin() ? 'Create Leave Request' : 'Apply for Leave' }}
                    </a>
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

    <!-- Tab Navigation -->
    <div class="card border-0 shadow-sm">
        @php
        $myRequests = $leaveRequests->where('employee_id', auth()->user()->employee?->id);
        $teamRequests = $leaveRequests->where('employee_id', '!=', auth()->user()->employee?->id);
        $pendingTeamRequests = $teamRequests->where('status', 'pending');
        @endphp

        <ul class="nav nav-tabs" id="leaveRequestTabs" role="tablist">
            <!-- My Leave Requests Tab -->
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="my-requests-tab" data-bs-toggle="tab" data-bs-target="#my-requests"
                    type="button" role="tab" aria-controls="my-requests" aria-selected="true">
                    <i class="bi bi-person me-2"></i>My Leave Requests
                    <span class="badge-count">{{ $myRequests->count() }}</span>
                </button>
            </li>

            <!-- Team Management Tab (Only for Managers and HR) -->
            @if($user->isManager() || $user->isHRAdmin())
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="team-requests-tab" data-bs-toggle="tab" data-bs-target="#team-requests"
                    type="button" role="tab" aria-controls="team-requests" aria-selected="false">
                    <i class="bi bi-people me-2"></i>
                    {{ $user->isHRAdmin() ? 'All Employee Requests' : 'Team Requests' }}
                    <span class="badge-count">{{ $teamRequests->count() }}</span>
                    @if($pendingTeamRequests->count() > 0)
                    <span class="badge bg-warning text-dark ms-1">{{ $pendingTeamRequests->count() }} pending</span>
                    @endif
                </button>
            </li>
            @endif
        </ul>

        <div class="tab-content" id="leaveRequestTabContent">
            <!-- My Leave Requests Tab Pane -->
            <div class="tab-pane fade show active" id="my-requests" role="tabpanel" aria-labelledby="my-requests-tab">
                <div class="p-4">
                    <!-- My Requests Filter Section -->
                    <div class="filter-section p-3 mb-4">
                        <h6 class="mb-3">
                            <i class="bi bi-funnel me-2"></i>Filter My Requests
                        </h6>

                        <form method="GET" action="{{ route('leave-requests.index') }}" id="myRequestsFilter">
                            <input type="hidden" name="tab" value="my-requests">
                            <div class="row">
                                <div class="col-md-3 mb-2">
                                    <select class="form-select form-select-sm" name="my_status">
                                        <option value="">All Status</option>
                                        <option value="pending" {{ request('my_status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="approved" {{ request('my_status') == 'approved' ? 'selected' : '' }}>Approved</option>
                                        <option value="rejected" {{ request('my_status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                    </select>
                                </div>
                                <div class="col-md-3 mb-2">
                                    <select class="form-select form-select-sm" name="my_leave_type">
                                        <option value="">All Types</option>
                                        @foreach(\App\Models\LeaveRequest::LEAVE_TYPES as $key => $value)
                                        <option value="{{ $key }}" {{ request('my_leave_type') == $key ? 'selected' : '' }}>
                                            {{ $value }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2 mb-2">
                                    <input type="date" class="form-control form-control-sm" name="my_date_from"
                                        value="{{ request('my_date_from') }}" placeholder="From Date">
                                </div>
                                <div class="col-md-2 mb-2">
                                    <input type="date" class="form-control form-control-sm" name="my_date_to"
                                        value="{{ request('my_date_to') }}" placeholder="To Date">
                                </div>
                                <div class="col-md-2 mb-2">
                                    <button type="submit" class="btn btn-light btn-sm w-100">
                                        <i class="bi bi-search me-1"></i>Filter
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- My Requests Statistics -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card border-0 bg-warning bg-opacity-10">
                                <div class="card-body text-center">
                                    <h4 class="text-warning">{{ $myRequests->where('status', 'pending')->count() }}</h4>
                                    <small class="text-muted">Pending</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card border-0 bg-success bg-opacity-10">
                                <div class="card-body text-center">
                                    <h4 class="text-success">{{ $myRequests->where('status', 'approved')->count() }}</h4>
                                    <small class="text-muted">Approved</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card border-0 bg-danger bg-opacity-10">
                                <div class="card-body text-center">
                                    <h4 class="text-danger">{{ $myRequests->where('status', 'rejected')->count() }}</h4>
                                    <small class="text-muted">Rejected</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card border-0 bg-info bg-opacity-10">
                                <div class="card-body text-center">
                                    <h4 class="text-info">{{ $myRequests->sum('days_requested') }}</h4>
                                    <small class="text-muted">Total Days</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- My Leave Requests List -->
                    @if($myRequests->count() > 0)
                    <div class="row">
                        @foreach($myRequests as $request)
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="card leave-card {{ $request->status }} h-100 shadow-sm">
                                <div class="card-body">
                                    <!-- Request Header -->
                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <div>
                                            <h6 class="card-title mb-1">{{ $request->leave_type_name }}</h6>
                                            <small class="text-muted">{{ $request->request_date->format('M j, Y') }}</small>
                                        </div>
                                        <div>
                                            <span class="badge {{ $request->status_badge_class }}">
                                                <i class="bi bi-{{ $request->status_icon }} me-1"></i>
                                                {{ ucfirst($request->status) }}
                                            </span>
                                        </div>
                                    </div>

                                    <!-- Leave Details -->
                                    <div class="mb-3">
                                        <div class="d-flex align-items-center mb-2">
                                            <i class="bi bi-calendar-range text-primary me-2"></i>
                                            <span class="small">
                                                {{ $request->start_date->format('M j') }} - {{ $request->end_date->format('M j, Y') }}
                                            </span>
                                        </div>

                                        <div class="d-flex align-items-center mb-2">
                                            <i class="bi bi-clock text-info me-2"></i>
                                            <span class="small">{{ $request->days_requested }} day(s)</span>
                                        </div>

                                        <div class="mb-2">
                                            <small class="text-muted">Reason:</small>
                                            <p class="small mb-0">{{ Str::limit($request->reason, 80) }}</p>
                                        </div>
                                    </div>

                                    <!-- Approval Info -->
                                    @if($request->status !== 'pending' && $request->approver)
                                    <div class="mb-3 p-2 bg-light rounded">
                                        <small class="text-muted">
                                            {{ ucfirst($request->status) }} by {{ $request->approver->first_name }} {{ $request->approver->last_name }}
                                            on {{ $request->response_date->format('M j, Y') }}
                                        </small>
                                        @if($request->manager_comments)
                                        <div class="mt-1">
                                            <small class="text-muted">Comments: {{ $request->manager_comments }}</small>
                                        </div>
                                        @endif
                                    </div>
                                    @endif

                                    <!-- Action Buttons -->
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('leave-requests.show', $request->id) }}"
                                            class="btn btn-outline-primary btn-sm flex-fill">
                                            <i class="bi bi-eye me-1"></i>View
                                        </a>

                                        @if($request->status === 'pending')
                                        <form method="POST" action="{{ route('leave-requests.destroy', $request->id) }}"
                                            class="d-inline flex-fill"
                                            onsubmit="return confirm('Are you sure you want to delete this leave request?')">
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
                    @else
                    <!-- Empty State for My Requests -->
                    <div class="text-center py-5">
                        <i class="bi bi-calendar-x text-muted" style="font-size: 3rem;"></i>
                        <h5 class="text-muted mt-3">No Leave Requests Found</h5>
                        <p class="text-muted">You haven't submitted any leave requests yet.</p>
                        <a href="{{ route('leave-requests.create') }}" class="btn btn-primary">
                            <i class="bi bi-plus-circle me-2"></i>Apply for Leave
                        </a>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Team Requests Tab Pane -->
            @if($user->isManager() || $user->isHRAdmin())
            <div class="tab-pane fade" id="team-requests" role="tabpanel" aria-labelledby="team-requests-tab">
                <div class="p-4">
                    <!-- Team Requests Filter Section -->
                    <div class="filter-section p-3 mb-4">
                        <h6 class="mb-3">
                            <i class="bi bi-funnel me-2"></i>Filter {{ $user->isHRAdmin() ? 'Employee' : 'Team' }} Requests
                        </h6>

                        <form method="GET" action="{{ route('leave-requests.index') }}" id="teamRequestsFilter">
                            <input type="hidden" name="tab" value="team-requests">
                            <div class="row">
                                <div class="col-md-2 mb-2">
                                    <select class="form-select form-select-sm" name="team_status">
                                        <option value="">All Status</option>
                                        <option value="pending" {{ request('team_status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="approved" {{ request('team_status') == 'approved' ? 'selected' : '' }}>Approved</option>
                                        <option value="rejected" {{ request('team_status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                    </select>
                                </div>
                                <div class="col-md-2 mb-2">
                                    <select class="form-select form-select-sm" name="team_leave_type">
                                        <option value="">All Types</option>
                                        @foreach(\App\Models\LeaveRequest::LEAVE_TYPES as $key => $value)
                                        <option value="{{ $key }}" {{ request('team_leave_type') == $key ? 'selected' : '' }}>
                                            {{ $value }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                                @if($employees->count() > 0)
                                <div class="col-md-2 mb-2">
                                    <select class="form-select form-select-sm" name="team_employee">
                                        <option value="">All Employees</option>
                                        @foreach($employees as $emp)
                                        <option value="{{ $emp->id }}" {{ request('team_employee') == $emp->id ? 'selected' : '' }}>
                                            {{ $emp->first_name }} {{ $emp->last_name }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                                @endif
                                <div class="col-md-2 mb-2">
                                    <input type="date" class="form-control form-control-sm" name="team_date_from"
                                        value="{{ request('team_date_from') }}">
                                </div>
                                <div class="col-md-2 mb-2">
                                    <input type="date" class="form-control form-control-sm" name="team_date_to"
                                        value="{{ request('team_date_to') }}">
                                </div>
                                <div class="col-md-2 mb-2">
                                    <button type="submit" class="btn btn-light btn-sm w-100">
                                        <i class="bi bi-search me-1"></i>Filter
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Team Requests Statistics -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card border-0 bg-warning bg-opacity-10">
                                <div class="card-body text-center">
                                    <h4 class="text-warning">{{ $pendingTeamRequests->count() }}</h4>
                                    <small class="text-muted">Pending Approval</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card border-0 bg-success bg-opacity-10">
                                <div class="card-body text-center">
                                    <h4 class="text-success">{{ $teamRequests->where('status', 'approved')->count() }}</h4>
                                    <small class="text-muted">Approved</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card border-0 bg-danger bg-opacity-10">
                                <div class="card-body text-center">
                                    <h4 class="text-danger">{{ $teamRequests->where('status', 'rejected')->count() }}</h4>
                                    <small class="text-muted">Rejected</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card border-0 bg-info bg-opacity-10">
                                <div class="card-body text-center">
                                    <h4 class="text-info">{{ $teamRequests->sum('days_requested') }}</h4>
                                    <small class="text-muted">Total Days</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Team Leave Requests List -->
                    @if($teamRequests->count() > 0)
                    <div class="row">
                        @foreach($teamRequests as $request)
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="card leave-card {{ $request->status }} h-100 shadow-sm">
                                <div class="card-body">
                                    <!-- Request Header -->
                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                        <div>
                                            <h6 class="card-title mb-1">{{ $request->employee->first_name }} {{ $request->employee->last_name }}</h6>
                                            <small class="text-muted">
                                                {{ $request->leave_type_name }} • {{ $request->request_date->format('M j, Y') }}
                                            </small>
                                        </div>
                                        <div>
                                            <span class="badge {{ $request->status_badge_class }}">
                                                <i class="bi bi-{{ $request->status_icon }} me-1"></i>
                                                {{ ucfirst($request->status) }}
                                            </span>
                                        </div>
                                    </div>

                                    <!-- Leave Details -->
                                    <div class="mb-3">
                                        <div class="d-flex align-items-center mb-2">
                                            <i class="bi bi-calendar-range text-primary me-2"></i>
                                            <span class="small">
                                                {{ $request->start_date->format('M j') }} - {{ $request->end_date->format('M j, Y') }}
                                            </span>
                                        </div>

                                        <div class="d-flex align-items-center mb-2">
                                            <i class="bi bi-clock text-info me-2"></i>
                                            <span class="small">{{ $request->days_requested }} day(s)</span>
                                        </div>

                                        @if($request->employee->department)
                                        <div class="d-flex align-items-center mb-2">
                                            <i class="bi bi-building text-secondary me-2"></i>
                                            <span class="small">{{ $request->employee->department->name }}</span>
                                        </div>
                                        @endif

                                        <div class="mb-2">
                                            <small class="text-muted">Reason:</small>
                                            <p class="small mb-0">{{ Str::limit($request->reason, 80) }}</p>
                                        </div>
                                    </div>

                                    <!-- Action Buttons -->
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('leave-requests.show', $request->id) }}"
                                            class="btn btn-outline-primary btn-sm flex-fill">
                                            <i class="bi bi-eye me-1"></i>View
                                        </a>

                                        @if($request->status === 'pending')
                                        <button type="button" class="btn btn-approve btn-sm flex-fill"
                                            onclick="approveRequest({{ $request->id }})">
                                            <i class="bi bi-check me-1"></i>Approve
                                        </button>
                                        <button type="button" class="btn btn-reject btn-sm flex-fill"
                                            onclick="rejectRequest({{ $request->id }})">
                                            <i class="bi bi-x me-1"></i>Reject
                                        </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <!-- Empty State for Team Requests -->
                    <div class="text-center py-5">
                        <i class="bi bi-people text-muted" style="font-size: 3rem;"></i>
                        <h5 class="text-muted mt-3">No {{ $user->isHRAdmin() ? 'Employee' : 'Team' }} Requests</h5>
                        <p class="text-muted">No leave requests to review at this time.</p>
                    </div>
                    @endif
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
        document.getElementById('approvalSubmitBtn').textContent = 'Approve';
        document.getElementById('approvalSubmitBtn').className = 'btn btn-success';
        document.getElementById('approvalForm').action = `/leave-requests/${requestId}/status`;

        new bootstrap.Modal(document.getElementById('approvalModal')).show();
    }

    function rejectRequest(requestId) {
        document.getElementById('approvalModalTitle').textContent = 'Reject Leave Request';
        document.getElementById('approvalStatus').value = 'rejected';
        document.getElementById('approvalSubmitBtn').textContent = 'Reject';
        document.getElementById('approvalSubmitBtn').className = 'btn btn-danger';
        document.getElementById('approvalForm').action = `/leave-requests/${requestId}/status`;

        new bootstrap.Modal(document.getElementById('approvalModal')).show();
    }

    // Handle tab switching and preserve active tab
    document.addEventListener('DOMContentLoaded', function() {
        // Check URL for active tab
        const urlParams = new URLSearchParams(window.location.search);
        const activeTab = urlParams.get('tab');

        if (activeTab === 'team-requests') {
            document.getElementById('my-requests-tab').classList.remove('active');
            document.getElementById('my-requests').classList.remove('show', 'active');
            document.getElementById('team-requests-tab').classList.add('active');
            document.getElementById('team-requests').classList.add('show', 'active');
        }

        // Auto-submit forms when filters change
        const filterSelects = document.querySelectorAll('select[name*="status"], select[name*="leave_type"], select[name*="employee"]');
        filterSelects.forEach(select => {
            select.addEventListener('change', function() {
                this.closest('form').submit();
            });
        });
    });
</script>
@endpush
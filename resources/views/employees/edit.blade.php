@extends('layouts.app')

@section('title', 'Edit Employee - Employee Management System')

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

    .btn-update {
        background: linear-gradient(135deg, #ffc107 0%, #ff8c00 100%);
        border: none;
        padding: 12px 30px;
        font-weight: 600;
        color: white;
    }

    .btn-update:hover {
        background: linear-gradient(135deg, #e0a800 0%, #e07b00 100%);
        transform: translateY(-1px);
        color: white;
    }

    .btn-cancel {
        background: linear-gradient(135deg, #6c757d 0%, #5a6268 100%);
        border: none;
        padding: 12px 30px;
        font-weight: 600;
    }

    .employee-info-header {
        background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);
        color: white;
        padding: 1rem;
        border-radius: 10px;
        margin-bottom: 2rem;
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
            <li class="breadcrumb-item active">Edit {{ $employee->first_name }} {{ $employee->last_name }}</li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="employee-info-header">
        <div class="row align-items-center">
            <div class="col">
                <h1 class="h3 mb-2">
                    <i class="bi bi-person-gear me-2"></i>Edit Employee
                </h1>
                <p class="mb-0">
                    <strong>{{ $employee->first_name }} {{ $employee->last_name }}</strong>
                    ({{ $employee->email }})
                    @if($employee->department)
                    • {{ $employee->department->name }}
                    @endif
                    @if($employee->role)
                    • {{ $employee->role->name }}
                    @endif
                </p>
            </div>
            <div class="col-auto">
                <div class="d-flex gap-2">
                    <a href="{{ route('employees.show', $employee->id) }}" class="btn btn-light">
                        <i class="bi bi-eye me-2"></i>View Profile
                    </a>
                    <a href="{{ route('employees.index') }}" class="btn btn-outline-light">
                        <i class="bi bi-arrow-left me-2"></i>Back to List
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

    <!-- Employee Edit Form -->
    <form method="POST" action="{{ route('employees.update', $employee->id) }}" id="employeeEditForm">
        @csrf
        @method('PUT')

        <!-- Personal Information Section -->
        <div class="form-section">
            <h5 class="section-header">
                <i class="bi bi-person me-2"></i>Personal Information
            </h5>
            <div class="p-4">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="first_name" class="form-label required-field">First Name</label>
                        <input type="text"
                            class="form-control @error('first_name') is-invalid @enderror"
                            id="first_name"
                            name="first_name"
                            value="{{ old('first_name', $employee->first_name) }}"
                            placeholder="Enter first name"
                            required>
                        @error('first_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="last_name" class="form-label required-field">Last Name</label>
                        <input type="text"
                            class="form-control @error('last_name') is-invalid @enderror"
                            id="last_name"
                            name="last_name"
                            value="{{ old('last_name', $employee->last_name) }}"
                            placeholder="Enter last name"
                            required>
                        @error('last_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="email" class="form-label required-field">Email Address</label>
                        <input type="email"
                            class="form-control @error('email') is-invalid @enderror"
                            id="email"
                            name="email"
                            value="{{ old('email', $employee->email) }}"
                            placeholder="Enter email address"
                            required>
                        @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="phone" class="form-label">Phone Number</label>
                        <input type="tel"
                            class="form-control @error('phone') is-invalid @enderror"
                            id="phone"
                            name="phone"
                            value="{{ old('phone', $employee->phone) }}"
                            placeholder="+1-555-0123">
                        @error('phone')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label for="address" class="form-label">Address</label>
                    <textarea class="form-control @error('address') is-invalid @enderror"
                        id="address"
                        name="address"
                        rows="2"
                        placeholder="Enter full address">{{ old('address', $employee->address) }}</textarea>
                    @error('address')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <!-- Employment Information Section -->
        <div class="form-section">
            <h5 class="section-header">
                <i class="bi bi-briefcase me-2"></i>Employment Information
            </h5>
            <div class="p-4">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="hire_date" class="form-label required-field">Hire Date</label>
                        <input type="date"
                            class="form-control @error('hire_date') is-invalid @enderror"
                            id="hire_date"
                            name="hire_date"
                            value="{{ old('hire_date', $employee->hire_date->format('Y-m-d')) }}"
                            required>
                        @error('hire_date')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="salary" class="form-label">Salary</label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number"
                                class="form-control @error('salary') is-invalid @enderror"
                                id="salary"
                                name="salary"
                                value="{{ old('salary', $employee->salary) }}"
                                min="0"
                                step="0.01"
                                placeholder="0.00">
                        </div>
                        @error('salary')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="department_id" class="form-label">Department</label>
                        <select class="form-select @error('department_id') is-invalid @enderror"
                            id="department_id"
                            name="department_id">
                            <option value="">Select Department</option>
                            @foreach($departments as $department)
                            <option value="{{ $department->id }}"
                                {{ old('department_id', $employee->department_id) == $department->id ? 'selected' : '' }}>
                                {{ $department->name }}
                            </option>
                            @endforeach
                        </select>
                        @error('department_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="role_id" class="form-label">Role</label>
                        <select class="form-select @error('role_id') is-invalid @enderror"
                            id="role_id"
                            name="role_id">
                            <option value="">Select Role</option>
                            @foreach($roles as $role)
                            <option value="{{ $role->id }}"
                                {{ old('role_id', $employee->role_id) == $role->id ? 'selected' : '' }}
                                data-salary="{{ $role->base_salary }}">
                                {{ $role->name }}
                                @if($role->base_salary)
                                (Base: ${{ number_format($role->base_salary, 2) }})
                                @endif
                            </option>
                            @endforeach
                        </select>
                        @error('role_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4 mb-3">
                        <label for="manager_id" class="form-label">Manager</label>
                        <select class="form-select @error('manager_id') is-invalid @enderror"
                            id="manager_id"
                            name="manager_id">
                            <option value="">Select Manager</option>
                            @foreach($managers as $manager)
                            <option value="{{ $manager->id }}"
                                {{ old('manager_id', $employee->manager_id) == $manager->id ? 'selected' : '' }}
                                data-department="{{ $manager->department_id }}">
                                {{ $manager->first_name }} {{ $manager->last_name }}
                                @if($manager->department)
                                ({{ $manager->department->name }})
                                @endif
                            </option>
                            @endforeach
                        </select>
                        @error('manager_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="status" class="form-label required-field">Status</label>
                        <select class="form-select @error('status') is-invalid @enderror"
                            id="status"
                            name="status"
                            required>
                            <option value="">Select Status</option>
                            <option value="active" {{ old('status', $employee->status) == 'active' ? 'selected' : '' }}>
                                Active
                            </option>
                            <option value="inactive" {{ old('status', $employee->status) == 'inactive' ? 'selected' : '' }}>
                                Inactive
                            </option>
                        </select>
                        @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Employee Since</label>
                        <div class="form-control-plaintext">
                            <i class="bi bi-calendar-check text-success me-2"></i>
                            {{ $employee->hire_date->format('F j, Y') }}
                            ({{ $employee->hire_date->diffForHumans() }})
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Current Information Summary -->
        <div class="form-section">
            <h5 class="section-header">
                <i class="bi bi-info-circle me-2"></i>Current Information Summary
            </h5>
            <div class="p-4">
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="fw-bold mb-3">Current Details</h6>
                        <div class="mb-2">
                            <strong>Full Name:</strong> {{ $employee->first_name }} {{ $employee->last_name }}
                        </div>
                        <div class="mb-2">
                            <strong>Email:</strong> {{ $employee->email }}
                        </div>
                        <div class="mb-2">
                            <strong>Department:</strong> {{ $employee->department ? $employee->department->name : 'Not assigned' }}
                        </div>
                        <div class="mb-2">
                            <strong>Role:</strong> {{ $employee->role ? $employee->role->name : 'Not assigned' }}
                        </div>
                        <div class="mb-2">
                            <strong>Manager:</strong>
                            @if($employee->manager)
                            {{ $employee->manager->first_name }} {{ $employee->manager->last_name }}
                            @else
                            Not assigned
                            @endif
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h6 class="fw-bold mb-3">Additional Info</h6>
                        <div class="mb-2">
                            <strong>Status:</strong>
                            <span class="badge {{ $employee->status == 'active' ? 'bg-success' : 'bg-secondary' }}">
                                {{ ucfirst($employee->status) }}
                            </span>
                        </div>
                        <div class="mb-2">
                            <strong>Salary:</strong>
                            @if($employee->salary)
                            ${{ number_format($employee->salary, 2) }}
                            @else
                            Not set
                            @endif
                        </div>
                        <div class="mb-2">
                            <strong>Phone:</strong> {{ $employee->phone ?: 'Not provided' }}
                        </div>
                        <div class="mb-2">
                            <strong>Hire Date:</strong> {{ $employee->hire_date->format('M j, Y') }}
                        </div>
                        <div class="mb-2">
                            <strong>Last Updated:</strong> {{ $employee->updated_at->format('M j, Y g:i A') }}
                        </div>
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
                        <a href="{{ route('employees.index') }}" class="btn btn-cancel">
                            <i class="bi bi-x-circle me-2"></i>Cancel
                        </a>
                        <button type="submit" class="btn btn-update">
                            <i class="bi bi-check-circle me-2"></i>Update Employee
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Track original values for change detection
        const originalValues = {};
        const form = document.getElementById('employeeEditForm');
        const inputs = form.querySelectorAll('input, select, textarea');

        inputs.forEach(input => {
            originalValues[input.name] = input.value;
        });

        // Auto-fill salary when role is selected (only if salary is empty)
        const roleSelect = document.getElementById('role_id');
        const salaryInput = document.getElementById('salary');

        roleSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const baseSalary = selectedOption.getAttribute('data-salary');

            if (baseSalary && confirm('Would you like to update the salary to the base salary for this role?')) {
                salaryInput.value = parseFloat(baseSalary).toFixed(2);
            }
        });

        // Filter managers by department
        const departmentSelect = document.getElementById('department_id');
        const managerSelect = document.getElementById('manager_id');
        const managerOptions = Array.from(managerSelect.options);

        departmentSelect.addEventListener('change', function() {
            const selectedDepartment = this.value;

            // Store current selection
            const currentManager = managerSelect.value;

            // Clear manager select
            managerSelect.innerHTML = '<option value="">Select Manager</option>';

            // Add filtered options
            managerOptions.forEach(option => {
                if (option.value === '' || option.getAttribute('data-department') === selectedDepartment) {
                    managerSelect.appendChild(option.cloneNode(true));
                }
            });

            // Restore selection if still valid
            if (currentManager && managerSelect.querySelector(`option[value="${currentManager}"]`)) {
                managerSelect.value = currentManager;
            }
        });

        // Form change detection
        let hasChanges = false;
        inputs.forEach(input => {
            input.addEventListener('change', function() {
                hasChanges = (this.value !== originalValues[this.name]);
            });
        });

        // Warn before leaving if there are unsaved changes
        window.addEventListener('beforeunload', function(e) {
            if (hasChanges) {
                e.preventDefault();
                e.returnValue = '';
            }
        });

        // Don't warn when submitting the form
        form.addEventListener('submit', function() {
            hasChanges = false;
        });

        // Form validation feedback
        form.addEventListener('submit', function(e) {
            const requiredFields = form.querySelectorAll('[required]');
            let isValid = true;

            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    isValid = false;
                    field.classList.add('is-invalid');
                } else {
                    field.classList.remove('is-invalid');
                }
            });

            if (!isValid) {
                e.preventDefault();
                alert('Please fill in all required fields.');
            }
        });
    });
</script>
@endpush
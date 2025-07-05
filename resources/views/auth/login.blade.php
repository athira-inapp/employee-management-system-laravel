@extends('layouts.app')

@section('title', 'Login - Employee Management System')

@section('content')
<div class="login-container d-flex align-items-center">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6 col-lg-5 col-xl-4">
                <div class="card login-card">
                    <div class="card-body p-5">
                        <!-- Company Logo -->
                        <div class="logo-container">
                            <div class="ems-logo">
                                <div class="logo-text">
                                    <span class="e-letter">E</span><span class="ms-letters">MS</span>
                                </div>
                            </div>
                        </div>

                        <!-- Header -->
                        <div class="text-center mb-4">
                            <h2 class="card-title fw-bold mb-2">Welcome Back!</h2>
                            <p class="text-muted">Please sign in to your account</p>
                        </div>

                        <!-- Success/Error Messages -->
                        @if (session('success'))
                        <div class="alert alert-success d-flex align-items-center mb-4">
                            <i class="bi bi-check-circle-fill me-2"></i>
                            {{ session('success') }}
                        </div>
                        @endif

                        @if (session('error'))
                        <div class="alert alert-danger d-flex align-items-center mb-4">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                            {{ session('error') }}
                        </div>
                        @endif

                        <!-- Login Form -->
                        <form method="POST" action="{{ route('login') }}">
                            @csrf

                            <!-- Email Field -->
                            <div class="form-floating mb-3">
                                <input type="email"
                                    class="form-control @error('email') is-invalid @enderror"
                                    id="email"
                                    name="email"
                                    placeholder="name@example.com"
                                    value="{{ old('email') }}"
                                    required>
                                <label for="email">
                                    <i class="bi bi-envelope me-2"></i>Email Address
                                </label>
                                @error('email')
                                <div class="invalid-feedback">
                                    <i class="bi bi-exclamation-circle me-1"></i>{{ $message }}
                                </div>
                                @enderror
                            </div>

                            <!-- Password Field -->
                            <div class="form-floating mb-3">
                                <input type="password"
                                    class="form-control @error('password') is-invalid @enderror"
                                    id="password"
                                    name="password"
                                    placeholder="Password"
                                    required>
                                <label for="password">
                                    <i class="bi bi-lock me-2"></i>Password
                                </label>
                                @error('password')
                                <div class="invalid-feedback">
                                    <i class="bi bi-exclamation-circle me-1"></i>{{ $message }}
                                </div>
                                @enderror
                            </div>

                            <!-- Remember Me -->
                            <div class="form-check mb-4">
                                <input class="form-check-input" type="checkbox" id="remember" name="remember">
                                <label class="form-check-label" for="remember">
                                    Remember me for 30 days
                                </label>
                            </div>

                            <!-- Login Button -->
                            <button type="submit" class="btn btn-primary btn-login w-100 mb-3">
                                <i class="bi bi-box-arrow-in-right me-2"></i>Sign In
                            </button>
                        </form>

                        <!-- Footer -->
                        <div class="text-center mt-4">
                            <small class="text-muted">
                                <i class="bi bi-shield-lock me-1"></i>
                                Your data is secure and encrypted
                            </small>
                        </div>

                        <!-- Demo Credentials -->
                        <!-- <div class="mt-4 p-3 bg-light rounded">
                            <h6 class="fw-bold mb-2">
                                <i class="bi bi-info-circle me-2"></i>Demo Credentials
                            </h6>
                            <div class="row">
                                <div class="col-12 mb-2">
                                    <strong>Admin:</strong><br>
                                    <small class="text-muted">sarah.johnson@company.com / password</small>
                                </div>
                                <div class="col-12 mb-2">
                                    <strong>Manager:</strong><br>
                                    <small class="text-muted">david.chen@company.com / password</small>
                                </div>
                                <div class="col-12">
                                    <strong>Employee:</strong><br>
                                    <small class="text-muted">john.smith@company.com / password</small>
                                </div>
                            </div>
                        </div> -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@push('styles')
<style>
    .ems-logo {
        background: #000000;
        width: 140px;
        height: 140px;
        border-radius: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.3);
        margin: 0 auto;
        /* This centers the logo horizontally */
    }

    .logo-text {
        font-size: 2.5rem;
        font-weight: 700;
        letter-spacing: 2px;
        text-shadow: none;
    }

    .logo-text .e-letter {
        color: #ff0000;
    }

    .logo-text .ms-letters {
        color: #ffffff;
    }

    .logo-container {
        text-align: center;
        margin-bottom: 2rem;
        /* Add some space below the logo */
        display: flex;
        justify-content: center;
        align-items: center;
    }
</style>
@endpush
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Employee Management System')</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        .login-container {
            min-height: 100vh;
            background: linear-gradient(135deg, #0e0e0e 0%, #080808 100%);
        }

        .login-card {
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.95);
            border: none;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
        }

        .company-logo {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
        }

        .form-floating>.form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.25rem rgba(102, 126, 234, 0.25);
        }

        .btn-login {
            background: linear-gradient(135deg, #a9080d 0%, #a10202 100%);
            border: none;
            padding: 12px;
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        .btn-login:hover {
            background: linear-gradient(135deg, #dc3d3d 0%, #cf4b4b 100%);
            transform: translateY(-1px);
        }

        .navbar-brand {
            font-weight: 700;
            color: #970707 !important;
        }

        .alert {
            border: none;
            border-radius: 10px;
        }

        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .main-content {
            flex: 1;
        }

        .footer {
            background: linear-gradient(135deg, #090909 0%, #131313 100%);
            color: #ecf0f1;
            /* padding: 40px 0 20px; */
            margin-top: auto;
        }

        .footer h5 {
            color: #3498db;
            font-weight: 600;
            margin-bottom: 20px;
        }

        .footer a {
            color: #bdc3c7;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .footer a:hover {
            color: #3498db;
        }

        .footer-bottom {
            border-top: 1px solid #34495e;
            padding-top: 20px;
            /* margin-top: 30px; */
            text-align: center;
            color: #ffffff;
        }

        .social-links a {
            display: inline-block;
            margin: 0 10px;
            font-size: 1.5rem;
            color: #bdc3c7;
            transition: all 0.3s ease;
        }

        .social-links a:hover {
            color: #3498db;
            transform: translateY(-2px);
        }
    </style>

    @stack('styles')
</head>

<body>
    <div>


        @yield('content')
    </div>
    <footer class="footer">
        <div class="container">


            <div class="footer-bottom">
                <p>&copy; {{ date('Y') }} Employee Management System. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    @stack('scripts')
</body>

</html>
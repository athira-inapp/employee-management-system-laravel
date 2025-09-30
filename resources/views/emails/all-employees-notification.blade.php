<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>All Employees List</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            background-color: #ffffff;
            border: 1px solid #e0e0e0;
        }

        .header {
            background-color: #000000;
            color: #ffffff;
            padding: 20px;
            text-align: center;
        }

        .header h1 {
            margin: 0;
            font-size: 24px;
        }

        .content {
            padding: 30px;
        }

        .summary {
            background-color: #f8f9fa;
            padding: 15px;
            margin-bottom: 25px;
            border-left: 4px solid #000000;
        }

        .employee-list {
            margin: 0;
            padding: 0;
            list-style: none;
        }

        .employee-item {
            padding: 15px;
            border-bottom: 1px solid #e0e0e0;
        }

        .employee-name {
            font-weight: bold;
            color: #212529;
            font-size: 16px;
        }

        .employee-details {
            color: #6c757d;
            font-size: 14px;
            margin-top: 5px;
        }

        .footer {
            padding: 20px;
            text-align: center;
            color: #6c757d;
            font-size: 14px;
            border-top: 1px solid #e0e0e0;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>📋 All Employees List</h1>
            <p>{{ now()->format('F d, Y - h:i A') }}</p>
        </div>

        <div class="content">
            <div class="summary">
                <h2>Total Employees: {{ $totalCount }}</h2>
            </div>

            <ul class="employee-list">
                @foreach($employees as $index => $employee)
                <li class="employee-item">
                    <div class="employee-name">
                        {{ $index + 1 }}. {{ $employee->first_name }} {{ $employee->last_name }}
                    </div>
                    <div class="employee-details">
                        📧 {{ $employee->email }}
                        @if($employee->department)
                        • 🏢 {{ $employee->department->name }}
                        @endif
                        @if($employee->role)
                        • 👤 {{ $employee->role->name }}
                        @endif
                    </div>
                </li>
                @endforeach
            </ul>
        </div>

        <div class="footer">
            <p>&copy; {{ date('Y') }} Employee Management System</p>
        </div>
    </div>
</body>

</html>
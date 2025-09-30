<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>New Employee Created</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border: 1px solid #e0e0e0;
        }

        .header {
            background-color: #000000;
            color: #ffffff;
            padding: 20px;
        }

        .header h1 {
            margin: 0;
            font-size: 24px;
        }

        .content {
            padding: 30px;
            color: #212529;
        }

        .employee-info {
            background-color: #f8f9fa;
            padding: 20px;
            margin: 20px 0;
            border-left: 4px solid #000000;
        }

        .info-row {
            margin: 10px 0;
        }

        .info-label {
            font-weight: bold;
            color: #495057;
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
            <h1>🎉 New Employee Created</h1>
        </div>

        <div class="content">
            <p>Hello,</p>
            <p>A new employee has been successfully added to the Employee Management System.</p>

            <div class="employee-info">
                <h2 style="margin-top: 0; color: #212529;">Employee Details</h2>

                <div class="info-row">
                    <span class="info-label">Name:</span>
                    {{ $employee->first_name }} {{ $employee->last_name }}
                </div>

                <div class="info-row">
                    <span class="info-label">Email:</span>
                    {{ $employee->email }}
                </div>

                @if($employee->phone)
                <div class="info-row">
                    <span class="info-label">Phone:</span>
                    {{ $employee->phone }}
                </div>
                @endif

                @if($employee->address)
                <div class="info-row">
                    <span class="info-label">Address:</span>
                    {{ $employee->address }}
                </div>
                @endif

                @if($employee->department)
                <div class="info-row">
                    <span class="info-label">Department:</span>
                    {{ $employee->department->name }}
                </div>
                @endif

                @if($employee->role)
                <div class="info-row">
                    <span class="info-label">Role:</span>
                    {{ $employee->role->name }}
                </div>
                @endif

                @if($employee->hire_date)
                <div class="info-row">
                    <span class="info-label">Hire Date:</span>
                    {{ \Carbon\Carbon::parse($employee->hire_date)->format('F d, Y') }}
                </div>
                @endif

                @if($employee->salary)
                <div class="info-row">
                    <span class="info-label">Salary:</span>
                    ${{ number_format($employee->salary, 2) }}
                </div>
                @endif

                <div class="info-row">
                    <span class="info-label">Status:</span>
                    {{ ucfirst($employee->status) }}
                </div>

                <div class="info-row">
                    <span class="info-label">Created At:</span>
                    {{ $employee->created_at->format('F d, Y g:i A') }}
                </div>
            </div>

            <p>This is an automated notification from the Employee Management System.</p>
        </div>

        <div class="footer">
            <p>&copy; {{ date('Y') }} Employee Management System. All rights reserved.</p>
        </div>
    </div>
</body>

</html>
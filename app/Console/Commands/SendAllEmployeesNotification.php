<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Models\Employee;
use Illuminate\Support\Facades\Mail;
use App\Mail\AllEmployeesNotification;

class SendAllEmployeesNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:all-employees';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send notification to admin with all employee names';


    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Fetching all employees...');

        $employees = Employee::with(['department', 'role'])
            ->orderBy('first_name')
            ->get();

        if ($employees->isEmpty()) {
            $this->warn('No employees found in database.');
            Log::info('All Employees Notification: No employees found');
            return;
        }

        $this->info("Found {$employees->count()} employees. Sending email...");
        try {
            Mail::to(env('ADMIN_EMAIL'))
                ->send(new AllEmployeesNotification($employees));

            $this->info('Email sent successfully!');
            Log::info("All Employees Notification sent: {$employees->count()} employees");
        } catch (\Exception $e) {
            $this->error('Failed to send email: ' . $e->getMessage());
            Log::error('All Employees Notification failed: ' . $e->getMessage());
        }

        return Command::SUCCESS;
    }
}

<?php

use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\EmployeeController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Redirect root to login
Route::get('/', function () {
    return redirect()->route('login');
});

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
// Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::match(['get', 'post'], '/logout', [AuthController::class, 'logout'])->name('logout');
// Protected Routes
Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [AuthController::class, 'dashboard'])->name('dashboard');
    Route::resource('employees', EmployeeController::class);

    Route::resource('leave-requests', \App\Http\Controllers\Web\LeaveRequestController::class);
    Route::patch('/leave-requests/{leaveRequest}/status', [
        \App\Http\Controllers\Web\LeaveRequestController::class,
        'updateStatus'
    ])->name('leave-requests.update-status');
});

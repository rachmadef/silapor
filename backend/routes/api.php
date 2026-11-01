<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClassController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LaptopController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ParentController;
use App\Http\Controllers\PickupController;
use App\Http\Controllers\ReturnController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\StudentController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::post('/auth/login', [AuthController::class, 'login']);
Route::get('/public/students', [LaptopController::class, 'publicStudents']);
Route::post('/public/laptop-form', [LaptopController::class, 'submitStudentForm']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // Auth profile & logout
    Route::get('/auth/me', [AuthController::class, 'me']);
    Route::post('/auth/logout', [AuthController::class, 'logout']);

    // Dashboard & stats
    Route::get('/dashboard/stats', [DashboardController::class, 'stats']);

    // Master Data Resources
    Route::apiResource('classes', ClassController::class);
    Route::apiResource('parents', ParentController::class);
    Route::apiResource('students', StudentController::class);
    Route::post('/students/{student}/generate-qr', [StudentController::class, 'generateQr']);
    Route::apiResource('laptops', LaptopController::class);
    Route::apiResource('schedules', ScheduleController::class);

    // Pickup Transactions
    Route::post('/pickups/scan', [PickupController::class, 'scan']);
    Route::get('/pickups/history', [PickupController::class, 'history']);

    // Return Transactions
    Route::post('/returns/scan', [ReturnController::class, 'scan']);
    Route::get('/returns/history', [ReturnController::class, 'history']);

    // Notifications & Overdue Check
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::post('/notifications/check-overdue', [NotificationController::class, 'triggerCheckOverdue']);
    Route::post('/notifications/{notification}/retry', [NotificationController::class, 'retry']);
});

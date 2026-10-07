<?php

use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BillingController;
use App\Http\Controllers\ClinicDashboardController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\LabOrderController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - Dental Clinic Management & Public Portal
|--------------------------------------------------------------------------
*/

// Public-Facing Website & Patient Online Booking
Route::get('/', function () {
    return view('website.index');
})->name('website.home');

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Clinic Management ERP & EMR (Protected by Authentication & Role Middleware)
Route::prefix('clinic')->name('clinic.')->middleware(['auth'])->group(function () {
    // 1. Dashboard Overview (Accessible to all authenticated staff)
    Route::get('/', [ClinicDashboardController::class, 'index'])->name('dashboard');

    // 2. Patients Directory & Medical Records (All staff can view, doctors/receptionists manage)
    Route::get('/patients/search', [PatientController::class, 'search'])->name('patients.search');
    Route::get('/patients', [PatientController::class, 'index'])->name('patients.index');
    Route::post('/patients', [PatientController::class, 'store'])->name('patients.store');
    Route::get('/patients/{patient}', [PatientController::class, 'show'])->name('patients.show');
    Route::put('/patients/{patient}', [PatientController::class, 'update'])->name('patients.update');

    // 3. Appointments & Reception Desk (Admin, Receptionist, Doctor)
    Route::middleware(['role:admin,receptionist,doctor'])->group(function () {
        Route::get('/appointments', [AppointmentController::class, 'index'])->name('appointments.index');
        Route::post('/appointments', [AppointmentController::class, 'store'])->name('appointments.store');
        Route::patch('/appointments/{appointment}/status', [AppointmentController::class, 'updateStatus'])->name('appointments.status');
        Route::delete('/appointments/{appointment}', [AppointmentController::class, 'destroy'])->name('appointments.destroy');
    });

    // 4. Clinical Dental Chart (Odontogram Live Updates - Doctor & Admin only)
    Route::post('/tooth-update', [ClinicDashboardController::class, 'updateTooth'])
        ->middleware(['role:admin,doctor'])
        ->name('tooth.update');

    // 5. Billing, Invoices & Installments (Accountant & Admin only for overview, receptionists can record payments/invoices)
    Route::middleware(['role:admin,accountant'])->group(function () {
        Route::get('/billing', [BillingController::class, 'index'])->name('billing.index');
        Route::get('/billing/{invoice}', [BillingController::class, 'show'])->name('billing.show');
    });

    Route::middleware(['role:admin,accountant,receptionist'])->group(function () {
        Route::post('/billing', [BillingController::class, 'store'])->name('billing.store');
        Route::post('/billing/{invoice}/payment', [BillingController::class, 'recordPayment'])->name('billing.payment');
    });

    // 6. Dental Lab Orders & Prosthetics (Doctor & Admin only)
    Route::middleware(['role:admin,doctor'])->group(function () {
        Route::get('/labs', [LabOrderController::class, 'index'])->name('labs.index');
        Route::post('/labs', [LabOrderController::class, 'store'])->name('labs.store');
        Route::patch('/labs/{labOrder}/status', [LabOrderController::class, 'updateStatus'])->name('labs.status');
    });

    // 7. User & Access Security Management (Admin only)
    Route::middleware(['role:admin'])->group(function () {
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
        Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
        Route::patch('/users/{user}/toggle', [UserController::class, 'toggleStatus'])->name('users.toggle');
    });

    // 8. Clinic Expenses Management (Admin & Accountant)
    Route::middleware(['role:admin,accountant'])->group(function () {
        Route::get('/expenses', [ExpenseController::class, 'index'])->name('expenses.index');
        Route::post('/expenses', [ExpenseController::class, 'store'])->name('expenses.store');
        Route::delete('/expenses/{expense}', [ExpenseController::class, 'destroy'])->name('expenses.destroy');
    });

    // 9. Clinic System & Financial Settings (Admin only)
    Route::middleware(['role:admin'])->group(function () {
        Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
        Route::put('/settings', [SettingController::class, 'update'])->name('settings.update');
    });
});

// Alias for convenience
Route::get('/dashboard', function () {
    return redirect()->route('clinic.dashboard');
});

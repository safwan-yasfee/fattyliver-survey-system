<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Auth\RegistrationController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware('guest')->group(function () {
    Route::get('/register', [RegistrationController::class, 'showRegistration'])
    ->name('register');

Route::post('/register', [RegistrationController::class, 'register'])
    ->middleware('throttle:5,1');




    Route::get('/login', [
        LoginController::class, 'showLogin'
    ])->name('login');

    Route::post('/login', [
        LoginController::class, 'login'
    ])->middleware('throttle:5,1');

});

Route::middleware('auth')->group(function () {

    Route::middleware(['auth', 'can:view-patient-records'])
    ->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'clinical'])
            ->name('dashboard');

        Route::get(
            '/dashboard/research',
            [DashboardController::class, 'research']
        )->name('dashboard.research');

        Route::get(
            '/dashboard/research/visits/export',
            [DashboardController::class, 'exportVisits']
        )->name('dashboard.research.export');
    });

    Route::post('/logout', [
        LoginController::class, 'logout'
    ])->name('logout');

    // Admin User Management
    Route::prefix('admin')
        ->name('admin.')
        ->group(function () {

            Route::get('/users', [
                UserController::class, 'index'
            ])->name('users.index');

            Route::get('/users/create', [
                UserController::class, 'create'
            ])->name('users.create');

            Route::post('/users', [
                UserController::class, 'store'
            ])->name('users.store');

            Route::patch('/users/{user}/status', [
                UserController::class, 'toggleStatus'
            ])->name('users.status');

        });

        Route::middleware(['auth', 'can:view-patient-records'])
    ->group(function () {
        Route::get('/patients', [PatientController::class, 'index'])
            ->name('patients.index');

        Route::get('/patients/create', [PatientController::class, 'create'])
            ->name('patients.create');

        Route::post('/patients', [PatientController::class, 'store'])
            ->name('patients.store');

         Route::get('/patients/{patient}', [PatientController::class, 'show'])
    ->whereNumber('patient')
    ->name('patients.show');   
    });

    Route::middleware(['auth', 'can:manage-patient-records'])
    ->group(function () {
        Route::get('/patients/{patient}/edit', [PatientController::class, 'edit'])
            ->name('patients.edit');

        Route::put('/patients/{patient}', [PatientController::class, 'update'])
            ->name('patients.update');
    });




});

<?php

use App\Http\Controllers\InterfaceController;
use App\Http\Middleware\EnsureActiveAccount;
use Illuminate\Support\Facades\Route;

// Uses the authentication, active-account middleware and Gate
// already present in your project.
Route::middleware([
    'auth:web',
    EnsureActiveAccount::class,
    'can:view-patient-records',
])
    ->prefix('interface')
    ->name('interface.')
    ->group(function () {
        Route::get('/clinical', [
            InterfaceController::class,
            'clinical',
        ])->name('clinical');

        Route::get('/clinical/patients/{patient}', [
            InterfaceController::class,
            'patient',
        ])
            ->where('patient', 'P-[0-9]{4}')
            ->name('patient');

        Route::get('/research', [
            InterfaceController::class,
            'research',
        ])->name('research');

        Route::get('/research/export/csv', [
            InterfaceController::class,
            'csv',
        ])->name('research.csv');

        Route::get('/research/export/pdf', [
            InterfaceController::class,
            'pdf',
        ])->name('research.pdf');
    });
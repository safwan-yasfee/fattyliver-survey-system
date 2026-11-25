<?php

use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\AccountSetupController;
use App\Http\Middleware\EnsureActiveAccount;
use Illuminate\Support\Facades\Route;

// Guests can open an invitation and set their password.
Route::middleware([
    'guest',
    'throttle:6,1',
])->group(function () {
    Route::get('/account/setup/{token}', [
        AccountSetupController::class,
        'show',
    ])->name('account.setup.show');

    Route::post('/account/setup', [
        AccountSetupController::class,
        'store',
    ])->name('account.setup.store');
});

// Account management requires an active, authenticated Admin.
Route::middleware([
    'auth:web',
    EnsureActiveAccount::class,
    'can:manage-users',
])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/users', [
            UserController::class,
            'index',
        ])->name('users.index');

        Route::get('/consent-versions', [
            \App\Http\Controllers\Admin\ConsentVersionController::class,
            'index',
        ])->name('consent-versions.index');

        Route::post('/consent-versions', [
            \App\Http\Controllers\Admin\ConsentVersionController::class,
            'store',
        ])->name('consent-versions.store');

        Route::patch('/consent-versions/{consentVersion}/availability', [
            \App\Http\Controllers\Admin\ConsentVersionController::class,
            'availability',
        ])
            ->whereNumber('consentVersion')
            ->name('consent-versions.availability');





        Route::post('/users', [
            UserController::class,
            'store',
        ])->name('users.store');


        Route::post('/users', [
            UserController::class,
            'store',
        ])->name('users.store');

        Route::post('/users/approve-selected', [
            UserController::class,
            'bulkApprove',
        ])
            ->middleware('throttle:6,1')
            ->name('users.approve-selected');

        Route::patch('/users/{user}/approve', [
            UserController::class,
            'approve',
        ])
            ->whereNumber('user')
            ->name('users.approve');

        Route::patch('/users/{user}/suspend', [
            UserController::class,
            'suspend',
        ])
            ->whereNumber('user')
            ->name('users.suspend');

        Route::patch('/users/{user}/reactivate', [
            UserController::class,
            'reactivate',
        ])
            ->whereNumber('user')
            ->name('users.reactivate');

        Route::post('/users/{user}/invite', [
            UserController::class,
            'resendInvite',
        ])
            ->whereNumber('user')
            ->middleware('throttle:6,1')
            ->name('users.invite');


        Route::post('/users/approve-selected', [
            UserController::class,
            'bulkApprove',
        ])
            ->middleware('throttle:6,1')
            ->name('users.approve-selected');
    });
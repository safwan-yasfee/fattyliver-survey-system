<?php

use App\Http\Controllers\AdherenceController;
use App\Http\Controllers\AdverseEventController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Http\Controllers\ClinicalAlertController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DietPlanController;
use App\Http\Controllers\ElastographyResultController;
use App\Http\Controllers\FollowUpController;
use App\Http\Controllers\LabResultController;
use App\Http\Controllers\LifestyleProfileController;
use App\Http\Controllers\MedicalHistoryController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\TreatmentController;
use App\Http\Middleware\EnsureActiveAccount;
use Illuminate\Support\Facades\Route;

// Home
Route::get('/', function () {
    return redirect()->route(
        auth()->check() ? 'dashboard' : 'login'
    );
});

// Login and password recovery
Route::middleware('guest')->group(function () {
    Route::get('/login', [
        LoginController::class,
        'showLogin',
    ])->name('login');

    // Additional IP-wide limit.
    // LoginController separately limits failures per email + IP.
    Route::post('/login', [
        LoginController::class,
        'login',
    ])->middleware('throttle:30,1');

    Route::get('/forgot-password', [
        PasswordResetController::class,
        'showForgotForm',
    ])->name('password.request');

    Route::post('/forgot-password', [
        PasswordResetController::class,
        'sendResetLink',
    ])
        ->middleware('throttle:5,1')
        ->name('password.email');

    Route::get('/reset-password/{token}', [
        PasswordResetController::class,
        'showResetForm',
    ])->name('password.reset');

    Route::post('/reset-password', [
        PasswordResetController::class,
        'resetPassword',
    ])
        ->middleware('throttle:5,1')
        ->name('password.update');
});

// Signed-in users with active accounts
Route::middleware([
    'auth:web',
    EnsureActiveAccount::class,
])->group(function () {
    Route::post('/logout', [
        LoginController::class,
        'logout',
    ])->name('logout');

    // Archived patients — Admin and Assistant
    Route::middleware('can:archive-patients')->group(function () {
        Route::get('/patients/archived', [
            PatientController::class,
            'archived',
        ])->name('patients.archived');

        Route::patch('/patients/{patient}/restore', [
            PatientController::class,
            'restore',
        ])
            ->whereNumber('patient')
            ->name('patients.restore');

        Route::delete('/patients/{patient}', [
            PatientController::class,
            'destroy',
        ])
            ->whereNumber('patient')
            ->name('patients.destroy');
    });

    // Patient editing
    Route::middleware('can:manage-patient-records')->group(function () {
        Route::get('/patients/{patient}/edit', [
            PatientController::class,
            'edit',
        ])
            ->whereNumber('patient')
            ->name('patients.edit');

        Route::put('/patients/{patient}', [
            PatientController::class,
            'update',
        ])
            ->whereNumber('patient')
            ->name('patients.update');
    });

    // Dashboards and clinical records
    Route::middleware('can:view-patient-records')->group(function () {
        // Dashboards
        Route::get('/dashboard', [
            DashboardController::class,
            'clinical',
        ])->name('dashboard');

        Route::get('/dashboard/research', [
            DashboardController::class,
            'research',
        ])->name('dashboard.research');

        Route::get('/dashboard/research/visits/export', [
            DashboardController::class,
            'exportVisits',
        ])->name('dashboard.research.export');

        Route::get('/dashboard/research/patients', [
            \App\Http\Controllers\ResearchPatientController::class,
            'index',
        ])->name('dashboard.research.patients');

        Route::get('/dashboard/research/patients/export', [
            \App\Http\Controllers\ResearchPatientController::class,
            'export',
        ])->name('dashboard.research.patients.export');

        Route::get('/dashboard/research/patients', [
            \App\Http\Controllers\ResearchPatientController::class,
            'index',
        ])->name('dashboard.research.patients');





        Route::get('/patients/{patient}/consents', [
            \App\Http\Controllers\ConsentController::class,
            'index',
        ])
            ->whereNumber('patient')
            ->name('patients.consents.index');

        Route::post('/patients/{patient}/consents', [
            \App\Http\Controllers\ConsentController::class,
            'store',
        ])
            ->middleware('can:record-patient-consent')
            ->whereNumber('patient')
            ->name('patients.consents.store');

        Route::patch('/patients/{patient}/consents/{consent}/withdraw', [
            \App\Http\Controllers\ConsentController::class,
            'withdraw',
        ])
            ->middleware('can:record-patient-consent')
            ->whereNumber('patient')
            ->whereNumber('consent')
            ->name('patients.consents.withdraw');

        // Patients
        Route::get('/patients', [
            PatientController::class,
            'index',
        ])->name('patients.index');

        Route::get('/patients/create', [
            PatientController::class,
            'create',
        ])->name('patients.create');

        Route::post('/patients', [
            PatientController::class,
            'store',
        ])->name('patients.store');

        Route::get('/patients/{patient}', [
            PatientController::class,
            'show',
        ])
            ->whereNumber('patient')
            ->name('patients.show');

        // Medical history
        Route::get('/patients/{patient}/medical-history', [
            MedicalHistoryController::class,
            'edit',
        ])
            ->whereNumber('patient')
            ->name('patients.medical-history.edit');

        Route::put('/patients/{patient}/medical-history', [
            MedicalHistoryController::class,
            'update',
        ])
            ->whereNumber('patient')
            ->name('patients.medical-history.update');



        // View eligibility and review history
        Route::get('/patients/{patient}/eligibility', [
            \App\Http\Controllers\EligibilityReviewController::class,
            'edit',
        ])
            ->whereNumber('patient')
            ->name('patients.eligibility.edit');

        // Record an eligibility decision — Admin and Doctor
        Route::post('/patients/{patient}/eligibility', [
            \App\Http\Controllers\EligibilityReviewController::class,
            'store',
        ])
            ->middleware('can:review-patient-eligibility')
            ->whereNumber('patient')
            ->name('patients.eligibility.store');

        // Lifestyle
        Route::get('/patients/{patient}/lifestyle', [
            LifestyleProfileController::class,
            'edit',
        ])
            ->whereNumber('patient')
            ->name('patients.lifestyle.edit');

        Route::put('/patients/{patient}/lifestyle', [
            LifestyleProfileController::class,
            'update',
        ])
            ->whereNumber('patient')
            ->name('patients.lifestyle.update');

        // Schedule visit
        Route::post('/patients/{patient}/visits', [
            PatientController::class,
            'storeVisit',
        ])
            ->whereNumber('patient')
            ->name('patients.visits.store');

        // Vitals
        Route::get('/patients/{patient}/visits/{visit}/vitals', [
            PatientController::class,
            'editVitals',
        ])
            ->whereNumber('patient')
            ->whereNumber('visit')
            ->name('patients.visits.vitals.edit');

        Route::put('/patients/{patient}/visits/{visit}/vitals', [
            PatientController::class,
            'updateVitals',
        ])
            ->whereNumber('patient')
            ->whereNumber('visit')
            ->name('patients.visits.vitals.update');

        // Complete visit
        Route::patch('/patients/{patient}/visits/{visit}/complete', [
            PatientController::class,
            'completeVisit',
        ])
            ->whereNumber('patient')
            ->whereNumber('visit')
            ->name('patients.visits.complete');

        // Lab results
        Route::get('/patients/{patient}/visits/{visit}/labs', [
            LabResultController::class,
            'edit',
        ])
            ->whereNumber('patient')
            ->whereNumber('visit')
            ->name('patients.visits.labs.edit');

        Route::put('/patients/{patient}/visits/{visit}/labs', [
            LabResultController::class,
            'update',
        ])
            ->whereNumber('patient')
            ->whereNumber('visit')
            ->name('patients.visits.labs.update');

        // Imaging
        Route::get('/patients/{patient}/visits/{visit}/elastography', [
            ElastographyResultController::class,
            'edit',
        ])
            ->whereNumber('patient')
            ->whereNumber('visit')
            ->name('patients.visits.elastography.edit');

        Route::put('/patients/{patient}/visits/{visit}/elastography', [
            ElastographyResultController::class,
            'update',
        ])
            ->whereNumber('patient')
            ->whereNumber('visit')
            ->name('patients.visits.elastography.update');

        // Treatments
        Route::get('/patients/{patient}/visits/{visit}/treatments', [
            TreatmentController::class,
            'index',
        ])
            ->whereNumber('patient')
            ->whereNumber('visit')
            ->name('patients.visits.treatments.index');

        Route::post('/patients/{patient}/visits/{visit}/treatments', [
            TreatmentController::class,
            'store',
        ])
            ->whereNumber('patient')
            ->whereNumber('visit')
            ->name('patients.visits.treatments.store');

        Route::put(
            '/patients/{patient}/visits/{visit}/treatments/{treatment}',
            [TreatmentController::class, 'update']
        )
            ->whereNumber('patient')
            ->whereNumber('visit')
            ->whereNumber('treatment')
            ->name('patients.visits.treatments.update');

        // Medication adherence
        Route::get(
            '/patients/{patient}/visits/{visit}/treatments/{treatment}/adherence',
            [AdherenceController::class, 'edit']
        )
            ->whereNumber('patient')
            ->whereNumber('visit')
            ->whereNumber('treatment')
            ->name('patients.visits.treatments.adherence.edit');

        Route::put(
            '/patients/{patient}/visits/{visit}/treatments/{treatment}/adherence',
            [AdherenceController::class, 'update']
        )
            ->whereNumber('patient')
            ->whereNumber('visit')
            ->whereNumber('treatment')
            ->name('patients.visits.treatments.adherence.update');

        // Adverse events
        Route::get('/patients/{patient}/visits/{visit}/adverse-events', [
            AdverseEventController::class,
            'index',
        ])
            ->whereNumber('patient')
            ->whereNumber('visit')
            ->name('patients.visits.adverse-events.index');

        Route::post('/patients/{patient}/visits/{visit}/adverse-events', [
            AdverseEventController::class,
            'store',
        ])
            ->whereNumber('patient')
            ->whereNumber('visit')
            ->name('patients.visits.adverse-events.store');

        Route::put(
            '/patients/{patient}/visits/{visit}/adverse-events/{event}',
            [AdverseEventController::class, 'update']
        )
            ->whereNumber('patient')
            ->whereNumber('visit')
            ->whereNumber('event')
            ->name('patients.visits.adverse-events.update');

        // Diet and exercise counselling
        Route::get('/patients/{patient}/visits/{visit}/counselling', [
            DietPlanController::class,
            'edit',
        ])
            ->whereNumber('patient')
            ->whereNumber('visit')
            ->name('patients.visits.counselling.edit');

        Route::put('/patients/{patient}/visits/{visit}/counselling', [
            DietPlanController::class,
            'update',
        ])
            ->whereNumber('patient')
            ->whereNumber('visit')
            ->name('patients.visits.counselling.update');

        // Follow-up notes
        Route::get('/patients/{patient}/visits/{visit}/follow-up', [
            FollowUpController::class,
            'index',
        ])
            ->whereNumber('patient')
            ->whereNumber('visit')
            ->name('patients.visits.follow-up.index');

        Route::post('/patients/{patient}/visits/{visit}/follow-up', [
            FollowUpController::class,
            'store',
        ])
            ->whereNumber('patient')
            ->whereNumber('visit')
            ->name('patients.visits.follow-up.store');

        Route::put(
            '/patients/{patient}/visits/{visit}/follow-up/{entry}',
            [FollowUpController::class, 'update']
        )
            ->whereNumber('patient')
            ->whereNumber('visit')
            ->whereNumber('entry')
            ->name('patients.visits.follow-up.update');

        // Clinical alerts
        Route::get('/patients/{patient}/visits/{visit}/alerts', [
            ClinicalAlertController::class,
            'index',
        ])
            ->whereNumber('patient')
            ->whereNumber('visit')
            ->name('patients.visits.alerts.index');

        Route::post('/patients/{patient}/visits/{visit}/alerts', [
            ClinicalAlertController::class,
            'store',
        ])
            ->whereNumber('patient')
            ->whereNumber('visit')
            ->name('patients.visits.alerts.store');

        Route::patch(
            '/patients/{patient}/visits/{visit}/alerts/{alert}/resolve',
            [ClinicalAlertController::class, 'resolve']
        )
            ->whereNumber('patient')
            ->whereNumber('visit')
            ->whereNumber('alert')
            ->name('patients.visits.alerts.resolve');
    });
});

// Invitation links must remain accessible to guests.
// Admin routes in this separate file need their own protection.
require __DIR__ . '/account-management.php';
require __DIR__ . '/interface.php';
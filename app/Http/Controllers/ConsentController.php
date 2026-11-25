<?php

namespace App\Http\Controllers;

use App\Models\ConsentVersion;
use App\Models\Patient;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ConsentController extends Controller
{
    public function index(Patient $patient): View
    {
        Gate::authorize('view-patient-records');

        $versions = ConsentVersion::query()
            ->where('is_active', true)
            ->whereNotNull('version_code')
            ->whereNotNull('title')
            ->whereDate('effective_date', '<=', today()->toDateString())
            ->orderByDesc('effective_date')
            ->orderByDesc('id')
            ->get();

        $latestConsent = $patient->latestConsent()
            ->with('consentVersion')
            ->first();

        $consents = $patient->consents()
            ->with([
                'consentVersion',
                'recordedBy',
                'withdrawnBy',
            ])
            ->orderByDesc('id')
            ->paginate(10);

        return view('patients.consents', compact(
            'patient',
            'versions',
            'latestConsent',
            'consents'
        ));
    }

    public function store(
        Request $request,
        Patient $patient
    ): RedirectResponse {
        Gate::authorize('record-patient-consent');

        $data = $request->validate([
            'consent_version_id' => [
                'required',
                'integer',
                'exists:consent_versions,id',
            ],
            'signed_at' => [
                'required',
                'date_format:Y-m-d\TH:i',
                'before_or_equal:now',
            ],
            'consent_confirmed' => ['accepted'],
            'notes' => ['nullable', 'string', 'max:10000'],
        ]);

        $signedAt = Carbon::createFromFormat(
            'Y-m-d\TH:i',
            $data['signed_at'],
            config('app.timezone')
        )->startOfMinute();

        DB::transaction(function () use (
            $patient,
            $request,
            $data,
            $signedAt
        ): void {
            // Serialize consent changes for this patient.
            $lockedPatient = Patient::query()
                ->whereKey($patient->getKey())
                ->lockForUpdate()
                ->firstOrFail();

            $version = ConsentVersion::query()
                ->whereKey($data['consent_version_id'])
                ->lockForUpdate()
                ->firstOrFail();

            if (
                ! $version->is_active ||
                ! $version->version_code ||
                ! $version->title ||
                ! $version->effective_date ||
                $version->effective_date->toDateString()
                    > $signedAt->toDateString()
            ) {
                throw ValidationException::withMessages([
                    'consent_version_id' =>
                        'Select an active version effective on the signing date.',
                ]);
            }

            $latest = $lockedPatient->consents()
                ->orderByDesc('id')
                ->first();

            if ($latest) {
                if (
                    $latest->signed_at &&
                    $signedAt->lt($latest->signed_at)
                ) {
                    throw ValidationException::withMessages([
                        'signed_at' =>
                            'A new consent cannot precede the latest recorded signing date.',
                    ]);
                }

                if (
                    $latest->withdrawn_at &&
                    $signedAt->lte($latest->withdrawn_at)
                ) {
                    throw ValidationException::withMessages([
                        'signed_at' =>
                            'New consent must be signed after the recorded withdrawal.',
                    ]);
                }

                if (
                    ! $latest->withdrawn_at &&
                    (int) $latest->consent_version_id === (int) $version->id
                ) {
                    throw ValidationException::withMessages([
                        'consent_version_id' =>
                            'The latest consent already uses this version and has not been withdrawn.',
                    ]);
                }
            }

            $consent = $lockedPatient->consents()->make([
                'consent_version_id' => $version->id,
                'signed_at' => $signedAt,
                'notes' => $data['notes'] ?? null,
            ]);

            $consent->recorded_by = $request->user()->id;
            $consent->save();
        });

        return redirect()
            ->route('patients.consents.index', $patient)
            ->with('success', 'Patient consent recorded successfully.');
    }

    public function withdraw(
        Request $request,
        Patient $patient,
        int $consent
    ): RedirectResponse {
        Gate::authorize('record-patient-consent');

        $data = $request->validate([
            'withdrawn_at' => [
                'required',
                'date_format:Y-m-d\TH:i',
                'before_or_equal:now',
            ],
            'withdrawal_reason' => [
                'required',
                'string',
                'max:10000',
            ],
        ]);

        $withdrawnAt = Carbon::createFromFormat(
            'Y-m-d\TH:i',
            $data['withdrawn_at'],
            config('app.timezone')
        )->startOfMinute();

        DB::transaction(function () use (
            $patient,
            $request,
            $consent,
            $data,
            $withdrawnAt
        ): void {
            $lockedPatient = Patient::query()
                ->whereKey($patient->getKey())
                ->lockForUpdate()
                ->firstOrFail();

            $record = $lockedPatient->consents()
                ->findOrFail($consent);

            $latestId = $lockedPatient->consents()->max('id');

            if (
                (int) $record->id !== (int) $latestId ||
                ! $record->signed_at ||
                $record->withdrawn_at
            ) {
                throw ValidationException::withMessages([
                    'consent' =>
                        'Only the latest signed, unwithdrawn consent can be withdrawn here.',
                ]);
            }

            if ($withdrawnAt->lt($record->signed_at)) {
                throw ValidationException::withMessages([
                    'withdrawn_at' =>
                        'Withdrawal cannot precede the signing date.',
                ]);
            }

            $record->withdrawn_at = $withdrawnAt;
            $record->withdrawn_by = $request->user()->id;
            $record->withdrawal_reason = $data['withdrawal_reason'];
            $record->save();
        });

        return redirect()
            ->route('patients.consents.index', $patient)
            ->with('success', 'Consent withdrawal recorded successfully.');
    }
}
@extends('layouts.patients')

@section('title', 'Patient Consent')

@section('content')
    <header class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
        <div>
            <h1 class="h2 fw-bold mb-1">Patient Consent</h1>

            <p class="text-secondary mb-0">
                {{ $patient->full_name }}
                · Code {{ $patient->study_code }}
            </p>
        </div>

        <a href="{{ route('patients.show', $patient) }}"
           class="btn btn-outline-secondary">
            Patient Profile
        </a>
    </header>

    <section class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4">
            <h2 class="h5 mb-3">Current Consent Status</h2>

            @if (! $latestConsent || ! $latestConsent->signed_at)
                <p class="text-secondary mb-0">
                    No signed consent recorded.
                </p>
            @elseif ($latestConsent->withdrawn_at)
                <span class="badge text-bg-danger">Withdrawn</span>

                <p class="mt-2 mb-0">
                    Withdrawn on
                    {{ $latestConsent->withdrawn_at->format('d M Y, h:i A') }}
                </p>
            @else
                <span class="badge text-bg-success">
                    Signed — not withdrawn
                </span>

                <p class="mt-2 mb-0">
                    Version:
                    {{ $latestConsent->consentVersion?->version_code ?? '—' }}
                    <br>
                    Signed on
                    {{ $latestConsent->signed_at->format('d M Y, h:i A') }}
                </p>
            @endif
        </div>
    </section>

    @can('record-patient-consent')
        <section class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-4">
                <h2 class="h5 mb-3">Record Signed Consent</h2>

                @if ($versions->isEmpty())
                    <p class="text-secondary mb-0">
                        No active consent versions are available.
                        Ask Admin to create and activate a version.
                    </p>
                @else
                    <p class="text-secondary">
                        Enter details from the signed consent document.
                        Timezone: {{ config('app.timezone') }}.
                    </p>

                    <form method="POST"
                          action="{{ route('patients.consents.store', $patient) }}">
                        @csrf

                        <div class="mb-3">
                            <label for="consent_version_id" class="form-label">
                                Consent Version
                            </label>

                            <select id="consent_version_id"
                                    name="consent_version_id"
                                    class="form-select"
                                    required>
                                <option value="">Select version</option>

                                @foreach ($versions as $version)
                                    <option value="{{ $version->id }}"
                                            @selected((string) old('consent_version_id') === (string) $version->id)>
                                        {{ $version->version_code }}
                                        — {{ $version->title }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="signed_at" class="form-label">
                                Signed Date and Time
                            </label>

                            <input id="signed_at"
                                   name="signed_at"
                                   type="datetime-local"
                                   class="form-control"
                                   value="{{ old('signed_at') }}"
                                   required>
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label">
                                Notes
                            </label>

                            <textarea id="notes"
                                      name="notes"
                                      class="form-control"
                                      rows="3"
                                      maxlength="10000">{{ old('notes') }}</textarea>
                        </div>

                        <div class="form-check mb-3">
                            <input id="consent_confirmed"
                                   name="consent_confirmed"
                                   type="checkbox"
                                   class="form-check-input"
                                   value="1"
                                   @checked(old('consent_confirmed'))
                                   required>

                            <label for="consent_confirmed"
                                   class="form-check-label">
                                I confirm consent was obtained and these
                                details match the signed document.
                            </label>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            Save Consent
                        </button>
                    </form>
                @endif
            </div>
        </section>
    @endcan

    <section class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4">
            <h2 class="h5 mb-3">Consent History</h2>

            @forelse ($consents as $record)
                <article class="border rounded-3 p-3 mb-3">
                    <h3 class="h6">
                        {{ $record->consentVersion?->version_code ?? 'Unknown version' }}
                        — {{ $record->consentVersion?->title ?? '—' }}
                    </h3>

                    <p class="text-secondary small">
                        Signed:
                        {{ $record->signed_at?->format('d M Y, h:i A') ?? '—' }}
                        <br>
                        Recorded by:
                        {{ $record->recordedBy?->name ?? 'User unavailable' }}
                    </p>

                    @if ($record->notes)
                        <p style="white-space: pre-wrap">{{ $record->notes }}</p>
                    @endif

                    @if ($record->withdrawn_at)
                        <span class="badge text-bg-danger">Withdrawn</span>

                        <p class="small mt-2 mb-2">
                            {{ $record->withdrawn_at->format('d M Y, h:i A') }}
                            <br>
                            Recorded by:
                            {{ $record->withdrawnBy?->name ?? 'User unavailable' }}
                        </p>

                        <p class="mb-0" style="white-space: pre-wrap">{{ $record->withdrawal_reason }}</p>
                    @elseif ($record->signed_at)
                        @if ($record->id === $latestConsent?->id)
                            <span class="badge text-bg-success">
                                Current — not withdrawn
                            </span>

                            @can('record-patient-consent')
                                <form method="POST"
                                      action="{{ route('patients.consents.withdraw', [$patient, $record]) }}"
                                      class="mt-3"
                                      onsubmit="return confirm('Record withdrawal of this patient’s consent?');">
                                    @csrf
                                    @method('PATCH')

                                    <div class="mb-3">
                                        <label for="withdrawn_at_{{ $record->id }}"
                                               class="form-label">
                                            Withdrawal Date and Time
                                            ({{ config('app.timezone') }})
                                        </label>

                                        <input id="withdrawn_at_{{ $record->id }}"
                                               name="withdrawn_at"
                                               type="datetime-local"
                                               class="form-control"
                                               value="{{ old('withdrawn_at') }}"
                                               required>
                                    </div>

                                    <div class="mb-3">
                                        <label for="withdrawal_reason_{{ $record->id }}"
                                               class="form-label">
                                            Withdrawal Reason
                                        </label>

                                        <textarea id="withdrawal_reason_{{ $record->id }}"
                                                  name="withdrawal_reason"
                                                  class="form-control"
                                                  rows="3"
                                                  maxlength="10000"
                                                  required>{{ old('withdrawal_reason') }}</textarea>
                                    </div>

                                    <button type="submit"
                                            class="btn btn-outline-danger">
                                        Record Withdrawal
                                    </button>
                                </form>
                            @endcan
                        @else
                            <span class="badge text-bg-secondary">
                                Earlier consent record
                            </span>
                        @endif
                    @endif
                </article>
            @empty
                <p class="text-secondary mb-0">
                    No consent records.
                </p>
            @endforelse

            @if ($consents->hasPages())
                {{ $consents->links('pagination::bootstrap-5') }}
            @endif
        </div>
    </section>
@endsection
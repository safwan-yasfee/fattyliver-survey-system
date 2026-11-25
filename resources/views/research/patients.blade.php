@extends('layouts.patients')

@section('title', 'Research Patient List')

@section('content')
    @php
        $eligibilityLabels = [
            'eligible' => 'Eligible',
            'pending' => 'Pending Review',
            'not_eligible' => 'Not Eligible',
        ];

        $eligibilityColors = [
            'eligible' => 'success',
            'pending' => 'warning',
            'not_eligible' => 'danger',
        ];
    @endphp

    <header class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
        <div>
            <h1 class="h2 fw-bold mb-1">Research Patient List</h1>

            <p class="text-secondary mb-0">
                {{ $patients->total() }} matching active patient records
            </p>
        </div>

        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('dashboard.research.patients.export', [
        'search' => $search,
        'eligibility' => $eligibility,
        'consent' => $consent,
    ]) }}" class="btn btn-primary">
                Export Filtered Results CSV
            </a>

            <a href="{{ route('dashboard.research') }}" class="btn btn-outline-secondary">
                Research Dashboard
            </a>
        </div>
    </header>

    <form method="GET" action="{{ route('dashboard.research.patients') }}" class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4">
            <div class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label for="search" class="form-label">
                        Patient Code
                    </label>

                    <input id="search" name="search" type="search" class="form-control" maxlength="30" value="{{ $search }}"
                        placeholder="Search by code">
                </div>

                <div class="col-md-4">
                    <label for="eligibility" class="form-label">
                        Eligibility
                    </label>

                    <select id="eligibility" name="eligibility" class="form-select">
                        <option value="all" @selected($eligibility === 'all')>
                            All
                        </option>

                        <option value="unreviewed" @selected($eligibility === 'unreviewed')>
                            Not Reviewed
                        </option>

                        @foreach ($eligibilityLabels as $value => $label)
                            <option value="{{ $value }}" @selected($eligibility === $value)>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4">
                    <label for="consent" class="form-label">
                        Consent
                    </label>

                    <select id="consent" name="consent" class="form-select">
                        <option value="all" @selected($consent === 'all')>
                            All
                        </option>

                        <option value="signed" @selected($consent === 'signed')>
                            Signed — Not Withdrawn
                        </option>

                        <option value="withdrawn" @selected($consent === 'withdrawn')>
                            Withdrawn
                        </option>

                        <option value="not_recorded" @selected($consent === 'not_recorded')>
                            No Signed Consent
                        </option>
                    </select>
                </div>

                <div class="col-12">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            Apply Filters
                        </button>

                        <a href="{{ route('dashboard.research.patients') }}" class="btn btn-outline-secondary">
                            Reset
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <section class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4">
            <p class="text-secondary small">
                Eligibility and consent reflect each patient's latest record.
            </p>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th scope="col">Code</th>
                            <th scope="col">Eligibility</th>
                            <th scope="col">Consent</th>
                            <th scope="col">Consent Version</th>
                            <th scope="col">Completed Visits</th>
                            <th scope="col">Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($patients as $patient)
                            @php
                                $review = $patient->latestEligibilityReview;
                                $latestConsent = $patient->latestConsent;
                            @endphp

                            <tr>
                                <td>
                                    <a href="{{ route('patients.show', $patient) }}">
                                        {{ $patient->study_code }}
                                    </a>
                                </td>

                                <td>
                                    @if ($review)
                                        <span class="badge text-bg-{{ $eligibilityColors[$review->status] ?? 'secondary' }}">
                                            {{ $eligibilityLabels[$review->status] ?? 'Unknown Status' }}
                                        </span>
                                    @else
                                        <span class="badge text-bg-secondary">
                                            Not Reviewed
                                        </span>
                                    @endif
                                </td>

                                <td>
                                    @if (!$latestConsent || !$latestConsent->signed_at)
                                        <span class="badge text-bg-secondary">
                                            No Signed Consent
                                        </span>
                                    @elseif ($latestConsent->withdrawn_at)
                                        <span class="badge text-bg-danger">
                                            Withdrawn
                                        </span>
                                    @else
                                        <span class="badge text-bg-success">
                                            Signed — Not Withdrawn
                                        </span>
                                    @endif
                                </td>

                                <td>
                                    {{ $latestConsent?->consentVersion?->version_code ?? '—' }}
                                </td>

                                <td>
                                    {{ $patient->completed_visits_count }} / 4
                                </td>

                                <td>
                                    <div class="d-flex flex-wrap gap-2">
                                        <a href="{{ route('patients.eligibility.edit', $patient) }}"
                                            class="btn btn-sm btn-outline-primary">
                                            Eligibility
                                        </a>

                                        <a href="{{ route('patients.consents.index', $patient) }}"
                                            class="btn btn-sm btn-outline-secondary">
                                            Consent
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">
                                    No patients match the selected filters.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($patients->hasPages())
                <div class="mt-3">
                    {{ $patients->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>
    </section>
@endsection
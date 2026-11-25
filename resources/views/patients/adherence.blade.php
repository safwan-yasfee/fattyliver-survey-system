@extends('layouts.patients')

@section('title', 'Medication Adherence')

@section('content')
    <header class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
        <div>
            <h1 class="h2 fw-bold mb-1">Medication Adherence</h1>

            <p class="text-secondary mb-0">
                {{ $patient->full_name }}
                &middot; Code {{ $patient->study_code }}
                &middot;
                {{ ucwords(str_replace('_', ' ', $studyVisit->time_point)) }}
            </p>
        </div>

        @php
            $adherenceCategory = \App\Services\ClinicalCalculator::adherenceCategory(
                $assessment?->tablets_expected,
                $assessment?->tablets_taken
            );
        @endphp

        <div class="alert alert-info">
            <strong>Saved adherence:</strong>

            {{ $assessment?->adherence_percentage !== null
        ? number_format((float) $assessment->adherence_percentage, 2) . '%'
        : 'Not calculated' }}

            <br>

            <strong>Category:</strong>
            {{ $adherenceCategory ?? 'Not calculated' }}
        </div>




        <a href="{{ route('patients.visits.treatments.index', [$patient, $studyVisit]) }}"
            class="btn btn-outline-secondary">
            Treatment List
        </a>
    </header>

    <section class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4">
            <h2 class="h5">{{ $medication->medication_name }}</h2>

            <p class="text-secondary mb-0">
                Dose: {{ $medication->dose ?? 'Not recorded' }}
                &middot;
                Frequency: {{ $medication->frequency ?? 'Not recorded' }}
            </p>
        </div>
    </section>

    <section class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4">
            <h2 class="h5 mb-3">Adherence Assessment</h2>

            <p class="text-secondary">
                For tablet medication, enter expected and taken counts
                for the same assessment period.
                Leave both blank if counts are unavailable or not applicable.
            </p>

            @if ($assessment?->adherence_percentage !== null)
                <div class="alert alert-info">
                    Saved tablet-count adherence:
                    <strong>{{ $assessment->adherence_percentage }}%</strong>
                </div>
            @endif

            <form method="POST"
                action="{{ route('patients.visits.treatments.adherence.update', [$patient, $studyVisit, $medication]) }}">
                @csrf
                @method('PUT')

                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="assessment_date" class="form-label">
                            Assessment Date
                        </label>

                        <input id="assessment_date" name="assessment_date" type="date" class="form-control"
                            max="{{ now()->toDateString() }}"
                            value="{{ old('assessment_date', $assessment?->assessment_date?->format('Y-m-d')) }}" required>
                    </div>

                    <div class="col-md-6">
                        <label for="tablets_expected" class="form-label">
                            Tablets Expected
                        </label>

                        <input id="tablets_expected" name="tablets_expected" type="number" min="1" max="4294967295" step="1"
                            class="form-control" value="{{ old('tablets_expected', $assessment?->tablets_expected) }}">
                    </div>

                    <div class="col-md-6">
                        <label for="tablets_taken" class="form-label">
                            Tablets Taken
                        </label>

                        <input id="tablets_taken" name="tablets_taken" type="number" min="0" step="1" class="form-control"
                            value="{{ old('tablets_taken', $assessment?->tablets_taken) }}">
                    </div>



                    <div class="col-12">
                        <label for="notes" class="form-label">
                            Assessment Period and Notes
                        </label>

                        <textarea id="notes" name="notes" rows="4" maxlength="10000" class="form-control"
                            placeholder="Record the period assessed, source of counts and relevant notes.">{{ old('notes', $assessment?->notes) }}</textarea>
                    </div>
                </div>

                <div class="d-flex flex-wrap gap-2 mt-4">
                    <button type="submit" class="btn btn-primary">
                        Save Adherence
                    </button>

                    <a href="{{ route('patients.visits.treatments.index', [$patient, $studyVisit]) }}"
                        class="btn btn-outline-secondary">
                        Back to Treatments
                    </a>
                </div>
            </form>
        </div>
    </section>
@endsection
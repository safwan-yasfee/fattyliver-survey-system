@extends('layouts.patients')

@section('title', 'Imaging / Elastography')

@section('content')
    @php
        $timePoints = [
            'baseline' => 'Baseline',
            'month_3' => '3 Months',
            'month_6' => '6 Months',
            'month_12' => '12 Months',
        ];
    @endphp

    <header class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
        <div>
            <h1 class="h2 fw-bold mb-1">Imaging / Elastography</h1>

            <p class="text-secondary mb-0">
                {{ $patient->full_name }}
                &middot; Code {{ $patient->study_code }}
                &middot;
                {{ $timePoints[$studyVisit->time_point] ?? $studyVisit->time_point }}
            </p>
        </div>

        <a href="{{ route('patients.show', $patient) }}"
           class="btn btn-outline-secondary">
            Patient Profile
        </a>
    </header>

    <section class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4">
            <h2 class="h5 mb-2">Elastography Report</h2>

            <p class="text-secondary mb-4">
                Enter the measurements and findings from the report.
                Leave unavailable findings blank.
            </p>

            <form method="POST"
                  action="{{ route('patients.visits.elastography.update', [$patient, $studyVisit]) }}">
                @csrf
                @method('PUT')

                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="assessment_date" class="form-label">
                            Assessment Date
                        </label>

                        <input id="assessment_date"
                               name="assessment_date"
                               type="date"
                               class="form-control @error('assessment_date') is-invalid @enderror"
                               value="{{ old('assessment_date', $result?->assessment_date?->format('Y-m-d')) }}"
                               max="{{ now()->toDateString() }}"
                               required>

                        @error('assessment_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="lsm_kpa" class="form-label">
                            Liver Stiffness Measurement — kPa
                        </label>

                        <input id="lsm_kpa"
                               name="lsm_kpa"
                               type="number"
                               step="0.01"
                               min="0"
                               max="999999.99"
                               class="form-control @error('lsm_kpa') is-invalid @enderror"
                               value="{{ old('lsm_kpa', $result?->lsm_kpa) }}">

                        @error('lsm_kpa')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="cap_db_m" class="form-label">
                            CAP Measurement — dB/m
                        </label>

                        <input id="cap_db_m"
                               name="cap_db_m"
                               type="number"
                               step="0.01"
                               min="0"
                               max="999999.99"
                               class="form-control @error('cap_db_m') is-invalid @enderror"
                               value="{{ old('cap_db_m', $result?->cap_db_m) }}">

                        @error('cap_db_m')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="fibrosis_stage" class="form-label">
                            Reported Fibrosis Stage
                        </label>

                        <input id="fibrosis_stage"
                               name="fibrosis_stage"
                               type="text"
                               maxlength="30"
                               class="form-control @error('fibrosis_stage') is-invalid @enderror"
                               value="{{ old('fibrosis_stage', $result?->fibrosis_stage) }}">

                        @error('fibrosis_stage')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="steatosis_grade" class="form-label">
                            Reported Steatosis Grade
                        </label>

                        <input id="steatosis_grade"
                               name="steatosis_grade"
                               type="text"
                               maxlength="30"
                               class="form-control @error('steatosis_grade') is-invalid @enderror"
                               value="{{ old('steatosis_grade', $result?->steatosis_grade) }}">

                        @error('steatosis_grade')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <label for="notes" class="form-label">
                            Report Notes
                        </label>

                        <textarea id="notes"
                                  name="notes"
                                  rows="4"
                                  maxlength="10000"
                                  class="form-control @error('notes') is-invalid @enderror">{{ old('notes', $result?->notes) }}</textarea>

                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="d-flex flex-wrap gap-2 mt-4">
                    <button type="submit" class="btn btn-primary">
                        Save Elastography Results
                    </button>

                    <a href="{{ route('patients.show', $patient) }}"
                       class="btn btn-outline-secondary">
                        Back to Patient
                    </a>
                </div>
            </form>
        </div>
    </section>
@endsection
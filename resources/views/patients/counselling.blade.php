@extends('layouts.patients')

@section('title', 'Diet & Exercise Counselling')

@section('content')
    @php
        $timePoints = [
            'baseline' => 'Baseline',
            'month_3' => '3 Months',
            'month_6' => '6 Months',
            'month_12' => '12 Months',
        ];

        $fields = [
            'diet_advice' => [
                'label' => 'Dietary Advice Provided',
                'help' => 'Record the dietary advice given during this visit.',
            ],
            'exercise_advice' => [
                'label' => 'Exercise Advice Provided',
                'help' => 'Record the activity advice given during this visit.',
            ],
            'goals' => [
                'label' => 'Agreed Goals',
                'help' => 'Record the goals discussed with the patient.',
            ],
            'notes' => [
                'label' => 'Additional Notes',
                'help' => 'Record relevant concerns, barriers or other details.',
            ],
        ];
    @endphp

    <header class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
        <div>
            <h1 class="h2 fw-bold mb-1">
                Diet &amp; Exercise Counselling
            </h1>

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
            <h2 class="h5 mb-2">Counselling Record</h2>

            <p class="text-secondary mb-4">
                Record the advice provided by staff and the goals
                discussed with the patient.
            </p>

            @if ($plan)
                <p class="small text-secondary">
                    Originally recorded by:
                    {{ $plan->recordedBy?->name ?? 'Unavailable' }}
                </p>
            @endif

            <form method="POST"
                  action="{{ route('patients.visits.counselling.update', [$patient, $studyVisit]) }}">
                @csrf
                @method('PUT')

                <div class="mb-4">
                    <label for="counselling_date" class="form-label fw-semibold">
                        Counselling Date
                    </label>

                    <input id="counselling_date"
                           name="counselling_date"
                           type="date"
                           class="form-control @error('counselling_date') is-invalid @enderror"
                           value="{{ old('counselling_date', $plan?->counselling_date?->format('Y-m-d')) }}"
                           max="{{ now()->toDateString() }}"
                           required>

                    @error('counselling_date')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                @foreach ($fields as $field => $settings)
                    <div class="mb-4">
                        <label for="{{ $field }}"
                               class="form-label fw-semibold">
                            {{ $settings['label'] }}
                        </label>

                        <textarea id="{{ $field }}"
                                  name="{{ $field }}"
                                  rows="4"
                                  maxlength="10000"
                                  aria-describedby="{{ $field }}_help"
                                  class="form-control @error($field) is-invalid @enderror">{{ old($field, $plan?->{$field}) }}</textarea>

                        <div id="{{ $field }}_help" class="form-text">
                            {{ $settings['help'] }}
                        </div>

                        @error($field)
                            <div class="invalid-feedback">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                @endforeach

                <div class="d-flex flex-wrap gap-2">
                    <button type="submit" class="btn btn-primary">
                        Save Counselling
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
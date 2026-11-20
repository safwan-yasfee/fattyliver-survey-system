@extends('layouts.patients')

@section('title', 'Visit Vitals')

@section('content')
    @php
        $timePoints = [
            'baseline' => 'Baseline',
            'month_3' => '3 Months',
            'month_6' => '6 Months',
            'month_12' => '12 Months',
        ];

        $fields = [
            'weight_kg' => ['label' => 'Weight in kg', 'step' => '0.01'],
            'height_cm' => ['label' => 'Height in cm', 'step' => '0.01'],
            'waist_cm' => ['label' => 'Waist in cm', 'step' => '0.01'],
            'systolic_bp' => ['label' => 'Systolic BP in mmHg', 'step' => '1'],
            'diastolic_bp' => ['label' => 'Diastolic BP in mmHg', 'step' => '1'],
            'heart_rate' => ['label' => 'Heart Rate in bpm', 'step' => '1'],
        ];
    @endphp

    <header class="d-flex justify-content-between align-items-center
                   flex-wrap gap-3 mb-4">
        <div>
            <h1 class="h2 mb-1">Visit Vitals</h1>

            <p class="text-secondary mb-0">
                {{ $patient->full_name }}
                · {{ $timePoints[$studyVisit->time_point] ?? $studyVisit->time_point }}
            </p>
        </div>

        <a href="{{ route('patients.show', $patient) }}"
           class="btn btn-outline-secondary">
            Patient Profile
        </a>
    </header>

    <section class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4">
            <form
                method="POST"
                action="{{ route('patients.visits.vitals.update', [$patient, $studyVisit]) }}"
            >
                @csrf
                @method('PUT')

                <div class="row g-3">
                    @foreach ($fields as $field => $settings)
                        <div class="col-md-6">
                            <label for="{{ $field }}" class="form-label">
                                {{ $settings['label'] }}
                            </label>

                            <input
                                id="{{ $field }}"
                                name="{{ $field }}"
                                type="number"
                                step="{{ $settings['step'] }}"
                                class="form-control"
                                value="{{ old($field, $vitals?->{$field}) }}"
                            >
                        </div>
                    @endforeach
                </div>

                <button type="submit" class="btn btn-primary mt-4">
                    Save Vitals
                </button>
            </form>
        </div>
    </section>

    <section class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4">
            <h2 class="h5">Visit Status</h2>

            @if ($studyVisit->visited_at)
                <p class="mb-0 text-success">
                    Completed on
                    {{ $studyVisit->visited_at->format('d M Y H:i') }}
                </p>
            @else
                <p class="text-secondary">
                    Save the measurements before marking this visit complete.
                </p>

                <form
                    method="POST"
                    action="{{ route('patients.visits.complete', [$patient, $studyVisit]) }}"
                >
                    @csrf
                    @method('PATCH')

                    <button type="submit" class="btn btn-success">
                        Mark Visit Complete
                    </button>
                </form>
            @endif
        </div>
    </section>
@endsection
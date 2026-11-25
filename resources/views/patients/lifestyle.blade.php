@extends('layouts.patients')

@section('title', 'Lifestyle & Diet')

@section('content')
    @php
        $fields = [
            'physical_activity' => [
                'label' => 'Physical Activity',
                'help' => 'Record activity type, frequency and duration.',
                'placeholder' => 'Describe the patient’s reported exercise habits.',
            ],
            'dietary_habits' => [
                'label' => 'Dietary Habits',
                'help' => 'Record meal patterns, food choices and relevant dietary habits.',
                'placeholder' => 'Describe the patient’s usual diet.',
            ],
            'smoking_history' => [
                'label' => 'Smoking History',
                'help' => 'Record reported smoking status, amount and duration, if known.',
                'placeholder' => 'Enter the patient’s reported smoking history.',
            ],
            'notes' => [
                'label' => 'Additional Notes',
                'help' => 'Record other relevant lifestyle information.',
                'placeholder' => 'Enter additional notes.',
            ],
        ];
    @endphp

    <header class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
        <div>
            <h1 class="h2 fw-bold mb-1">Lifestyle &amp; Diet</h1>

            <p class="text-secondary mb-0">
                {{ $patient->full_name }}
                &middot; Code {{ $patient->study_code }}
            </p>
        </div>

        <a href="{{ route('patients.show', $patient) }}"
           class="btn btn-outline-secondary">
            Patient Profile
        </a>
    </header>

    <section class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4">
            <h2 class="h5 mb-2">Lifestyle Information</h2>

            <p class="text-secondary mb-4">
                Record the patient's reported habits.
                Leave unknown information blank.
            </p>

            <form method="POST"
                  action="{{ route('patients.lifestyle.update', $patient) }}">
                @csrf
                @method('PUT')

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
                                  placeholder="{{ $settings['placeholder'] }}"
                                  aria-describedby="{{ $field }}_help"
                                  class="form-control @error($field) is-invalid @enderror">{{ old($field, $lifestyle?->{$field}) }}</textarea>

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
                        Save Lifestyle &amp; Diet
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
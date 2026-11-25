@extends('layouts.patients')

@section('title', 'Medical History')

@section('content')
    @php
        $fields = [
            'diseases' => [
                'label' => 'Diseases and Conditions',
                'help' => 'Record known conditions and relevant diagnoses.',
            ],
            'alcohol_history' => [
                'label' => 'Alcohol History',
                'help' => 'Record reported alcohol use and relevant dates.',
            ],
            'drug_history' => [
                'label' => 'Medication and Drug History',
                'help' => 'Record current or previous medicines and other relevant drug use.',
            ],
            'biopsy_history' => [
                'label' => 'Biopsy History',
                'help' => 'Record previous biopsy dates and reported findings.',
            ],
            'notes' => [
                'label' => 'Additional Notes',
                'help' => 'Record other relevant medical history.',
            ],
        ];
    @endphp

    <header class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
        <div>
            <h1 class="h2 fw-bold mb-1">Medical History</h1>

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
            <p class="text-secondary mb-4">
                Enter the patient's available history.
                Leave unknown information blank.
            </p>

            <form method="POST"
                  action="{{ route('patients.medical-history.update', $patient) }}">
                @csrf
                @method('PUT')

                @foreach ($fields as $field => $settings)
                    <div class="mb-4">
                        <label for="{{ $field }}" class="form-label fw-semibold">
                            {{ $settings['label'] }}
                        </label>

                        <textarea id="{{ $field }}"
                                  name="{{ $field }}"
                                  rows="3"
                                  maxlength="10000"
                                  aria-describedby="{{ $field }}_help"
                                  class="form-control @error($field) is-invalid @enderror">{{ old($field, $history?->{$field}) }}</textarea>

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
                        Save Medical History
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
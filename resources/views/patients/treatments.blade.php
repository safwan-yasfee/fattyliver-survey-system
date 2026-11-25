@extends('layouts.patients')

@section('title', 'Treatment & Medication')

@section('content')
    @php
        $timePoints = [
            'baseline' => 'Baseline',
            'month_3' => '3 Months',
            'month_6' => '6 Months',
            'month_12' => '12 Months',
        ];

        $statuses = [
            'active' => 'Active',
            'paused' => 'Paused',
            'completed' => 'Completed',
            'stopped' => 'Stopped',
        ];

        $textFields = [
            'dose' => 'Prescribed Dose',
            'frequency' => 'Frequency',
            'route' => 'Administration Route',
        ];
    @endphp

    <header class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
        <div>
            <h1 class="h2 fw-bold mb-1">Treatment &amp; Medication</h1>

            <p class="text-secondary mb-0">
                {{ $patient->full_name }}
                &middot; Code {{ $patient->study_code }}
                &middot;
                {{ $timePoints[$studyVisit->time_point] ?? $studyVisit->time_point }}
            </p>
        </div>

        <a href="{{ route('patients.show', $patient) }}" class="btn btn-outline-secondary">
            Patient Profile
        </a>
    </header>

    <section class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4">
            <h2 class="h5 mb-2">
                {{ $treatment ? 'Edit Treatment' : 'Add Treatment' }}
            </h2>

            <p class="text-secondary mb-4">
                Record medication details from the prescription.
                Include the dose unit when entering the prescribed dose.
            </p>

            <form method="POST" action="{{ $treatment
        ? route('patients.visits.treatments.update', [$patient, $studyVisit, $treatment])
        : route('patients.visits.treatments.store', [$patient, $studyVisit]) }}">
                @csrf

                @if ($treatment)
                    @method('PUT')
                @endif

                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="medication_name" class="form-label">
                            Medication Name
                        </label>

                        <input id="medication_name" name="medication_name" type="text" maxlength="255" class="form-control"
                            value="{{ old('medication_name', $treatment?->medication_name) }}" required>
                    </div>

                    <div class="col-md-6">
                        <label for="status" class="form-label">
                            Treatment Status
                        </label>

                        <select id="status" name="status" class="form-select" required>
                            <option value="">Select status</option>

                            @foreach ($statuses as $value => $label)
                                <option value="{{ $value }}" @selected(old('status', $treatment?->status) === $value)>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    @foreach ($textFields as $field => $label)
                        <div class="col-md-4">
                            <label for="{{ $field }}" class="form-label">
                                {{ $label }}
                            </label>

                            <input id="{{ $field }}" name="{{ $field }}" type="text"
                                maxlength="{{ $field === 'route' ? 50 : 100 }}" class="form-control"
                                value="{{ old($field, $treatment?->{$field}) }}">
                        </div>
                    @endforeach

                    <div class="col-md-6">
                        <label for="start_date" class="form-label">
                            Start Date
                        </label>

                        <input id="start_date" name="start_date" type="date" class="form-control"
                            value="{{ old('start_date', $treatment?->start_date?->format('Y-m-d')) }}" required>
                    </div>

                    <div class="col-md-6">
                        <label for="end_date" class="form-label">
                            End Date
                        </label>

                        <input id="end_date" name="end_date" type="date" class="form-control"
                            value="{{ old('end_date', $treatment?->end_date?->format('Y-m-d')) }}">

                        <div class="form-text">
                            Leave blank if no end date is recorded.
                        </div>
                    </div>

                    <div class="col-12">
                        <label for="notes" class="form-label">
                            Notes
                        </label>

                        <textarea id="notes" name="notes" rows="3" maxlength="10000"
                            class="form-control">{{ old('notes', $treatment?->notes) }}</textarea>
                    </div>
                </div>

                <div class="d-flex flex-wrap gap-2 mt-4">
                    <button type="submit" class="btn btn-primary">
                        {{ $treatment ? 'Save Changes' : 'Save Treatment' }}
                    </button>

                    <a href="{{ route('patients.visits.treatments.index', [$patient, $studyVisit]) }}"
                        class="btn btn-outline-secondary">
                        {{ $treatment ? 'Cancel Edit' : 'Clear Form' }}
                    </a>
                </div>
            </form>
        </div>
    </section>

    <section class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4">
            <h2 class="h5 mb-3">Recorded Treatments</h2>

            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th scope="col">Medication</th>
                            <th scope="col">Dose</th>
                            <th scope="col">Frequency / Route</th>
                            <th scope="col">Dates</th>
                            <th scope="col">Status</th>
                            <th scope="col">Recorded By</th>
                            <th scope="col">Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($treatments as $record)
                                            <tr>
                                                <td>
                                                    <div class="fw-semibold">
                                                        {{ $record->medication_name }}
                                                    </div>

                                                    @if ($record->notes)
                                                        <div class="small text-secondary">
                                                            {{ $record->notes }}
                                                        </div>
                                                    @endif
                                                </td>

                                                <td>{{ $record->dose ?? '—' }}</td>

                                                <td>
                                                    {{ $record->frequency ?? '—' }}
                                                    <div class="small text-secondary">
                                                        {{ $record->route ?? '—' }}
                                                    </div>
                                                </td>

                                                <td>
                                                    {{ $record->start_date?->format('d M Y') ?? '—' }}
                                                    <div class="small text-secondary">
                                                        End:
                                                        {{ $record->end_date?->format('d M Y') ?? 'Not recorded' }}
                                                    </div>
                                                </td>

                                                <td>
                                                    {{ $statuses[$record->status] ?? 'Not recorded' }}
                                                </td>

                                                <td>
                                                    {{ $record->recordedBy?->name ?? 'Unavailable' }}
                                                </td>

                                                <td>
                                                    <a href="{{ route('patients.visits.treatments.index', [
                                'patient' => $patient,
                                'visit' => $studyVisit->getKey(),
                                'treatment' => $record->id,
                            ]) }}" class="btn btn-sm btn-outline-primary">
                                                        Edit
                                                    </a>

                                                    <a href="{{ route('patients.visits.treatments.adherence.edit', [$patient, $studyVisit, $record]) }}"
                                                        class="btn btn-sm btn-outline-success">
                                                        Adherence
                                                    </a>
                                                </td>
                                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    No treatment records for this visit.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>
@endsection
@extends('layouts.patients')

@section('title', 'Lab Results')

@section('content')
    <header class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
        <div>
            <h1 class="h2 fw-bold mb-1">Lab Results</h1>

            <p class="text-secondary mb-0">
                {{ $patient->full_name }}
                &middot; Code {{ $patient->study_code }}
                &middot;
                {{ ucwords(str_replace('_', ' ', $studyVisit->time_point)) }}
            </p>
        </div>

        <a href="{{ route('patients.show', $patient) }}"
           class="btn btn-outline-secondary">
            Patient Profile
        </a>
    </header>

    <section class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4">
            <h2 class="h5 mb-3">
                {{ $selectedResult ? 'Update Result' : 'Record Result' }}
            </h2>

            @if ($tests->isEmpty())
                <div class="alert alert-info mb-0">
                    No active lab tests have been added yet.
                    Add tests to the lab test catalogue first.
                </div>
            @else
                <p class="text-secondary">
                    Enter a numeric result, a text result, or both.
                    Use the unit shown on the laboratory report.
                </p>

                <form method="POST"
                      action="{{ route('patients.visits.labs.update', [$patient, $studyVisit]) }}">
                    @csrf
                    @method('PUT')

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="lab_test_id" class="form-label">
                                Lab Test
                            </label>

                            <select id="lab_test_id"
                                    name="lab_test_id"
                                    class="form-select"
                                    required>
                                <option value="">Select a test</option>

                                @foreach ($tests as $test)
                                    <option value="{{ $test->id }}"
                                        @selected(
                                            (string) old('lab_test_id', $selectedResult?->lab_test_id)
                                            === (string) $test->id
                                        )>
                                        {{ $test->name }} ({{ $test->test_code }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label for="tested_at" class="form-label">
                                Test Date
                            </label>

                            <input id="tested_at"
                                   name="tested_at"
                                   type="date"
                                   class="form-control"
                                   max="{{ now()->toDateString() }}"
                                   value="{{ old('tested_at', $selectedResult?->tested_at?->format('Y-m-d')) }}"
                                   required>
                        </div>

                        <div class="col-md-6">
                            <label for="result_value" class="form-label">
                                Numeric Result
                            </label>

                            <input id="result_value"
                                   name="result_value"
                                   type="number"
                                   step="0.0001"
                                   class="form-control"
                                   value="{{ old('result_value', $selectedResult?->result_value) }}">
                        </div>

                        <div class="col-md-6">
                            <label for="unit" class="form-label">
                                Reported Unit
                            </label>

                            <input id="unit"
                                   name="unit"
                                   type="text"
                                   maxlength="50"
                                   class="form-control"
                                   value="{{ old('unit', $selectedResult?->unit) }}">
                        </div>

                        <div class="col-12">
                            <label for="result_text" class="form-label">
                                Text Result
                            </label>

                            <textarea id="result_text"
                                      name="result_text"
                                      rows="2"
                                      maxlength="10000"
                                      class="form-control">{{ old('result_text', $selectedResult?->result_text) }}</textarea>
                        </div>

                        <div class="col-12">
                            <label for="notes" class="form-label">
                                Notes
                            </label>

                            <textarea id="notes"
                                      name="notes"
                                      rows="2"
                                      maxlength="10000"
                                      class="form-control">{{ old('notes', $selectedResult?->notes) }}</textarea>
                        </div>
                    </div>

                    <div class="d-flex flex-wrap gap-2 mt-4">
                        <button type="submit" class="btn btn-primary">
                            Save Lab Result
                        </button>

                        <a href="{{ route('patients.visits.labs.edit', [$patient, $studyVisit]) }}"
                           class="btn btn-outline-secondary">
                            Clear Form
                        </a>
                    </div>
                </form>
            @endif
        </div>
    </section>

    <section class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4">
            <h2 class="h5 mb-3">Saved Results</h2>

            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th scope="col">Test</th>
                            <th scope="col">Numeric Result</th>
                            <th scope="col">Text Result</th>
                            <th scope="col">Unit</th>
                            <th scope="col">Test Date</th>
                            <th scope="col">Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($results as $result)
                            <tr>
                                <td>{{ $result->labTest?->name ?? 'Unavailable test' }}</td>
                                <td>{{ $result->result_value ?? '—' }}</td>
                                <td>{{ $result->result_text ?? '—' }}</td>
                                <td>{{ $result->unit ?? '—' }}</td>
                                <td>{{ $result->tested_at?->format('d M Y') ?? '—' }}</td>
                                <td>
                                    @if ($result->labTest?->is_active)
                                        <a class="btn btn-sm btn-outline-primary"
                                           href="{{ route('patients.visits.labs.edit', [
                                               'patient' => $patient,
                                               'visit' => $studyVisit->getKey(),
                                               'test' => $result->lab_test_id,
                                           ]) }}">
                                            Edit
                                        </a>
                                    @else
                                        <span class="text-secondary">
                                            Inactive test
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">
                                    No lab results saved for this visit.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>
@endsection
@extends('layouts.patients')

@section('title', 'Patient List')

@section('content')
    <div class="d-flex justify-content-between align-items-center
                flex-wrap gap-3 mb-4">
        <div>
            <h1 class="h2 fw-bold mb-1">Patient List</h1>

            <p class="text-secondary mb-0">
                {{ $patients->total() }} registered patients
            </p>
        </div>

        <a href="{{ route('patients.create') }}" class="btn btn-primary">
            Add Patient
        </a>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th scope="col">Patient ID</th>
                            <th scope="col">Code</th>
                            <th scope="col">Full Name</th>
                            <th scope="col">Sex</th>
                            <th scope="col">Phone</th>
                            <th scope="col">Registered On</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($patients as $patient)
                            <tr>
                                <td>{{ $patient->patient_id }}</td>

                                <td>
                                    <a href="{{ route('patients.show', $patient) }}">
                                        {{ $patient->study_code }}
                                    </a>
                                </td>

                                <td>{{ $patient->full_name }}</td>
                                <td>{{ $patient->sex ?? '—' }}</td>
                                <td>{{ $patient->phone ?? '—' }}</td>

                                <td>
                                    {{ $patient->created_at?->format('d M Y') ?? '—' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">
                                    No patients on this page.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <nav class="d-flex justify-content-between align-items-center
                        flex-wrap gap-3 mt-3"
                 aria-label="Patient list pages">
                <span class="text-secondary">
                    Page {{ $patients->currentPage() }}
                    of {{ $patients->lastPage() }}
                </span>

                <div class="d-flex gap-2">
                    @if ($patients->previousPageUrl())
                        <a href="{{ $patients->previousPageUrl() }}"
                           class="btn btn-outline-secondary btn-sm">
                            Previous
                        </a>
                    @endif

                    @if ($patients->nextPageUrl())
                        <a href="{{ $patients->nextPageUrl() }}"
                           class="btn btn-outline-secondary btn-sm">
                            Next
                        </a>
                    @endif
                </div>
            </nav>
        </div>
    </div>
@endsection
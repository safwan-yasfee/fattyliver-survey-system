@extends('layouts.patients')

@section('title', 'Archived Patients')

@section('content')
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
        <div>
            <h1 class="h2 fw-bold mb-1">Archived Patients</h1>

            <p class="text-secondary mb-0">
                {{ $patients->total() }} archived patient records
            </p>
        </div>

        <a href="{{ route('patients.index') }}"
           class="btn btn-outline-primary">
            Active Patients
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
                            <th scope="col">Archived On</th>
                            <th scope="col">Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($patients as $patient)
                            <tr>
                                <td>{{ $patient->patient_id }}</td>

                                <td>{{ $patient->study_code }}</td>

                                <td>{{ $patient->full_name }}</td>

                                <td>{{ $patient->sex ?? '—' }}</td>

                                <td>
                                    {{ $patient->deleted_at?->format('d M Y, h:i A') ?? '—' }}
                                </td>

                                <td>
                                    @can('archive-patients')
                                        <form method="POST"
                                              action="{{ route('patients.restore', $patient->patient_id) }}"
                                              onsubmit="return confirm('Restore this patient to the active patient list?');">
                                            @csrf
                                            @method('PATCH')

                                            <button type="submit"
                                                    class="btn btn-sm btn-outline-success">
                                                Restore
                                            </button>
                                        </form>
                                    @endcan
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">
                                    No archived patients.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($patients->hasPages())
                <nav class="d-flex justify-content-between align-items-center flex-wrap gap-3 mt-3"
                     aria-label="Archived patient list pages">
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
            @endif
        </div>
    </div>
@endsection
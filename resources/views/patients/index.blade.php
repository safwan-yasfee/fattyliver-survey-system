@extends('layouts.patients')

@section('title', 'Patient List')

@section('content')
    @php
        $canEdit = auth()->user()->can('manage-patient-records');
        $canArchive = auth()->user()->can('archive-patients');
        $showActions = $canEdit || $canArchive;
        $hasFilters = $search !== '' || $sex !== '';
    @endphp

    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
        <div>
            <h1 class="h2 fw-bold mb-1">Patient List</h1>

            <p class="text-secondary mb-0">
                {{ $patients->total() }}
                {{ $hasFilters ? 'matching active patient records' : 'active patient records' }}
            </p>
        </div>

        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('patients.create') }}"
               class="btn btn-primary">
                Add Patient
            </a>

            @if ($canArchive)
                <a href="{{ route('patients.archived') }}"
                   class="btn btn-outline-secondary">
                    Archived Patients
                </a>
            @endif
        </div>
    </div>

    <form method="GET"
          action="{{ route('patients.index') }}"
          class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4">
            <div class="row g-3 align-items-end">
                <div class="col-md-6">
                    <label for="search" class="form-label">
                        Search Patients
                    </label>

                    <input id="search"
                           name="search"
                           type="search"
                           class="form-control"
                           maxlength="255"
                           value="{{ $search }}"
                           placeholder="Code, full name or phone">
                </div>

                <div class="col-md-3">
                    <label for="sex" class="form-label">Sex</label>

                    <select id="sex" name="sex" class="form-select">
                        <option value="">All</option>

                        @foreach (['Male', 'Female', 'Other'] as $option)
                            <option value="{{ $option }}"
                                    @selected($sex === $option)>
                                {{ $option }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            Search
                        </button>

                        <a href="{{ route('patients.index') }}"
                           class="btn btn-outline-secondary">
                            Reset
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>

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

                            @if ($showActions)
                                <th scope="col">Actions</th>
                            @endif
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

                                @if ($showActions)
                                    <td>
                                        <div class="d-flex flex-wrap gap-2">
                                            @if ($canEdit)
                                                <a href="{{ route('patients.edit', $patient) }}"
                                                   class="btn btn-sm btn-outline-primary">
                                                    Edit
                                                </a>
                                            @endif

                                            @if ($canArchive)
                                                <form method="POST"
                                                      action="{{ route('patients.destroy', $patient) }}"
                                                      onsubmit="return confirm('Archive this patient? Their records will be preserved.');">
                                                    @csrf
                                                    @method('DELETE')

                                                    <button type="submit"
                                                            class="btn btn-sm btn-outline-danger">
                                                        Archive
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ $showActions ? 7 : 6 }}"
                                    class="text-center py-4">
                                    <p class="mb-2">
                                        {{ $hasFilters
                                            ? 'No matching patients on this page.'
                                            : 'No active patients on this page.' }}
                                    </p>

                                    <a href="{{ route('patients.index') }}">
                                        View all active patients
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <nav class="d-flex justify-content-between align-items-center flex-wrap gap-3 mt-3"
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
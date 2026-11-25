@extends('layouts.dashboard')

@section('title', 'Clinical Dashboard')
@section('dashboard-title', 'Clinical Dashboard')

@section('dashboard-content')
    <div class="d-flex flex-wrap gap-2 mb-4">
        <a href="{{ route('patients.index') }}" class="btn btn-primary">
            Patient List
        </a>

        <a href="{{ route('patients.create') }}"
           class="btn btn-outline-secondary">
            Add Patient
        </a>

        @can('manage-users')
            <a href="{{ route('admin.users.index') }}"
               class="btn btn-outline-primary">
                User Management
            </a>
        @endcan
    </div>

    <div class="row g-3 mb-4">
        @foreach ($stats as $label => $count)
            <div class="col-6 col-lg-3">
                <section class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body p-4">
                        <h2 class="h6 text-secondary">{{ $label }}</h2>
                        <p class="fs-2 fw-bold mb-0">{{ $count }}</p>
                    </div>
                </section>
            </div>
        @endforeach
    </div>

    <section class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4">
            <h2 class="h5 mb-3">Visits Needing Attention</h2>

            @if ($attentionVisits->isEmpty())
                <p class="text-secondary mb-0">
                    No uncompleted visits with a due date.
                </p>
            @else
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead>
                            <tr>
                                <th scope="col">Patient</th>
                                <th scope="col">Code</th>
                                <th scope="col">Time Point</th>
                                <th scope="col">Due Date</th>
                                <th scope="col">Action</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($attentionVisits as $visit)
                                <tr>
                                    <td>{{ $visit->patient->full_name }}</td>
                                    <td>{{ $visit->patient->study_code }}</td>
                                    <td>{{ $visit->time_point }}</td>
                                    <td>
                                        {{ $visit->due_date->format('d M Y') }}
                                    </td>
                                    <td>
                                        <a href="{{ route('patients.visits.vitals.edit', [$visit->patient, $visit]) }}"
                                           class="btn btn-outline-primary btn-sm">
                                            Open Visit
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </section>

    <div class="row g-4">
        <div class="col-lg-7">
            <section class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-4">
                    <h2 class="h5 mb-3">Recently Added Patients</h2>

                    <div class="table-responsive">
                        <table class="table mb-0">
                            <thead>
                                <tr>
                                    <th scope="col">Code</th>
                                    <th scope="col">Full Name</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse ($recentPatients as $patient)
                                    <tr>
                                        <td>
                                            <a href="{{ route('patients.show', $patient) }}">
                                                {{ $patient->study_code }}
                                            </a>
                                        </td>
                                        <td>{{ $patient->full_name }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2">
                                            No patients registered.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
        </div>

        <div class="col-lg-5">
            <section class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-4">
                    <h2 class="h5 mb-3">
                        Open Clinical Alerts

                        @if ($alertsAvailable)
                            <span class="badge text-bg-danger">
                                {{ $openAlertCount }}
                            </span>
                        @endif
                    </h2>

                    @if (! $alertsAvailable)
                        <p class="text-secondary mb-0">
                            Clinical alerts await section setup.
                        </p>
                    @else
                        @forelse ($alerts as $alert)
                            <div class="border-bottom pb-3 mb-3">
                                <p class="fw-semibold mb-1">
                                    {{ $alert->alert_type }}
                                </p>

                                <p class="mb-0">{{ $alert->message }}</p>
                            </div>
                        @empty
                            <p class="text-secondary mb-0">
                                No open alerts recorded.
                            </p>
                        @endforelse
                    @endif
                </div>
            </section>
        </div>
    </div>
@endsection
@extends('layouts.dashboard')

@section('title', 'Research Dashboard')
@section('dashboard-title', 'Research Dashboard')

@section('dashboard-content')
    <div class="d-flex justify-content-between align-items-center
                        flex-wrap gap-3 mb-4">
        <p class="text-secondary mb-0">
            {{ $patientCount }} registered patients
        </p>

        <a href="{{ route('dashboard.research.export') }}" class="btn btn-primary">
            Export Visit Summary CSV
        </a>


        <a href="{{ route('dashboard.research.patients') }}" class="btn btn-outline-primary">
            Research Patient List
        </a>


        @can('manage-users')
            <a href="{{ route('admin.consent-versions.index') }}" class="btn btn-outline-primary">
                Consent Versions
            </a>
        @endcan
    </div>

    @if ($eligibility === null)
        <div class="alert alert-info">
            Eligibility totals await section setup.
        </div>
    @else
        <div class="row g-3 mb-4">
            @foreach ($eligibility as $label => $count)
                <div class="col-md-4">
                    <section class="card border-0 shadow-sm rounded-4 h-100">
                        <div class="card-body p-4">
                            <h2 class="h6 text-secondary">{{ $label }}</h2>
                            <p class="fs-2 fw-bold mb-0">{{ $count }}</p>
                        </div>
                    </section>
                </div>
            @endforeach
        </div>

        <p class="text-secondary">
            Patients without an eligibility review
            <strong>{{ $unreviewedCount }}</strong>
        </p>
    @endif

    <div class="row g-3 mb-4">
        @foreach ([
                'Consented Patients' => $consentedCount,
                'All Four Visits Completed' => $allVisitsCompleted,
                'Recorded Serious Adverse Events' => $seriousEventCount,
            ] as $label => $count)
            <div class="col-md-4">
                <section class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-body p-4">
                        <h2 class="h6 text-secondary">{{ $label }}</h2>

                        @if ($count === null)
                            <p class="text-secondary mb-0">
                                Section awaiting setup
                            </p>
                        @else
                            <p class="fs-2 fw-bold mb-0">{{ $count }}</p>
                        @endif
                    </div>
                </section>
            </div>
        @endforeach
    </div>

    <section class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4">
            <h2 class="h5 mb-2">Study Visit Completion</h2>

            <p class="text-secondary">
                Completion percentages use all registered patients.
            </p>

            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Time Point</th>
                            <th>Visit Records</th>
                            <th>Completed</th>
                            <th>Overdue</th>
                            <th>Completed / Registered</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($visitProgress as $row)
                            <tr>
                                <td>{{ $row['label'] }}</td>
                                <td>{{ $row['records'] }}</td>
                                <td>{{ $row['completed'] }}</td>
                                <td>{{ $row['overdue'] }}</td>

                                <td style="min-width: 180px">
                                    <div class="progress" role="progressbar" aria-label="{{ $row['label'] }} completion"
                                        aria-valuenow="{{ $row['percentage'] }}" aria-valuemin="0" aria-valuemax="100">
                                        <div class="progress-bar bg-success" style="width: {{ $row['percentage'] }}%"></div>
                                    </div>

                                    <small>{{ $row['percentage'] }}%</small>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </section>
@endsection
@extends('layouts.patients')

@section('title', 'Patient Profile')

@section('content')
    @php
        $timePoints = [
            'baseline' => 'Baseline',
            'month_3' => '3 Months',
            'month_6' => '6 Months',
            'month_12' => '12 Months',
        ];
    @endphp

    <header class="d-flex justify-content-between align-items-center
                   flex-wrap gap-3 mb-4">
        <div>
            <h1 class="h2 fw-bold mb-1">{{ $patient->full_name }}</h1>

            <p class="text-secondary mb-0">
                Code {{ $patient->study_code }}
            </p>
        </div>

        <a href="{{ route('patients.index') }}"
           class="btn btn-outline-secondary">
            Patient List
        </a>
    </header>

    <section class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4">
            <h2 class="h5 mb-4">Patient Profile</h2>

            <dl class="row mb-0">
                <dt class="col-sm-4">Patient ID</dt>
                <dd class="col-sm-8">{{ $patient->patient_id }}</dd>

                <dt class="col-sm-4">Full Name</dt>
                <dd class="col-sm-8">{{ $patient->full_name }}</dd>

                <dt class="col-sm-4">Code</dt>
                <dd class="col-sm-8">{{ $patient->study_code }}</dd>

                <dt class="col-sm-4">Date of Birth</dt>
                <dd class="col-sm-8">
                    {{ $patient->date_of_birth?->format('d M Y') ?? '—' }}
                </dd>

                <dt class="col-sm-4">Sex</dt>
                <dd class="col-sm-8">{{ $patient->sex ?? '—' }}</dd>

                <dt class="col-sm-4">Phone</dt>
                <dd class="col-sm-8">{{ $patient->phone ?? '—' }}</dd>

                <dt class="col-sm-4">Registered On</dt>
                <dd class="col-sm-8">
                    {{ $patient->created_at?->format('d M Y') ?? '—' }}
                </dd>
            </dl>
        </div>
    </section>

    <section class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4">
            <h2 class="h5 mb-3">Study Visits</h2>

            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead>
                        <tr>
                            <th scope="col">Time Point</th>
                            <th scope="col">Due Date</th>
                            <th scope="col">Status</th>
                            <th scope="col">Completed On</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($timePoints as $key => $label)
                            @php
                                $studyVisit = $visits->get($key);
                            @endphp

                            <tr>
                                <td>{{ $label }}</td>

                                <td>
                                    {{ $studyVisit?->due_date?->format('d M Y') ?? '—' }}
                                </td>

                                <td>
                                    @if (! $studyVisit)
                                        <span class="badge text-bg-secondary">
                                            Unscheduled
                                        </span>
                                    @elseif ($studyVisit->visited_at)
                                        <span class="badge text-bg-success">
                                            Completed
                                        </span>
                                    @else
                                        <span class="badge text-bg-primary">
                                            Scheduled
                                        </span>
                                    @endif
                                </td>

                                <td>
                                    {{ $studyVisit?->visited_at?->format('d M Y') ?? '—' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </section>
@endsection
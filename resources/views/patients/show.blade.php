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

        $availableTimePoints = collect($timePoints)
            ->reject(fn ($label, $key) => $visits->has($key));
    @endphp

    <header class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
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

    @if (session('visit_success'))
        <div class="alert alert-success" role="status">
            {{ session('visit_success') }}
        </div>
    @endif


<div class="d-flex flex-wrap gap-2 mb-4">
    <a href="{{ route('patients.medical-history.edit', $patient) }}"
       class="btn btn-outline-primary">
        Medical History
    </a>

    <a href="{{ route('patients.lifestyle.edit', $patient) }}"
       class="btn btn-outline-primary">
        Lifestyle &amp; Diet
    </a>


    <a href="{{ route('patients.eligibility.edit', $patient) }}"
   class="btn btn-outline-primary">
    Eligibility Review
</a>
<a href="{{ route('patients.consents.index', $patient) }}"
   class="btn btn-outline-primary">
    Patient Consent
</a>



</div>
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

                @php
    $currentAge = \App\Services\ClinicalCalculator::age(
        $patient->date_of_birth?->format('Y-m-d'),
        today()->toDateString()
    );
@endphp

<dt class="col-sm-4">Age Today</dt>
<dd class="col-sm-8">
    {{ $currentAge !== null ? $currentAge . ' years' : '—' }}
</dd>

<dt class="col-sm-4">Occupation</dt>
<dd class="col-sm-8">
    {{ $patient->occupation ?? '—' }}
</dd>

<dt class="col-sm-4">Education Level</dt>
<dd class="col-sm-8">
    {{ $patient->education_level ?? '—' }}
</dd>

<dt class="col-sm-4">Email</dt>
<dd class="col-sm-8">
    {{ $patient->email ?? '—' }}
</dd>

<dt class="col-sm-4">Address</dt>
<dd class="col-sm-8" style="white-space: pre-wrap">{{ $patient->address ?? '—' }}</dd>



            </dl>
        </div>
    </section>

    <section class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4">
            <h2 class="h5 mb-3">Schedule Visit</h2>

            @if ($availableTimePoints->isEmpty())
                <p class="text-secondary mb-0">
                    All four study visits have already been scheduled.
                </p>
            @else
                <form method="POST"
                      action="{{ route('patients.visits.store', $patient) }}">
                    @csrf

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="time_point" class="form-label">
                                Time Point
                            </label>

                            <select id="time_point"
                                    name="time_point"
                                    class="form-select @error('time_point') is-invalid @enderror"
                                    required>
                                <option value="">Select a time point</option>

                                @foreach ($availableTimePoints as $key => $label)
                                    <option value="{{ $key }}"
                                            @selected(old('time_point') === $key)>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>

                            @error('time_point')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="due_date" class="form-label">
                                Due Date
                            </label>

                            <input id="due_date"
                                   name="due_date"
                                   type="date"
                                   class="form-control @error('due_date') is-invalid @enderror"
                                   value="{{ old('due_date') }}"
                                   required>

                            @error('due_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary mt-4">
                        Schedule Visit
                    </button>
                </form>
            @endif
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
                            <th scope="col">Action</th>
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

                                <td>
                                    @if ($studyVisit)
                                        <a href="{{ route('patients.visits.vitals.edit', [$patient, $studyVisit]) }}"
                                           class="btn btn-sm btn-outline-primary">
                                            Open Vitals
                                        </a>

                                        <a href="{{ route('patients.visits.elastography.edit', [$patient, $studyVisit]) }}"
   class="btn btn-sm btn-outline-secondary">
    Imaging
</a>

                                        <a href="{{ route('patients.visits.labs.edit', [$patient, $studyVisit]) }}"
   class="btn btn-sm btn-outline-success">
    Lab Results
</a>

<a href="{{ route('patients.visits.follow-up.index', [$patient, $studyVisit]) }}"
   class="btn btn-sm btn-outline-secondary">
    Follow-up Notes
</a>

<a href="{{ route('patients.visits.alerts.index', [$patient, $studyVisit]) }}"
   class="btn btn-sm btn-outline-danger">
    Clinical Alerts
</a>

<a href="{{ route('patients.visits.treatments.index', [$patient, $studyVisit]) }}"
   class="btn btn-sm btn-outline-primary">
    Treatment
</a>

<a href="{{ route('patients.visits.counselling.edit', [$patient, $studyVisit]) }}"
   class="btn btn-sm btn-outline-success">
    Counselling
</a>


<a href="{{ route('patients.visits.adverse-events.index', [$patient, $studyVisit]) }}"
   class="btn btn-sm btn-outline-danger">
    Adverse Events
</a>
                                    @else
                                        <span class="text-secondary">
                                            Schedule first
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </section>
@endsection
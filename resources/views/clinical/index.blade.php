@extends('layouts.app')

@section('title', 'Clinical Dashboard')

@section('content')
    <div class="page-heading">
        <div>
            <p class="eyebrow">Clinical workspace</p>
            <h1>Clinical Dashboard</h1>
            <p class="page-description">
                Patient overview, attendance and clinical review priorities.
            </p>
        </div>

        @if (\Illuminate\Support\Facades\Route::has('patients.create'))
            <a href="{{ route('patients.create') }}" class="btn btn-primary">
                <i class="bi bi-person-plus me-2" aria-hidden="true"></i>
                Add patient
            </a>
        @endif
    </div>

    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
            <x-stat-card label="Patients"
                         :value="$stats['patients']"
                         icon="people"
                         tone="primary"
                         hint="Registered sample records"/>
        </div>

        <div class="col-sm-6 col-xl-3">
            <x-stat-card label="Fatty Liver Patients"
                         :value="$stats['fatty']"
                         icon="heart-pulse"
                         tone="teal"
                         hint="Recorded imaging findings"/>
        </div>

        <div class="col-sm-6 col-xl-3">
            <x-stat-card label="High Risk"
                         :value="$stats['high']"
                         icon="exclamation-triangle"
                         tone="danger"
                         hint="Recorded review category"/>
        </div>

        <div class="col-sm-6 col-xl-3">
            <x-stat-card label="Appointments Today"
                         :value="$stats['today']"
                         icon="calendar2-check"
                         tone="amber"
                         hint="Scheduled for today"/>
        </div>
    </div>

    <section id="patients" class="ehr-card">
        <div class="card-heading">
            <div>
                <h2>Patient directory</h2>
                <p>{{ $patients->count() }} matching records</p>
            </div>

            <form method="GET"
                  action="{{ route('interface.clinical') }}"
                  class="directory-filter">
                <input type="hidden" name="q" value="{{ $q }}">

                @if ($appointments)
                    <input type="hidden"
                           name="appointments"
                           value="{{ $appointments }}">
                @endif

                <label for="risk-filter" class="visually-hidden">
                    Filter by risk
                </label>

                <select id="risk-filter"
                        name="risk"
                        class="form-select">
                    <option value="">All risk categories</option>
                    <option value="normal" @selected($risk === 'normal')>
                        Normal
                    </option>
                    <option value="moderate" @selected($risk === 'moderate')>
                        Moderate risk
                    </option>
                    <option value="high" @selected($risk === 'high')>
                        High risk
                    </option>
                </select>

                <button type="submit" class="btn btn-outline-secondary">
                    Apply
                </button>

                <a href="{{ route('interface.clinical') }}"
                   class="btn btn-link">
                    Reset
                </a>
            </form>
        </div>

        <div class="table-responsive">
            <table class="table ehr-table align-middle">
                <caption class="visually-hidden">
                    Patients with demographics, ultrasound grade and recorded risk.
                </caption>

                <thead>
                    <tr>
                        <th scope="col">Patient ID</th>
                        <th scope="col">Patient</th>
                        <th scope="col">Age / Gender</th>
                        <th scope="col">Ultrasound</th>
                        <th scope="col">Risk</th>
                        <th scope="col">Last attendance</th>
                        <th scope="col" class="text-end">Actions</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($patients as $patient)
                        <tr>
                            <td class="record-code">{{ $patient['id'] }}</td>

                            <td>
                                <a class="patient-name"
                                   href="{{ route('interface.patient', $patient['id']) }}">
                                    {{ $patient['name'] }}
                                </a>

                                <small class="d-block text-secondary">
                                    {{ $patient['dataset'] }}
                                </small>
                            </td>

                            <td>
                                {{ $patient['age'] }} years
                                <small class="d-block text-secondary">
                                    {{ $patient['gender'] }}
                                </small>
                            </td>

                            <td>
                                <span class="grade-badge">
                                    Grade {{ $patient['grade'] }}
                                </span>
                            </td>

                            <td>
                                <span class="risk-badge risk-{{ $patient['risk'] }}">
                                    <span class="status-dot" aria-hidden="true"></span>
                                    {{ ucfirst($patient['risk']) }}
                                </span>
                            </td>

                            <td>
                                {{ \Carbon\Carbon::parse($patient['last_visit'])->format('d M Y') }}
                            </td>

                            <td class="text-end">
                                <div class="dropdown">
                                    <button type="button"
                                            class="btn btn-sm btn-outline-primary dropdown-toggle"
                                            data-bs-toggle="dropdown"
                                            aria-expanded="false">
                                        Open
                                    </button>

                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li>
                                            <a class="dropdown-item"
                                               href="{{ route('interface.patient', $patient['id']) }}">
                                                Patient record
                                            </a>
                                        </li>

                                        <li>
                                            <a class="dropdown-item"
                                               href="{{ route('interface.patient', $patient['id']) }}#laboratory">
                                                Laboratory results
                                            </a>
                                        </li>

                                        <li>
                                            <a class="dropdown-item"
                                               href="{{ route('interface.patient', $patient['id']) }}#attendance">
                                                Attendance history
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="empty-state">
                                <i class="bi bi-search" aria-hidden="true"></i>
                                <p>No patients match this search.</p>
                                <a href="{{ route('interface.clinical') }}">
                                    Clear filters
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="table-footer">
            <span>Showing {{ $patients->count() }} sample records</span>
            <span>Risk categories shown are recorded demonstration values.</span>
        </div>
    </section>
@endsection
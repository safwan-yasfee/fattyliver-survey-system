@extends('layouts.app')

@section('title', 'Research Dashboard')

@section('content')
    <div class="page-heading">
        <div>
            <p class="eyebrow">Research workspace</p>
            <h1>Research Dashboard</h1>

            <p class="page-description">
                Define a cohort and review coded clinical observations.
            </p>
        </div>

        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('interface.research.csv', $filters) }}"
               class="btn btn-research">
                <i class="bi bi-download me-2" aria-hidden="true"></i>
                Export CSV
            </a>

            <a href="{{ route('interface.research.pdf', $filters) }}"
               class="btn btn-outline-secondary"
               target="_blank"
               rel="noopener">
                <i class="bi bi-file-earmark-pdf me-2" aria-hidden="true"></i>
                PDF report
            </a>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
            <x-stat-card label="Datasets"
                         :value="$stats['datasets']"
                         icon="database"
                         tone="teal"
                         hint="Represented in this cohort"/>
        </div>

        <div class="col-sm-6 col-xl-3">
            <x-stat-card label="Records"
                         :value="$stats['records']"
                         icon="collection"
                         tone="teal"
                         hint="Matching coded patients"/>
        </div>

        <div class="col-sm-6 col-xl-3">
            <x-stat-card label="Live Research"
                         :value="$stats['projects']"
                         icon="journal-medical"
                         tone="primary"
                         hint="Represented sample projects"/>
        </div>

        <div class="col-sm-6 col-xl-3">
            <x-stat-card label="Last Updated"
                         :value="$stats['updated']"
                         icon="clock-history"
                         tone="teal"
                         hint="Demonstration snapshot"/>
        </div>
    </div>

    {{-- Cohort filters are applied server-side and reused by exports. --}}
    <section class="ehr-card cohort-panel mb-4">
        <div class="card-heading">
            <div>
                <h2>Cohort definition</h2>
                <p>Charts, table and exports use the applied filters.</p>
            </div>

            <i class="bi bi-sliders section-icon" aria-hidden="true"></i>
        </div>

        <form method="GET" action="{{ route('interface.research') }}">
            <div class="cohort-controls">
                <fieldset>
                    <legend>Age</legend>

                    <div class="range-inputs">
                        <input type="number"
                               name="age_min"
                               class="form-control"
                               min="0"
                               max="120"
                               value="{{ $filters['age_min'] }}"
                               placeholder="Min"
                               aria-label="Minimum age">

                        <span>–</span>

                        <input type="number"
                               name="age_max"
                               class="form-control"
                               min="0"
                               max="120"
                               value="{{ $filters['age_max'] }}"
                               placeholder="Max"
                               aria-label="Maximum age">
                    </div>
                </fieldset>

                <div>
                    <label for="gender" class="form-label">Gender</label>

                    <select id="gender"
                            name="gender"
                            class="form-select">
                        <option value="">All genders</option>

                        @foreach (['Female', 'Male', 'Other'] as $gender)
                            <option value="{{ $gender }}"
                                    @selected($filters['gender'] === $gender)>
                                {{ $gender }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <fieldset>
                    <legend>BMI · kg/m²</legend>

                    <div class="range-inputs">
                        <input type="number"
                               name="bmi_min"
                               class="form-control"
                               min="0"
                               max="300"
                               step="0.1"
                               value="{{ $filters['bmi_min'] }}"
                               placeholder="Min"
                               aria-label="Minimum BMI">

                        <span>–</span>

                        <input type="number"
                               name="bmi_max"
                               class="form-control"
                               min="0"
                               max="300"
                               step="0.1"
                               value="{{ $filters['bmi_max'] }}"
                               placeholder="Max"
                               aria-label="Maximum BMI">
                    </div>
                </fieldset>

                <div>
                    <label for="grade" class="form-label">
                        Ultrasound grade
                    </label>

                    <select id="grade"
                            name="grade"
                            class="form-select">
                        <option value="">All grades</option>

                        @foreach (range(0, 3) as $grade)
                            <option value="{{ $grade }}"
                                    @selected((string) $filters['grade'] === (string) $grade)>
                                Grade {{ $grade }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="cohort-buttons">
                    <button type="submit" class="btn btn-research">
                        Apply
                    </button>

                    <a href="{{ route('interface.research') }}"
                       class="btn btn-outline-secondary">
                        Reset
                    </a>
                </div>
            </div>
        </form>
    </section>

    <div class="row g-4 mb-4">
        <div class="col-lg-4">
            <section class="ehr-card h-100">
                <div class="card-heading">
                    <div>
                        <h2>Ultrasound grade</h2>
                        <p>Distribution within the cohort</p>
                    </div>
                </div>

                <div class="chart-container chart-compact">
                    <canvas id="gradeChart"
                            role="img"
                            aria-label="Cohort counts by ultrasound grade"></canvas>
                </div>
            </section>
        </div>

        <div class="col-lg-4">
            <section class="ehr-card h-100">
                <div class="card-heading">
                    <div>
                        <h2>Gender analysis</h2>
                        <p>Coded cohort demographics</p>
                    </div>
                </div>

                <div class="chart-container chart-compact">
                    <canvas id="genderChart"
                            role="img"
                            aria-label="Cohort counts by gender"></canvas>
                </div>
            </section>
        </div>

        <div class="col-lg-4">
            <section class="ehr-card h-100">
                <div class="card-heading">
                    <div>
                        <h2>Age analysis</h2>
                        <p>Patient counts by age group</p>
                    </div>
                </div>

                <div class="chart-container chart-compact">
                    <canvas id="ageChart"
                            role="img"
                            aria-label="Cohort counts by age group"></canvas>
                </div>
            </section>
        </div>

        <div class="col-12">
            <section class="ehr-card">
                <div class="card-heading">
                    <div>
                        <h2>Mean ALT over time</h2>
                        <p>Applied cohort · four study time points</p>
                    </div>

                    <span class="subtle-label">U/L</span>
                </div>

                <div class="chart-container chart-wide">
                    <canvas id="altChart"
                            role="img"
                            aria-label="Mean cohort ALT at baseline, three, six and twelve months"></canvas>
                </div>
            </section>
        </div>
    </div>

    <section class="ehr-card">
        <div class="card-heading">
            <div>
                <h2>Cohort records</h2>
                <p>{{ $records->count() }} matching coded records</p>
            </div>

            <span class="subtle-label">
                <i class="bi bi-shield-lock me-1" aria-hidden="true"></i>
                Names excluded
            </span>
        </div>

        <div class="table-responsive">
            <table class="table ehr-table align-middle">
                <thead>
                    <tr>
                        <th scope="col">Patient ID</th>
                        <th scope="col">Age</th>
                        <th scope="col">Gender</th>
                        <th scope="col">BMI</th>
                        <th scope="col">Grade</th>
                        <th scope="col">ALT baseline</th>
                        <th scope="col">ALT 12 months</th>
                        <th scope="col">Dataset</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($records as $record)
                        <tr>
                            <td class="record-code">{{ $record['id'] }}</td>
                            <td>{{ $record['age'] }}</td>
                            <td>{{ $record['gender'] }}</td>
                            <td>{{ number_format($record['bmi'], 1) }}</td>

                            <td>
                                <span class="grade-badge">
                                    Grade {{ $record['grade'] }}
                                </span>
                            </td>

                            <td>{{ $record['alt'][0] }} U/L</td>
                            <td>{{ $record['alt'][3] }} U/L</td>
                            <td>{{ $record['dataset'] }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="empty-state">
                                <i class="bi bi-funnel" aria-hidden="true"></i>
                                <p>No records match this cohort.</p>

                                <a href="{{ route('interface.research') }}">
                                    Clear filters
                                </a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="table-footer">
            <span>Exports include every matching sample record.</span>
            <span>Patient codes remain sensitive research identifiers.</span>
        </div>
    </section>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.8/dist/chart.umd.min.js"></script>

    <script>
        (() => {
            const data = {{ \Illuminate\Support\Js::from($chartData) }};
            const blue = '#698BAA';
            const teal = '#75A9A5';
            const slate = '#A4B5C4';
            const pale = '#D5DFE7';

            Chart.defaults.font.family = 'Inter, sans-serif';
            Chart.defaults.color = '#607387';

            const cartesianOptions = () => ({
                responsive: true,
                maintainAspectRatio: false,
                animation: false,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    x: {
                        grid: { display: false }
                    },
                    y: {
                        beginAtZero: true,
                        ticks: { precision: 0 },
                        grid: { color: '#EDF1F5' }
                    }
                }
            });

            new Chart(document.getElementById('gradeChart'), {
                type: 'bar',
                data: {
                    labels: ['Grade 0', 'Grade 1', 'Grade 2', 'Grade 3'],
                    datasets: [{
                        data: data.grades,
                        backgroundColor: [pale, slate, teal, blue],
                        borderRadius: 4,
                        maxBarThickness: 48
                    }]
                },
                options: cartesianOptions()
            });

            new Chart(document.getElementById('genderChart'), {
                type: 'doughnut',
                data: {
                    labels: ['Female', 'Male', 'Other'],
                    datasets: [{
                        data: data.genders,
                        backgroundColor: [teal, blue, pale],
                        borderWidth: 3,
                        borderColor: '#FFFFFF'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: false,
                    cutout: '72%',
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                usePointStyle: true,
                                padding: 18
                            }
                        }
                    }
                }
            });

            new Chart(document.getElementById('ageChart'), {
                type: 'bar',
                data: {
                    labels: ['Under 30', '30–44', '45–59', '60+'],
                    datasets: [{
                        data: data.ages,
                        backgroundColor: teal,
                        borderRadius: 4,
                        maxBarThickness: 48
                    }]
                },
                options: cartesianOptions()
            });

            const altOptions = cartesianOptions();
            altOptions.scales.y.ticks = {};
            altOptions.scales.y.title = {
                display: true,
                text: 'Mean ALT · U/L'
            };

            new Chart(document.getElementById('altChart'), {
                type: 'line',
                data: {
                    labels: ['Baseline', '3 months', '6 months', '12 months'],
                    datasets: [{
                        label: 'Mean ALT',
                        data: data.alt,
                        borderColor: teal,
                        backgroundColor: 'rgba(117, 169, 165, 0.10)',
                        borderWidth: 2,
                        pointRadius: 4,
                        pointBackgroundColor: teal,
                        tension: 0.25,
                        fill: true
                    }]
                },
                options: altOptions
            });
        })();
    </script>
@endpush
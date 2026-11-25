@extends('layouts.app')

@section('title', $patient['name'])

@section('content')
    <div class="page-heading">
        <div>
            <p class="eyebrow">Patient record · {{ $patient['id'] }}</p>
            <h1>{{ $patient['name'] }}</h1>

            <p class="page-description">
                {{ $patient['age'] }} years
                · {{ $patient['gender'] }}
                · Last attendance:
                {{ \Carbon\Carbon::parse($patient['last_visit'])->format('d M Y') }}
            </p>
        </div>

        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('interface.clinical') }}"
               class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2" aria-hidden="true"></i>
                Patient directory
            </a>

            <button type="button"
                    class="btn btn-primary no-print"
                    onclick="window.print()">
                <i class="bi bi-printer me-2" aria-hidden="true"></i>
                Print record
            </button>
        </div>
    </div>

    <section class="ehr-card patient-summary mb-4">
        <div class="summary-item">
            <span class="summary-label">BMI</span>
            <strong>{{ $patient['bmi'] }} <small>kg/m²</small></strong>
        </div>

        <div class="summary-item">
            <span class="summary-label">Ultrasound</span>
            <strong>Grade {{ $patient['grade'] }}</strong>
        </div>

        <div class="summary-item">
            <span class="summary-label">Latest ALT</span>
            <strong>{{ $patient['alt'][3] }} <small>U/L</small></strong>
        </div>

        <div class="summary-item">
            <span class="summary-label">Recorded risk</span>

            <span class="risk-badge risk-{{ $patient['risk'] }}">
                <span class="status-dot" aria-hidden="true"></span>
                {{ ucfirst($patient['risk']) }}
            </span>
        </div>
    </section>

    <div class="clinical-alert-strip mb-4">
        <i class="bi bi-clipboard2-pulse" aria-hidden="true"></i>

        <div>
            <strong>Clinical review summary</strong>
            <span>
                {{ $labs->whereNotNull('direction')->count() }}
                results outside the displayed sample reference ranges.
            </span>
        </div>

        <span class="risk-badge risk-{{ $patient['risk'] }}">
            {{ ucfirst($patient['risk']) }} recorded risk
        </span>
    </div>

    <div class="row g-4">
        <div class="col-xl-7">
            <section id="laboratory" class="ehr-card h-100">
                <div class="card-heading">
                    <div>
                        <h2>Laboratory results</h2>
                        <p>Latest attendance · 17 tests</p>
                    </div>

                    <i class="bi bi-clipboard2-data section-icon"
                       aria-hidden="true"></i>
                </div>

                <div class="table-responsive">
                    <table class="table ehr-table laboratory-table align-middle">
                        <thead>
                            <tr>
                                <th scope="col">Test</th>
                                <th scope="col">Result</th>
                                <th scope="col">Reference range</th>
                                <th scope="col">Status</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($labs as $lab)
                                <tr>
                                    <td>{{ $lab['name'] }}</td>

                                    <td class="{{ $lab['direction'] ? 'abnormal-result' : 'normal-result' }}">
                                        @if ($lab['direction'])
                                            <i class="bi bi-arrow-{{ $lab['direction'] }}"
                                               aria-hidden="true"></i>
                                        @endif

                                        {{ $lab['result'] }}
                                        <small>{{ $lab['unit'] }}</small>
                                    </td>

                                    <td class="text-secondary">
                                        {{ $lab['range'] }}
                                    </td>

                                    <td>
                                        <span class="result-badge {{ $lab['direction'] ? 'result-abnormal' : 'result-normal' }}">
                                            {{ $lab['status'] }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="table-footer">
                    Illustrative reference ranges.
                    Use the reporting laboratory’s ranges for live records.
                </div>
            </section>
        </div>

        <div class="col-xl-5">
            <section class="ehr-card mb-4">
                <div class="card-heading">
                    <div>
                        <h2>ALT &amp; AST trend</h2>
                        <p>Four study time points · U/L</p>
                    </div>
                </div>

                <div class="chart-container">
                    <canvas id="enzymeChart"
                            role="img"
                            aria-label="ALT and AST values over four visits"></canvas>
                </div>

                <div class="visually-hidden">
                    ALT: {{ implode(', ', $patient['alt']) }}.
                    AST: {{ implode(', ', $patient['ast']) }}.
                </div>
            </section>

            <section class="ehr-card fib4-card mb-4">
                <div class="d-flex justify-content-between align-items-start gap-3">
                    <div>
                        <p class="eyebrow mb-2">Non-invasive score</p>
                        <h2>FIB-4</h2>
                        <p class="fib4-value">{{ number_format($fib4, 2) }}</p>
                    </div>

                    <span class="stat-icon">
                        <i class="bi bi-activity" aria-hidden="true"></i>
                    </span>
                </div>

                <p class="small text-secondary">
                    Calculated from sample age, AST, ALT and platelet count.
                </p>

                <h3 class="small fw-semibold mb-2">Recorded risk scale</h3>

                <div class="risk-scale">
                    @foreach (['normal', 'moderate', 'high'] as $risk)
                        <div class="risk-scale-segment scale-{{ $risk }} {{ $patient['risk'] === $risk ? 'selected' : '' }}">
                            @if ($patient['risk'] === $risk)
                                <i class="bi bi-caret-down" aria-hidden="true"></i>
                                <span class="visually-hidden">Selected:</span>
                            @endif

                            {{ ucfirst($risk) }}
                        </div>
                    @endforeach
                </div>

                <p class="small text-secondary mt-3 mb-0">
                    The recorded risk category is supplied separately from the score.
                </p>
            </section>

            <section id="attendance" class="ehr-card">
                <div class="card-heading">
                    <div>
                        <h2>Attendance chronology</h2>
                        <p>Recorded study visits</p>
                    </div>

                    <i class="bi bi-clock-history section-icon"
                       aria-hidden="true"></i>
                </div>

                <ol class="attendance-list">
                    @foreach ($visits as $visit)
                        <li>
                            <span class="attendance-marker" aria-hidden="true"></span>

                            <div class="d-flex justify-content-between flex-wrap gap-1">
                                <strong>{{ $visit['label'] }}</strong>
                                <time>{{ $visit['date'] }}</time>
                            </div>

                            <p>{{ $visit['note'] }}</p>
                        </li>
                    @endforeach
                </ol>
            </section>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.8/dist/chart.umd.min.js"></script>

    <script>
        (() => {
            const data = {{ \Illuminate\Support\Js::from($chartData) }};

            Chart.defaults.font.family = 'Inter, sans-serif';
            Chart.defaults.color = '#607387';

            new Chart(document.getElementById('enzymeChart'), {
                type: 'line',
                data: {
                    labels: data.labels,
                    datasets: [
                        {
                            label: 'ALT',
                            data: data.alt,
                            borderColor: '#698BAA',
                            backgroundColor: '#698BAA',
                            borderWidth: 2,
                            pointRadius: 4,
                            pointHoverRadius: 6,
                            tension: 0.25,
                            fill: false
                        },
                        {
                            label: 'AST',
                            data: data.ast,
                            borderColor: '#75A9A5',
                            backgroundColor: '#75A9A5',
                            borderWidth: 2,
                            pointRadius: 4,
                            pointHoverRadius: 6,
                            tension: 0.25,
                            fill: false
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                usePointStyle: true,
                                padding: 20
                            }
                        }
                    },
                    scales: {
                        x: {
                            grid: { display: false }
                        },
                        y: {
                            beginAtZero: true,
                            title: { display: true, text: 'U/L' },
                            grid: { color: '#EDF1F5' }
                        }
                    }
                }
            });
        })();
    </script>
@endpush
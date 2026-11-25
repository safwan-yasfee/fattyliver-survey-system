<section class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-4">
        <h2 class="h5 mb-3">Calculated Results</h2>

        <p class="text-secondary small">
            Calculated from saved information. Save your changes to update these results.
        </p>

        <div class="row g-3">
            <div class="col-md-4">
                <div class="border rounded-3 p-3 h-100">
                    <h3 class="h6 text-secondary">Age</h3>

                    <p class="fs-4 fw-bold mb-1">
                        {{ $calculations['age'] !== null
                            ? $calculations['age'] . ' years'
                            : 'Not calculated' }}
                    </p>

                    @if ($calculations['reference_date'])
                        <p class="small text-secondary mb-0">
                            {{ $calculations['date_source'] }}:
                            {{ $calculations['reference_date'] }}
                        </p>
                    @endif
                </div>
            </div>

            <div class="col-md-4">
                <div class="border rounded-3 p-3 h-100">
                    <h3 class="h6 text-secondary">BMI</h3>

                    <p class="fs-4 fw-bold mb-0">
                        {{ $calculations['bmi'] !== null
                            ? number_format($calculations['bmi'], 2) . ' kg/m²'
                            : 'Not calculated' }}
                    </p>
                </div>
            </div>

            <div class="col-md-4">
                <div class="border rounded-3 p-3 h-100">
                    <h3 class="h6 text-secondary">FIB-4</h3>

                    <p class="fs-4 fw-bold mb-1">
                        {{ $calculations['fib4'] !== null
                            ? number_format($calculations['fib4'], 2)
                            : 'Not calculated' }}
                    </p>

                    @if ($calculations['fib4_date'])
                        <p class="small text-secondary mb-1">
                            Lab assessment:
                            {{ $calculations['fib4_date'] }}
                        </p>
                    @endif

                    <p class="small text-secondary mb-0">
                        {{ $calculations['fib4_message'] }}
                    </p>
                </div>
            </div>
        </div>

        @if ($calculations['changes'])
            <h3 class="h6 mt-4 mb-3">Changes From Baseline</h3>

            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead>
                        <tr>
                            <th scope="col">Measurement</th>
                            <th scope="col">Baseline</th>
                            <th scope="col">Current</th>
                            <th scope="col">Difference</th>
                            <th scope="col">Change (%)</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($calculations['changes'] as $change)
                            <tr>
                                <td>
                                    {{ $change['label'] }}
                                    ({{ $change['unit'] }})
                                </td>

                                <td>
                                    {{ $change['baseline'] !== null
                                        ? number_format((float) $change['baseline'], 2)
                                        : '—' }}
                                </td>

                                <td>
                                    {{ $change['current'] !== null
                                        ? number_format((float) $change['current'], 2)
                                        : '—' }}
                                </td>

                                <td>
                                    @if ($change['difference'] !== null)
                                        {{ $change['difference'] > 0 ? '+' : '' }}{{ number_format($change['difference'], 2) }}
                                    @else
                                        —
                                    @endif
                                </td>

                                <td>
                                    @if ($change['percentage_change'] !== null)
                                        {{ $change['percentage_change'] > 0 ? '+' : '' }}{{ number_format($change['percentage_change'], 2) }}%
                                    @else
                                        —
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <p class="small text-secondary mt-2 mb-0">
                Negative values mean a decrease from baseline.
                Missing measurements are not treated as zero.
            </p>
        @endif
    </div>
</section>
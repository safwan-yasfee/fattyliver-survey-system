<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ResearchPatientController extends Controller
{
    public function index(Request $request): View
    {
        Gate::authorize('view-patient-records');

        $filters = $this->validatedFilters($request);

        $patients = $this->filteredPatients($filters)
            ->orderByDesc('patient_id')
            ->paginate(15)
            ->withQueryString();

        return view('research.patients', [
            'patients' => $patients,
            'search' => $filters['search'],
            'eligibility' => $filters['eligibility'],
            'consent' => $filters['consent'],
        ]);
    }

    public function export(Request $request): StreamedResponse
    {
        Gate::authorize('view-patient-records');

        $filters = $this->validatedFilters($request);

        return response()->streamDownload(
            function () use ($filters): void {
                $output = fopen('php://output', 'w');

                if ($output === false) {
                    throw new \RuntimeException(
                        'Unable to open the CSV output stream.'
                    );
                }

                try {
                    fputcsv($output, [
                        'study_code',
                        'eligibility_status',
                        'consent_status',
                        'consent_version',
                        'completed_visits',
                    ], ',', '"', '');

                    // Export every matching patient, across all pages.
                    $patients = $this->filteredPatients($filters)
                        ->lazyById(500, 'patient_id');

                    foreach ($patients as $patient) {
                        $review = $patient->latestEligibilityReview;
                        $latestConsent = $patient->latestConsent;

                        $eligibilityStatus = $review
                            ? ($review->status ?? 'unknown')
                            : 'unreviewed';

                        if (! $latestConsent || ! $latestConsent->signed_at) {
                            $consentStatus = 'not_recorded';
                        } elseif ($latestConsent->withdrawn_at) {
                            $consentStatus = 'withdrawn';
                        } else {
                            $consentStatus = 'signed';
                        }

                        $versionCode = (string) (
                            $latestConsent?->consentVersion?->version_code
                            ?? ''
                        );

                        fputcsv($output, [
                            $this->csvSafe((string) $patient->study_code),
                            $this->csvSafe($eligibilityStatus),
                            $this->csvSafe($consentStatus),
                            $this->csvSafe($versionCode),
                            (int) $patient->completed_visits_count,
                        ], ',', '"', '');
                    }
                } finally {
                    fclose($output);
                }
            },
            'research-patients.csv',
            [
                'Content-Type' => 'text/csv; charset=UTF-8',
                'Cache-Control' => 'no-store',
            ]
        );
    }

    private function validatedFilters(Request $request): array
    {
        $data = $request->validate([
            'search' => [
                'nullable',
                'string',
                'max:30',
            ],
            'eligibility' => [
                'nullable',
                Rule::in([
                    'all',
                    'eligible',
                    'pending',
                    'not_eligible',
                    'unreviewed',
                ]),
            ],
            'consent' => [
                'nullable',
                Rule::in([
                    'all',
                    'signed',
                    'withdrawn',
                    'not_recorded',
                ]),
            ],
        ]);

        return [
            'search' => trim($data['search'] ?? ''),
            'eligibility' => $data['eligibility'] ?? 'all',
            'consent' => $data['consent'] ?? 'all',
        ];
    }

    private function filteredPatients(array $filters): Builder
    {
        $query = Patient::query()
            ->with([
                'latestEligibilityReview',
                'latestConsent.consentVersion',
            ])
            ->withCount([
                'visits as completed_visits_count' => function (
                    Builder $query
                ): void {
                    $query->whereIn('time_point', [
                        'baseline',
                        'month_3',
                        'month_6',
                        'month_12',
                    ])->whereNotNull('visited_at');
                },
            ]);

        // Patient's SoftDeletes trait excludes archived records.
        if ($filters['search'] !== '') {
            $query->where(
                'study_code',
                'like',
                '%' . $filters['search'] . '%'
            );
        }

        if ($filters['eligibility'] === 'unreviewed') {
            $query->whereDoesntHave('eligibilityReviews');
        } elseif ($filters['eligibility'] !== 'all') {
            $query->whereHas(
                'latestEligibilityReview',
                function (Builder $query) use ($filters): void {
                    $query->where(
                        'status',
                        $filters['eligibility']
                    );
                }
            );
        }

        if ($filters['consent'] === 'signed') {
            $query->whereHas(
                'latestConsent',
                function (Builder $query): void {
                    $query->whereNotNull('signed_at')
                        ->whereNull('withdrawn_at');
                }
            );
        } elseif ($filters['consent'] === 'withdrawn') {
            $query->whereHas(
                'latestConsent',
                function (Builder $query): void {
                    $query->whereNotNull('signed_at')
                        ->whereNotNull('withdrawn_at');
                }
            );
        } elseif ($filters['consent'] === 'not_recorded') {
            $query->whereDoesntHave(
                'latestConsent',
                function (Builder $query): void {
                    $query->whereNotNull('signed_at');
                }
            );
        }

        return $query;
    }

    private function csvSafe(string $value): string
    {
        // Check for spreadsheet formulas, including leading
        // whitespace, control characters or UTF-8 BOMs.
        $candidate = str_replace("\xEF\xBB\xBF", '', $value);

        if (preg_match('/^[\x00-\x20\x7F]*[=+\-@]/', $candidate) === 1) {
            return "'" . $value;
        }

        return $value;
    }
}
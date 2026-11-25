<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class InterfaceController extends Controller
{
    public function clinical(Request $request): View
    {
        $filters = $request->validate([
            'q' => ['nullable', 'string', 'max:100'],
            'risk' => [
                'nullable',
                Rule::in(['normal', 'moderate', 'high']),
            ],
            'appointments' => [
                'nullable',
                Rule::in(['today']),
            ],
        ]);

        $all = $this->samplePatients();
        $q = trim($filters['q'] ?? '');
        $risk = $filters['risk'] ?? '';
        $appointments = $filters['appointments'] ?? '';

        $patients = $all->filter(function (array $patient) use (
            $q,
            $risk,
            $appointments
        ): bool {
            $searchMatches = $q === ''
                || str_contains(
                    strtolower($patient['name'] . ' ' . $patient['id']),
                    strtolower($q)
                );

            $riskMatches = $risk === ''
                || $patient['risk'] === $risk;

            $appointmentMatches = $appointments !== 'today'
                || $patient['appointment_date'] === today()->toDateString();

            return $searchMatches && $riskMatches && $appointmentMatches;
        })->values();

        $stats = [
            'patients' => $all->count(),
            'fatty' => $all->where('fatty_liver', true)->count(),
            'high' => $all->where('risk', 'high')->count(),
            'today' => $all->where(
                'appointment_date',
                today()->toDateString()
            )->count(),
        ];

        return view('clinical.index', compact(
            'patients',
            'stats',
            'q',
            'risk',
            'appointments'
        ));
    }

    public function patient(string $patient): View
    {
        $patient = $this->samplePatients()->firstWhere('id', $patient);

        abort_unless($patient, 404);

        $labs = $this->sampleLabs($patient);

        $fib4 = round(
            ($patient['age'] * $patient['ast'][3])
                / ($patient['platelets'] * sqrt($patient['alt'][3])),
            2
        );

        $chartData = [
            'labels' => ['Baseline', '3 months', '6 months', '12 months'],
            'alt' => $patient['alt'],
            'ast' => $patient['ast'],
        ];

        $visits = collect([
            ['label' => 'Baseline assessment', 'days' => 365],
            ['label' => '3-month review', 'days' => 275],
            ['label' => '6-month review', 'days' => 180],
            ['label' => '12-month review', 'days' => 0],
        ])->map(function (array $visit) use ($patient): array {
            return [
                'label' => $visit['label'],
                'date' => \Carbon\Carbon::parse($patient['last_visit'])
                    ->subDays($visit['days'])
                    ->format('d M Y'),
                'note' => 'Vitals, laboratory results and treatment reviewed.',
            ];
        });

        return view('clinical.show', compact(
            'patient',
            'labs',
            'fib4',
            'chartData',
            'visits'
        ));
    }

    public function research(Request $request): View
    {
        ['records' => $records, 'filters' => $filters] =
            $this->cohort($request);

        $chartData = [
            'grades' => collect(range(0, 3))
                ->map(fn ($grade) => $records->where('grade', $grade)->count())
                ->all(),

            'genders' => collect(['Female', 'Male', 'Other'])
                ->map(fn ($gender) => $records->where('gender', $gender)->count())
                ->all(),

            'ages' => [
                $records->where('age', '<', 30)->count(),
                $records->whereBetween('age', [30, 44])->count(),
                $records->whereBetween('age', [45, 59])->count(),
                $records->where('age', '>=', 60)->count(),
            ],

            'alt' => collect(range(0, 3))
                ->map(function ($index) use ($records) {
                    if ($records->isEmpty()) {
                        return null;
                    }

                    return round(
                        $records->avg(
                            fn (array $record) => $record['alt'][$index]
                        ),
                        1
                    );
                })
                ->all(),
        ];

        $stats = [
            'datasets' => $records->pluck('dataset')->unique()->count(),
            'records' => $records->count(),
            'projects' => $records->pluck('project')->unique()->count(),
            'updated' => now()->format('d M'),
        ];

        return view('research.index', compact(
            'records',
            'filters',
            'chartData',
            'stats'
        ));
    }

    public function csv(Request $request): StreamedResponse
    {
        ['records' => $records] = $this->cohort($request);

        return response()->streamDownload(function () use ($records): void {
            $output = fopen('php://output', 'w');

            if ($output === false) {
                throw new \RuntimeException('Unable to create CSV output.');
            }

            try {
                fputcsv($output, [
                    'patient_id',
                    'age',
                    'gender',
                    'bmi',
                    'ultrasound_grade',
                    'alt_baseline',
                    'alt_month_3',
                    'alt_month_6',
                    'alt_month_12',
                ], ',', '"', '');

                foreach ($records as $record) {
                    fputcsv($output, [
                        $record['id'],
                        $record['age'],
                        $record['gender'],
                        $record['bmi'],
                        $record['grade'],
                        ...$record['alt'],
                    ], ',', '"', '');
                }
            } finally {
                fclose($output);
            }
        }, 'research-cohort.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Cache-Control' => 'no-store',
        ]);
    }

    public function pdf(Request $request): View
    {
        ['records' => $records, 'filters' => $filters] =
            $this->cohort($request);

        return view('research.print', compact('records', 'filters'));
    }

    /**
     * Research data is explicitly whitelisted.
     * Names never enter the research view or export payload.
     */
    private function cohort(Request $request): array
    {
        $validated = $request->validate([
            'age_min' => ['nullable', 'integer', 'between:0,120'],
            'age_max' => ['nullable', 'integer', 'between:0,120'],
            'gender' => [
                'nullable',
                Rule::in(['Female', 'Male', 'Other']),
            ],
            'bmi_min' => ['nullable', 'numeric', 'between:0,300'],
            'bmi_max' => ['nullable', 'numeric', 'between:0,300'],
            'grade' => [
                'nullable',
                Rule::in(['0', '1', '2', '3']),
            ],
        ]);

        $filters = array_merge([
            'age_min' => null,
            'age_max' => null,
            'gender' => null,
            'bmi_min' => null,
            'bmi_max' => null,
            'grade' => null,
        ], $validated);

        foreach (['age', 'bmi'] as $field) {
            if (
                $filters[$field . '_min'] !== null
                && $filters[$field . '_max'] !== null
                && $filters[$field . '_min'] > $filters[$field . '_max']
            ) {
                throw ValidationException::withMessages([
                    $field . '_max' =>
                        'Maximum must be greater than or equal to minimum.',
                ]);
            }
        }

        $records = $this->samplePatients()
            ->map(fn (array $patient) => [
                'id' => $patient['id'],
                'age' => $patient['age'],
                'gender' => $patient['gender'],
                'bmi' => $patient['bmi'],
                'grade' => $patient['grade'],
                'alt' => $patient['alt'],
                'dataset' => $patient['dataset'],
                'project' => $patient['project'],
            ])
            ->filter(function (array $record) use ($filters): bool {
                foreach (['age', 'bmi'] as $field) {
                    if (
                        $filters[$field . '_min'] !== null
                        && $record[$field] < $filters[$field . '_min']
                    ) {
                        return false;
                    }

                    if (
                        $filters[$field . '_max'] !== null
                        && $record[$field] > $filters[$field . '_max']
                    ) {
                        return false;
                    }
                }

                if (
                    $filters['gender'] !== null
                    && $record['gender'] !== $filters['gender']
                ) {
                    return false;
                }

                return $filters['grade'] === null
                    || $record['grade'] === (int) $filters['grade'];
            })
            ->values();

        return compact('records', 'filters');
    }

    /**
     * Fictional records for interface demonstration.
     * Risk labels are supplied demo values, not calculated diagnoses.
     */
    private function samplePatients(): Collection
    {
        $rows = [
            [
                'id' => 'P-0231', 'name' => 'Amina Rahman',
                'age' => 55, 'gender' => 'Female',
                'weight' => 76, 'height' => 165,
                'grade' => 2, 'risk' => 'moderate',
                'alt' => [88, 80, 72, 62],
                'ast' => [66, 59, 51, 48],
                'platelets' => 220, 'last_days' => 12, 'appointment_offset' => 0,
                'dataset' => 'Metabolic follow-up', 'project' => 'Liver registry',
            ],
            [
                'id' => 'P-0232', 'name' => 'Kamrul Hasan',
                'age' => 63, 'gender' => 'Male',
                'weight' => 88, 'height' => 171,
                'grade' => 3, 'risk' => 'high',
                'alt' => [120, 108, 96, 82],
                'ast' => [98, 90, 82, 73],
                'platelets' => 150, 'last_days' => 4, 'appointment_offset' => 2,
                'dataset' => 'Advanced disease', 'project' => 'Liver registry',
            ],
            [
                'id' => 'P-0233', 'name' => 'Nusrat Karim',
                'age' => 38, 'gender' => 'Female',
                'weight' => 62, 'height' => 161,
                'grade' => 1, 'risk' => 'normal',
                'alt' => [55, 48, 42, 38],
                'ast' => [40, 36, 32, 30],
                'platelets' => 250, 'last_days' => 18, 'appointment_offset' => 0,
                'dataset' => 'Lifestyle cohort', 'project' => 'Lifestyle study',
            ],
            [
                'id' => 'P-0234', 'name' => 'Farhan Ahmed',
                'age' => 46, 'gender' => 'Male',
                'weight' => 90, 'height' => 174,
                'grade' => 2, 'risk' => 'moderate',
                'alt' => [94, 82, 75, 68],
                'ast' => [72, 66, 60, 55],
                'platelets' => 235, 'last_days' => 8, 'appointment_offset' => 3,
                'dataset' => 'Metabolic follow-up', 'project' => 'Liver registry',
            ],
            [
                'id' => 'P-0235', 'name' => 'Laila Begum',
                'age' => 59, 'gender' => 'Female',
                'weight' => 74, 'height' => 157,
                'grade' => 2, 'risk' => 'high',
                'alt' => [90, 83, 72, 64],
                'ast' => [82, 74, 67, 60],
                'platelets' => 145, 'last_days' => 2, 'appointment_offset' => 1,
                'dataset' => 'Advanced disease', 'project' => 'Liver registry',
            ],
            [
                'id' => 'P-0236', 'name' => 'Rashed Hossain',
                'age' => 32, 'gender' => 'Male',
                'weight' => 68, 'height' => 175,
                'grade' => 0, 'risk' => 'normal',
                'alt' => [40, 35, 30, 28],
                'ast' => [33, 30, 27, 24],
                'platelets' => 280, 'last_days' => 25, 'appointment_offset' => 0,
                'dataset' => 'Lifestyle cohort', 'project' => 'Lifestyle study',
            ],
            [
                'id' => 'P-0237', 'name' => 'Sadia Islam',
                'age' => 44, 'gender' => 'Female',
                'weight' => 72, 'height' => 163,
                'grade' => 1, 'risk' => 'moderate',
                'alt' => [65, 58, 51, 46],
                'ast' => [52, 47, 43, 39],
                'platelets' => 190, 'last_days' => 9, 'appointment_offset' => 4,
                'dataset' => 'Lifestyle cohort', 'project' => 'Lifestyle study',
            ],
            [
                'id' => 'P-0238', 'name' => 'Imran Chowdhury',
                'age' => 67, 'gender' => 'Male',
                'weight' => 80, 'height' => 168,
                'grade' => 3, 'risk' => 'high',
                'alt' => [132, 118, 106, 95],
                'ast' => [110, 100, 90, 80],
                'platelets' => 135, 'last_days' => 6, 'appointment_offset' => 5,
                'dataset' => 'Advanced disease', 'project' => 'Liver registry',
            ],
        ];

        return collect($rows)->map(function (array $patient): array {
            $patient['bmi'] = round(
                $patient['weight'] / (($patient['height'] / 100) ** 2),
                1
            );

            $patient['fatty_liver'] = $patient['grade'] > 0;

            $patient['last_visit'] = today()
                ->subDays($patient['last_days'])
                ->toDateString();

            $patient['appointment_date'] = today()
                ->addDays($patient['appointment_offset'])
                ->toDateString();

            return $patient;
        });
    }

    private function sampleLabs(array $patient): Collection
    {
        // Illustrative report ranges only; replace with actual lab ranges
        // when connecting the interface to production laboratory results.
        $rows = [
            ['ALT', $patient['alt'][3], 'U/L', 7, 56],
            ['AST', $patient['ast'][3], 'U/L', 10, 40],
            ['ALP', 110, 'U/L', 44, 147],
            ['Total bilirubin', 0.8, 'mg/dL', 0.1, 1.2],
            ['Albumin', 4.1, 'g/dL', 3.5, 5.0],
            ['Platelet count', $patient['platelets'], '×10⁹/L', 150, 450],
            ['Fasting glucose', 108, 'mg/dL', 70, 99],
            ['HbA1c', 6.2, '%', 4.0, 5.6],
            ['Total cholesterol', 212, 'mg/dL', 0, 199],
            ['LDL cholesterol', 118, 'mg/dL', 0, 99],
            ['HDL cholesterol', 48, 'mg/dL', 40, 100],
            ['Triglycerides', 185, 'mg/dL', 0, 149],
            ['Serum creatinine', 0.9, 'mg/dL', 0.6, 1.3],
            ['TSH', 2.1, 'µIU/mL', 0.4, 4.0],
            ['Free T4', 1.2, 'ng/dL', 0.8, 1.8],
        ];

        $labs = collect($rows)->map(function (array $row): array {
            [$name, $result, $unit, $min, $max] = $row;

            $direction = $result > $max
                ? 'up'
                : ($result < $min ? 'down' : null);

            return [
                'name' => $name,
                'result' => $result,
                'unit' => $unit,
                'range' => "{$min}–{$max}",
                'direction' => $direction,
                'status' => $direction === 'up'
                    ? 'High'
                    : ($direction === 'down' ? 'Low' : 'Normal'),
            ];
        });

        foreach (['HBsAg', 'Anti-HCV'] as $name) {
            $labs->push([
                'name' => $name,
                'result' => 'Negative',
                'unit' => '',
                'range' => 'Negative',
                'direction' => null,
                'status' => 'Normal',
            ]);
        }

        return $labs;
    }
}
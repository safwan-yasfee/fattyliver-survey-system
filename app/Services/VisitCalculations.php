<?php

namespace App\Services;

use App\Models\Patient;
use App\Models\Visit;

class VisitCalculations
{
    public static function forVisit(
        Patient $patient,
        Visit $visit
    ): array {
        $visit->loadMissing('vitals', 'labResults.labTest');

        $vitals = $visit->vitals;

        // An incomplete visit currently has no actual assessment-date field.
        // Use the last vitals save date, and clearly label that date.
        $referenceDate = $visit->visited_at?->format('Y-m-d')
            ?? $vitals?->updated_at?->format('Y-m-d');

        $dateSource = $visit->visited_at
            ? 'Visit completion date'
            : 'Last vitals save date';

        $dateOfBirth = $patient->date_of_birth?->format('Y-m-d');

        $age = ClinicalCalculator::age(
            $dateOfBirth,
            $referenceDate
        );

        $bmi = ClinicalCalculator::bmi(
            $vitals?->weight_kg,
            $vitals?->height_cm
        );

        $baseline = $patient->visits()
            ->where('time_point', 'baseline')
            ->with('vitals')
            ->first();

        $baselineVitals = $baseline?->vitals;

        $baselineBmi = ClinicalCalculator::bmi(
            $baselineVitals?->weight_kg,
            $baselineVitals?->height_cm
        );

        $changes = [];

        if ($visit->time_point !== 'baseline') {
            $measurements = [
                'weight_kg' => ['Weight', 'kg'],
                'waist_cm' => ['Waist', 'cm'],
                'systolic_bp' => ['Systolic BP', 'mmHg'],
                'diastolic_bp' => ['Diastolic BP', 'mmHg'],
                'heart_rate' => ['Heart rate', 'bpm'],
            ];

            foreach ($measurements as $field => [$label, $unit]) {
                $changes[] = [
                    'label' => $label,
                    'unit' => $unit,
                    'baseline' => $baselineVitals?->{$field},
                    'current' => $vitals?->{$field},
                    ...ClinicalCalculator::changeFromBaseline(
                        $baselineVitals?->{$field},
                        $vitals?->{$field}
                    ),
                ];
            }

            $changes[] = [
                'label' => 'BMI',
                'unit' => 'kg/m²',
                'baseline' => $baselineBmi,
                'current' => $bmi,
                ...ClinicalCalculator::changeFromBaseline(
                    $baselineBmi,
                    $bmi
                ),
            ];
        }

        $fib4 = self::fib4($patient, $visit);

        return [
            'age' => $age,
            'reference_date' => $referenceDate,
            'date_source' => $dateSource,
            'bmi' => $bmi,
            'changes' => $changes,
            'fib4' => $fib4['value'],
            'fib4_date' => $fib4['date'],
            'fib4_message' => $fib4['message'],
        ];
    }

    private static function fib4(
        Patient $patient,
        Visit $visit
    ): array {
        $unavailable = function (string $message): array {
            return [
                'value' => null,
                'date' => null,
                'message' => $message,
            ];
        };

        $results = $visit->labResults->keyBy(
            fn ($result) => strtoupper(
                trim((string) $result->labTest?->test_code)
            )
        );

        $ast = $results->get('AST');
        $alt = $results->get('ALT');
        $platelets = $results->get('PLATELET_COUNT');

        if (! $ast || ! $alt || ! $platelets) {
            return $unavailable(
                'Save AST, ALT and platelet count for this visit.'
            );
        }

        if (
            ! self::enzymeUnit($ast->unit) ||
            ! self::enzymeUnit($alt->unit) ||
            ! self::plateletUnit($platelets->unit)
        ) {
            return $unavailable(
                'Check units: AST and ALT must be U/L; platelets must be ×10³/µL or ×10⁹/L.'
            );
        }

        $dates = [
            $ast->tested_at?->format('Y-m-d'),
            $alt->tested_at?->format('Y-m-d'),
            $platelets->tested_at?->format('Y-m-d'),
        ];

        // Do not silently combine results from different assessment dates.
        if (
            in_array(null, $dates, true) ||
            count(array_unique($dates)) !== 1
        ) {
            return $unavailable(
                'FIB-4 requires these three results to have the same test date.'
            );
        }

        $age = ClinicalCalculator::age(
            $patient->date_of_birth?->format('Y-m-d'),
            $dates[0]
        );

        $value = ClinicalCalculator::fib4(
            $age,
            $ast->result_value,
            $alt->result_value,
            $platelets->result_value
        );

        if ($value === null) {
            return $unavailable(
                'A valid adult age and positive numeric AST, ALT and platelet values are required.'
            );
        }

        return [
            'value' => $value,
            'date' => $dates[0],
            'message' => 'Calculated score; clinical interpretation is recorded separately.',
        ];
    }

    private static function enzymeUnit(?string $unit): bool
    {
        return in_array(
            strtolower(trim((string) $unit)),
            ['u/l', 'iu/l'],
            true
        );
    }

    private static function plateletUnit(?string $unit): bool
    {
        $unit = strtolower(
            str_replace(
                [' ', '×', '³', '⁹', 'µ', 'μ'],
                ['', 'x', '^3', '^9', 'u', 'u'],
                (string) $unit
            )
        );

        return in_array($unit, [
            'x10^3/ul',
            '10^3/ul',
            'x10^9/l',
            '10^9/l',
        ], true);
    }
}
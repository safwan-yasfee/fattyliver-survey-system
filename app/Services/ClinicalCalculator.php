<?php

namespace App\Services;

use DateTimeImmutable;

class ClinicalCalculator
{
    /**
     * Completed years on a specific assessment date.
     * Both dates must use Y-m-d.
     */
    public static function age(
        ?string $dateOfBirth,
        ?string $assessmentDate
    ): ?int {
        $birth = self::date($dateOfBirth);
        $assessment = self::date($assessmentDate);

        if (! $birth || ! $assessment || $birth > $assessment) {
            return null;
        }

        return $birth->diff($assessment)->y;
    }

    /**
     * Weight in kilograms; height in centimetres.
     */
    public static function bmi(
        mixed $weightKg,
        mixed $heightCm
    ): ?float {
        $weight = self::number($weightKg);
        $height = self::number($heightCm);

        if ($weight === null || $height === null) {
            return null;
        }

        if ($weight <= 0 || $height <= 0) {
            return null;
        }

        $heightMetres = $height / 100;

        return round($weight / ($heightMetres ** 2), 2);
    }

    /**
     * Supports the existing expected/taken tablet fields.
     */
    public static function adherence(
        mixed $expected,
        mixed $taken
    ): ?float {
        $expected = self::number($expected);
        $taken = self::number($taken);

        if ($expected === null || $taken === null) {
            return null;
        }

        if (
            $expected <= 0 ||
            $taken < 0 ||
            $taken > $expected ||
            floor($expected) !== $expected ||
            floor($taken) !== $taken
        ) {
            return null;
        }

        return round(($taken / $expected) * 100, 2);
    }

    /**
     * Pill-count calculation for a defined assessment period.
     * Use when all prescribed tablets for that period were supplied.
     */
    public static function adherenceFromReturned(
        mixed $prescribed,
        mixed $returned
    ): ?float {
        $prescribed = self::number($prescribed);
        $returned = self::number($returned);

        if ($prescribed === null || $returned === null) {
            return null;
        }

        if (
            $prescribed <= 0 ||
            $returned < 0 ||
            $returned > $prescribed ||
            floor($prescribed) !== $prescribed ||
            floor($returned) !== $returned
        ) {
            return null;
        }

        return self::adherence(
            $prescribed,
            $prescribed - $returned
        );
    }

    /**
     * Categories from the clinic book.
     * Compare the unrounded percentage at category boundaries.
     */
    public static function adherenceCategory(
        mixed $expected,
        mixed $taken
    ): ?string {
        if (self::adherence($expected, $taken) === null) {
            return null;
        }

        $percentage = ((float) $taken / (float) $expected) * 100;

        return match (true) {
            $percentage >= 90 => '≥90%',
            $percentage >= 75 => '75–<90%',
            default => '<75%',
        };
    }

    /**
     * Adult FIB-4 arithmetic only; no automatic risk classification.
     *
     * Age: completed years at assessment.
     * AST and ALT: U/L.
     * Platelets: ×10^9/L, numerically equivalent to ×10^3/µL.
     */
    public static function fib4(
        ?int $ageYears,
        mixed $astUl,
        mixed $altUl,
        mixed $platelets
    ): ?float {
        $ast = self::number($astUl);
        $alt = self::number($altUl);
        $platelets = self::number($platelets);

        if (
            $ageYears === null ||
            $ageYears < 18 ||
            $ast === null ||
            $alt === null ||
            $platelets === null
        ) {
            return null;
        }

        if ($ast <= 0 || $alt <= 0 || $platelets <= 0) {
            return null;
        }

        return round(
            ($ageYears * $ast) / ($platelets * sqrt($alt)),
            2
        );
    }

    /**
     * Compare the same measurement in the same units.
     *
     * Negative difference means a decrease.
     * Percentage change is unavailable when baseline is zero.
     */
    public static function changeFromBaseline(
        mixed $baseline,
        mixed $current
    ): array {
        $baseline = self::number($baseline);
        $current = self::number($current);

        if ($baseline === null || $current === null) {
            return [
                'difference' => null,
                'percentage_change' => null,
            ];
        }

        return [
            'difference' => round($current - $baseline, 2),
            'percentage_change' => $baseline == 0.0
                ? null
                : round(
                    (($current - $baseline) / abs($baseline)) * 100,
                    2
                ),
        ];
    }

    private static function number(mixed $value): ?float
    {
        if (! is_numeric($value)) {
            return null;
        }

        $number = (float) $value;

        return is_finite($number) ? $number : null;
    }

    private static function date(?string $value): ?DateTimeImmutable
    {
        if (! $value) {
            return null;
        }

        $date = DateTimeImmutable::createFromFormat('!Y-m-d', $value);

        if (! $date || $date->format('Y-m-d') !== $value) {
            return null;
        }

        return $date;
    }
}
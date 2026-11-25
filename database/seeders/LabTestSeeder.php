<?php

namespace Database\Seeders;

use App\Models\LabTest;
use Illuminate\Database\Seeder;

class LabTestSeeder extends Seeder
{
    public function run(): void
    {
        $tests = [
            [
                'test_code' => 'ALT',
                'name' => 'Alanine Aminotransferase',
            ],
            [
                'test_code' => 'AST',
                'name' => 'Aspartate Aminotransferase',
            ],
            [
                'test_code' => 'ALP',
                'name' => 'Alkaline Phosphatase',
            ],
            [
                'test_code' => 'TOTAL_BILIRUBIN',
                'name' => 'Total Bilirubin',
            ],
            [
                'test_code' => 'ALBUMIN',
                'name' => 'Albumin',
            ],
            [
                'test_code' => 'PLATELET_COUNT',
                'name' => 'Platelet Count',
            ],
            [
                'test_code' => 'FPG',
                'name' => 'Fasting Plasma Glucose',
            ],
            [
                'test_code' => 'HBA1C',
                'name' => 'HbA1c',
            ],
            [
                'test_code' => 'TOTAL_CHOLESTEROL',
                'name' => 'Total Cholesterol',
            ],
            [
                'test_code' => 'LDL',
                'name' => 'LDL Cholesterol',
            ],
            [
                'test_code' => 'HDL',
                'name' => 'HDL Cholesterol',
            ],
            [
                'test_code' => 'TRIGLYCERIDES',
                'name' => 'Triglycerides',
            ],
            [
                'test_code' => 'CREATININE',
                'name' => 'Serum Creatinine',
            ],
            [
                'test_code' => 'TSH',
                'name' => 'Thyroid-Stimulating Hormone',
            ],
            [
                'test_code' => 'FREE_T4',
                'name' => 'Free T4',
            ],
            [
                'test_code' => 'HBSAG',
                'name' => 'Hepatitis B Surface Antigen',
            ],
            [
                'test_code' => 'ANTI_HCV',
                'name' => 'Hepatitis C Antibody',
            ],
        ];

        foreach ($tests as $test) {
            LabTest::firstOrCreate(
                ['test_code' => $test['test_code']],
                [
                    'name' => $test['name'],
                    'is_active' => true,
                ]
            );
        }
    }
}
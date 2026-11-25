<?php

namespace App\Http\Controllers;

use App\Models\LabTest;
use App\Models\Patient;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use App\Services\VisitCalculations;
class LabResultController extends Controller
{
    public function edit(
        Request $request,
        Patient $patient,
        int $visit
    ): View {
        $studyVisit = $patient->visits()->findOrFail($visit);

        $tests = LabTest::where('is_active', true)
            ->orderBy('name')
            ->get();

        $results = $studyVisit->labResults()
            ->with('labTest')
            ->orderBy('lab_test_id')
            ->get();

        $selectedResult = $results->firstWhere(
            'lab_test_id',
            (int) $request->query('test')
        );

        $calculations = VisitCalculations::forVisit(
            $patient,
            $studyVisit
        );

        return view('patients.lab-results', compact(
            'patient',
            'studyVisit',
            'tests',
            'results',
            'selectedResult',
            'calculations'
        ));
    }

    public function update(
        Request $request,
        Patient $patient,
        int $visit
    ): RedirectResponse {
        $studyVisit = $patient->visits()->findOrFail($visit);

        $data = $request->validate([
            'lab_test_id' => [
                'required',
                'integer',
                Rule::exists('lab_tests', 'id')
                    ->where('is_active', true),
            ],
            'result_value' => [
                'nullable',
                'required_without:result_text',
                'numeric',
                'decimal:0,4',
                'between:-9999999999,9999999999',
            ],
            'result_text' => [
                'nullable',
                'required_without:result_value',
                'string',
                'max:10000',
            ],
            'unit' => ['nullable', 'string', 'max:50'],
            'tested_at' => [
                'required',
                'date_format:Y-m-d',
                'before_or_equal:today',
            ],
            'notes' => ['nullable', 'string', 'max:10000'],
        ]);

        $testId = $data['lab_test_id'];
        unset($data['lab_test_id']);

        $studyVisit->labResults()->updateOrCreate(
            ['lab_test_id' => $testId],
            $data
        );

        return redirect()
            ->route('patients.visits.labs.edit', [
                'patient' => $patient,
                'visit' => $studyVisit->getKey(),
                'test' => $testId,
            ])
            ->with('success', 'Lab result saved successfully.');
    }
}
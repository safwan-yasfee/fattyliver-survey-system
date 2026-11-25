<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ElastographyResultController extends Controller
{
    public function edit(Patient $patient, int $visit): View
    {
        $studyVisit = $patient->visits()
            ->with('elastographyResult')
            ->findOrFail($visit);

        return view('patients.elastography', [
            'patient' => $patient,
            'studyVisit' => $studyVisit,
            'result' => $studyVisit->elastographyResult,
        ]);
    }

    public function update(
        Request $request,
        Patient $patient,
        int $visit
    ): RedirectResponse {
        $studyVisit = $patient->visits()->findOrFail($visit);

        $data = $request->validate([
            'assessment_date' => [
                'required',
                'date_format:Y-m-d',
                'before_or_equal:today',
            ],
            'lsm_kpa' => [
                'nullable',
                'numeric',
                'decimal:0,2',
                'min:0',
                'max:999999.99',
            ],
            'cap_db_m' => [
                'nullable',
                'numeric',
                'decimal:0,2',
                'min:0',
                'max:999999.99',
            ],
            'fibrosis_stage' => ['nullable', 'string', 'max:30'],
            'steatosis_grade' => ['nullable', 'string', 'max:30'],
            'notes' => ['nullable', 'string', 'max:10000'],
        ]);

        $studyVisit->elastographyResult()->updateOrCreate([], $data);

        return redirect()
            ->route('patients.visits.elastography.edit', [
                $patient,
                $studyVisit,
            ])
            ->with('success', 'Elastography results saved successfully.');
    }
}
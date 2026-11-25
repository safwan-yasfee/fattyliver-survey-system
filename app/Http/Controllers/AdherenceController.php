<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Services\ClinicalCalculator;

class AdherenceController extends Controller
{
    public function edit(
        Patient $patient,
        int $visit,
        int $treatment
    ): View {
        $studyVisit = $patient->visits()->findOrFail($visit);

        $medication = $studyVisit->treatments()
            ->with('adherence')
            ->findOrFail($treatment);

        return view('patients.adherence', [
            'patient' => $patient,
            'studyVisit' => $studyVisit,
            'medication' => $medication,
            'assessment' => $medication->adherence,
        ]);
    }

    public function update(
        Request $request,
        Patient $patient,
        int $visit,
        int $treatment
    ): RedirectResponse {
        $studyVisit = $patient->visits()->findOrFail($visit);
        $medication = $studyVisit->treatments()->findOrFail($treatment);

        $data = $request->validate([
            'assessment_date' => [
                'required',
                'date_format:Y-m-d',
                'before_or_equal:today',
            ],
            'tablets_expected' => [
                'nullable',
                'required_with:tablets_taken',
                'integer',
                'min:1',
                'max:4294967295',
            ],
            'tablets_taken' => [
                'nullable',
                'required_with:tablets_expected',
                'integer',
                'min:0',
                'lte:tablets_expected',
            ],
            'notes' => ['nullable', 'string', 'max:10000'],
        ]);

        $expected = $data['tablets_expected'] ?? null;
        $taken = $data['tablets_taken'] ?? null;

        $assessment = $medication->adherence()->firstOrNew([]);

        $assessment->fill($data);

        $assessment->adherence_percentage =
            ClinicalCalculator::adherence($expected, $taken);

        $assessment->save();

        return redirect()
            ->route('patients.visits.treatments.adherence.edit', [
                $patient,
                $studyVisit,
                $medication,
            ])
            ->with('success', 'Adherence assessment saved successfully.');
    }
}
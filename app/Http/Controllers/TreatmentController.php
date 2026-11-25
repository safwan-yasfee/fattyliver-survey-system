<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TreatmentController extends Controller
{
    public function index(
        Request $request,
        Patient $patient,
        int $visit
    ): View {
        $studyVisit = $patient->visits()->findOrFail($visit);

        $treatments = $studyVisit->treatments()
            ->with('recordedBy')
            ->orderByDesc('id')
            ->get();

        $treatment = $request->has('treatment')
            ? $studyVisit->treatments()
                ->findOrFail($request->integer('treatment'))
            : null;

        return view('patients.treatments', compact(
            'patient',
            'studyVisit',
            'treatments',
            'treatment'
        ));
    }

    public function store(
        Request $request,
        Patient $patient,
        int $visit
    ): RedirectResponse {
        $studyVisit = $patient->visits()->findOrFail($visit);

        $treatment = $studyVisit->treatments()
            ->make($this->validateTreatment($request));

        $treatment->recorded_by = $request->user()->id;
        $treatment->save();

        return redirect()
            ->route('patients.visits.treatments.index', [
                $patient,
                $studyVisit,
            ])
            ->with('success', 'Treatment added successfully.');
    }

    public function update(
        Request $request,
        Patient $patient,
        int $visit,
        int $treatment
    ): RedirectResponse {
        $studyVisit = $patient->visits()->findOrFail($visit);

        $record = $studyVisit->treatments()->findOrFail($treatment);

        $record->update($this->validateTreatment($request));

        return redirect()
            ->route('patients.visits.treatments.index', [
                $patient,
                $studyVisit,
            ])
            ->with('success', 'Treatment updated successfully.');
    }

    private function validateTreatment(Request $request): array
    {
        return $request->validate([
            'medication_name' => ['required', 'string', 'max:255'],
            'dose' => ['nullable', 'string', 'max:100'],
            'frequency' => ['nullable', 'string', 'max:100'],
            'route' => ['nullable', 'string', 'max:50'],
            'start_date' => ['required', 'date_format:Y-m-d'],
            'end_date' => [
                'nullable',
                'date_format:Y-m-d',
                'after_or_equal:start_date',
            ],
            'status' => [
                'required',
                'in:active,paused,completed,stopped',
            ],
            'notes' => ['nullable', 'string', 'max:10000'],
        ]);
    }
}
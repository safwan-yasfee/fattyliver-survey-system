<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DietPlanController extends Controller
{
    public function edit(Patient $patient, int $visit): View
    {
        $studyVisit = $patient->visits()
            ->with('dietPlan.recordedBy')
            ->findOrFail($visit);

        return view('patients.counselling', [
            'patient' => $patient,
            'studyVisit' => $studyVisit,
            'plan' => $studyVisit->dietPlan,
        ]);
    }

    public function update(
        Request $request,
        Patient $patient,
        int $visit
    ): RedirectResponse {
        $studyVisit = $patient->visits()->findOrFail($visit);

        $data = $request->validate([
            'counselling_date' => [
                'required',
                'date_format:Y-m-d',
                'before_or_equal:today',
            ],
            'diet_advice' => ['nullable', 'string', 'max:10000'],
            'exercise_advice' => ['nullable', 'string', 'max:10000'],
            'goals' => ['nullable', 'string', 'max:10000'],
            'notes' => ['nullable', 'string', 'max:10000'],
        ]);

        $plan = $studyVisit->dietPlan()->firstOrNew([]);

        $plan->fill($data);

        if (! $plan->exists) {
            $plan->recorded_by = $request->user()->id;
        }

        $plan->save();

        return redirect()
            ->route('patients.visits.counselling.edit', [
                $patient,
                $studyVisit,
            ])
            ->with(
                'success',
                'Diet and exercise counselling saved successfully.'
            );
    }
}
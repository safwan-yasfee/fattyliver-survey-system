<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class PatientController extends Controller
{
    public function index(): View
    {
        $patients = Patient::orderByDesc('patient_id')->paginate(15);

        return view('patients.index', compact('patients'));
    }

    public function create(): View
    {
        return view('patients.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'study_code' => [
                'required',
                'string',
                'max:30',
                'unique:patients,study_code',
            ],
            'full_name' => ['required', 'string', 'max:255'],
            'date_of_birth' => ['nullable', 'date', 'before_or_equal:today'],
            'sex' => ['nullable', 'in:Male,Female,Other'],
            'phone' => ['nullable', 'string', 'max:30'],
        ], [], [
            'study_code' => 'code',
            'full_name' => 'full name',
        ]);

        Patient::create($data);

        return redirect()
            ->route('patients.index')
            ->with('success', 'Patient registered successfully.');
    }

    public function show(Patient $patient): View
    {
        $visits = $patient->visits()
            ->get()
            ->keyBy('time_point');

        return view('patients.show', compact('patient', 'visits'));
    }

    public function edit(Patient $patient): View
    {
        return view('patients.edit', compact('patient'));
    }

    public function update(
        Request $request,
        Patient $patient
    ): RedirectResponse {
        $data = $request->validate([
            'study_code' => [
                'required',
                'string',
                'max:30',
                Rule::unique('patients', 'study_code')
                    ->ignore($patient->patient_id, 'patient_id'),
            ],
            'full_name' => ['required', 'string', 'max:255'],
            'date_of_birth' => ['nullable', 'date', 'before_or_equal:today'],
            'sex' => ['nullable', 'in:Male,Female,Other'],
            'phone' => ['nullable', 'string', 'max:30'],
        ], [], [
            'study_code' => 'code',
            'full_name' => 'full name',
        ]);

        $patient->update($data);

        return redirect()
            ->route('patients.index')
            ->with('success', 'Patient updated successfully.');
    }

    public function storeVisit(
        Request $request,
        Patient $patient
    ): RedirectResponse {
        $data = $request->validate([
            'time_point' => [
                'required',
                'in:baseline,month_3,month_6,month_12',
            ],
            'due_date' => ['required', 'date_format:Y-m-d'],
        ]);

        $visit = $patient->visits()->firstOrCreate(
            ['time_point' => $data['time_point']],
            ['due_date' => $data['due_date']]
        );

        if (! $visit->wasRecentlyCreated) {
            return back()
                ->withErrors([
                    'time_point' => 'This patient already has a visit for that time point.',
                ])
                ->withInput();
        }

        return redirect()
            ->route('patients.show', $patient)
            ->with('visit_success', 'Visit scheduled successfully.');
    }

    public function editVitals(
        Patient $patient,
        int $visit
    ): View {
        $studyVisit = $patient->visits()
            ->with('vitals')
            ->findOrFail($visit);

        return view('patients.vitals', [
            'patient' => $patient,
            'studyVisit' => $studyVisit,
            'vitals' => $studyVisit->vitals,
        ]);
    }

    public function updateVitals(
        Request $request,
        Patient $patient,
        int $visit
    ): RedirectResponse {
        $studyVisit = $patient->visits()->findOrFail($visit);

        $data = $request->validate([
            'weight_kg' => [
                'nullable',
                'numeric',
                'decimal:0,2',
                'gt:0',
                'max:999.99',
            ],
            'height_cm' => [
                'nullable',
                'numeric',
                'decimal:0,2',
                'gt:0',
                'max:999.99',
            ],
            'waist_cm' => [
                'nullable',
                'numeric',
                'decimal:0,2',
                'gt:0',
                'max:999.99',
            ],
            'systolic_bp' => [
                'nullable',
                'integer',
                'min:1',
                'max:65535',
            ],
            'diastolic_bp' => [
                'nullable',
                'integer',
                'min:1',
                'max:65535',
            ],
            'heart_rate' => [
                'nullable',
                'integer',
                'min:1',
                'max:65535',
            ],
        ]);

        $studyVisit->vitals()->updateOrCreate([], $data);

        return redirect()
            ->route('patients.visits.vitals.edit', [
                $patient,
                $studyVisit,
            ])
            ->with('success', 'Vitals saved successfully.');
    }

    public function completeVisit(
        Patient $patient,
        int $visit
    ): RedirectResponse {
        $studyVisit = $patient->visits()->findOrFail($visit);

        $updated = $patient->visits()
            ->whereKey($studyVisit->getKey())
            ->whereNull('visited_at')
            ->update([
                'visited_at' => now(),
            ]);

        return redirect()
            ->route('patients.visits.vitals.edit', [
                $patient,
                $studyVisit,
            ])
            ->with(
                'success',
                $updated
                    ? 'Visit marked as completed.'
                    : 'This visit is already completed.'
            );
    }
}
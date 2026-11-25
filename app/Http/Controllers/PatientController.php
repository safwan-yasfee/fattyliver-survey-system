<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Services\VisitCalculations;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PatientController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:255'],
            'sex' => ['nullable', 'in:Male,Female,Other'],
        ]);

        $search = trim($filters['search'] ?? '');
        $sex = $filters['sex'] ?? '';

        $patients = Patient::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('study_code', 'like', '%' . $search . '%')
                        ->orWhere('full_name', 'like', '%' . $search . '%')
                        ->orWhere('phone', 'like', '%' . $search . '%');
                });
            })
            ->when($sex !== '', function ($query) use ($sex) {
                $query->where('sex', $sex);
            })
            ->orderByDesc('patient_id')
            ->paginate(15)
            ->withQueryString();

        return view('patients.index', compact(
            'patients',
            'search',
            'sex'
        ));
    }

    public function create(): View
    {
        return view('patients.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([

            'occupation' => [
                'nullable',
                'string',
                'max:150',
            ],
            'education_level' => [
                'nullable',
                'string',
                'max:100',
            ],
            'address' => [
                'nullable',
                'string',
                'max:2000',
            ],
            'email' => [
                'nullable',
                'email',
                'max:255',
            ],



            'full_name' => [
                'required',
                'string',
                'max:255',
            ],
            'date_of_birth' => [
                'nullable',
                'date',
                'before_or_equal:today',
            ],
            'sex' => [
                'nullable',
                'in:Male,Female,Other',
            ],
            'phone' => [
                'bail',
                'nullable',
                'string',
                'max:14',
                'regex:/\A(?:01[3-9][0-9]{8}|\+8801[3-9][0-9]{8})\z/',
            ],
        ], [
            'phone.regex' =>
                'Enter a Bangladesh mobile number such as 01712345678 or +8801712345678, without spaces or hyphens.',
            'phone.max' =>
                'The mobile number must contain 11 local digits or use the +880 international format.',
        ], [
            'full_name' => 'full name',
            'phone' => 'mobile number',
        ]);

        // Store local numbers in international format.
        if (
            isset($data['phone']) &&
            str_starts_with($data['phone'], '01')
        ) {
            $data['phone'] = '+88' . $data['phone'];
        }

        // Generate the patient code on the server.
        $data['study_code'] = 'FL-' . (string) Str::ulid();

        Patient::create($data);

        return redirect()
            ->route('patients.index')
            ->with('success', 'Patient registered successfully.');
    }

    public function archived(): View
    {
        Gate::authorize('archive-patients');

        $patients = Patient::onlyTrashed()
            ->orderByDesc('deleted_at')
            ->paginate(15);

        return view('patients.archived', compact('patients'));
    }

    public function restore(int $patient): RedirectResponse
    {
        Gate::authorize('archive-patients');

        $archivedPatient = Patient::onlyTrashed()
            ->findOrFail($patient);

        $archivedPatient->restore();

        return redirect()
            ->route('patients.archived')
            ->with('success', 'Patient restored successfully.');
    }

    public function show(Patient $patient): View
    {
        $visits = $patient->visits()
            ->get()
            ->keyBy('time_point');

        return view('patients.show', compact(
            'patient',
            'visits'
        ));
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
            'full_name' => [
                'required',
                'string',
                'max:255',
            ],
            'date_of_birth' => [
                'nullable',
                'date',
                'before_or_equal:today',
            ],
            'sex' => [
                'nullable',
                'in:Male,Female,Other',
            ],
            'phone' => [
                'bail',
                'nullable',
                'string',
                'max:14',
                'regex:/\A(?:01[3-9][0-9]{8}|\+8801[3-9][0-9]{8})\z/',
            ],
        ], [
            'phone.regex' =>
                'Enter a Bangladesh mobile number such as 01712345678 or +8801712345678, without spaces or hyphens.',
            'phone.max' =>
                'The mobile number must contain 11 local digits or use the +880 international format.',
        ], [
            'full_name' => 'full name',
            'phone' => 'mobile number',
        ]);

        if (
            isset($data['phone']) &&
            str_starts_with($data['phone'], '01')
        ) {
            $data['phone'] = '+88' . $data['phone'];
        }

        // Study code is excluded from editable data.
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
            'due_date' => [
                'required',
                'date_format:Y-m-d',
            ],
        ]);

        $visit = $patient->visits()->firstOrCreate(
            ['time_point' => $data['time_point']],
            ['due_date' => $data['due_date']]
        );

        if (!$visit->wasRecentlyCreated) {
            return back()
                ->withErrors([
                    'time_point' =>
                        'This patient already has a visit for that time point.',
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

        $calculations = VisitCalculations::forVisit(
            $patient,
            $studyVisit
        );

        return view('patients.vitals', [
            'patient' => $patient,
            'studyVisit' => $studyVisit,
            'vitals' => $studyVisit->vitals,
            'calculations' => $calculations,
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

    public function destroy(Patient $patient): RedirectResponse
    {
        Gate::authorize('archive-patients');

        $patient->delete();

        return redirect()
            ->route('patients.index')
            ->with('success', 'Patient archived successfully.');
    }
}
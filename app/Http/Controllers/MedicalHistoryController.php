<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MedicalHistoryController extends Controller
{
    public function edit(Patient $patient): View
    {
        return view('patients.medical-history', [
            'patient' => $patient,
            'history' => $patient->medicalHistory,
        ]);
    }

    public function update(
        Request $request,
        Patient $patient
    ): RedirectResponse {
        $data = $request->validate([
            'diseases' => ['nullable', 'string', 'max:10000'],
            'alcohol_history' => ['nullable', 'string', 'max:10000'],
            'drug_history' => ['nullable', 'string', 'max:10000'],
            'biopsy_history' => ['nullable', 'string', 'max:10000'],
            'notes' => ['nullable', 'string', 'max:10000'],
        ]);

        $patient->medicalHistory()->updateOrCreate([], $data);

        return redirect()
            ->route('patients.medical-history.edit', $patient)
            ->with('success', 'Medical history saved successfully.');
    }
}
<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LifestyleProfileController extends Controller
{
    public function edit(Patient $patient): View
    {
        return view('patients.lifestyle', [
            'patient' => $patient,
            'lifestyle' => $patient->lifestyleProfile,
        ]);
    }

    public function update(
        Request $request,
        Patient $patient
    ): RedirectResponse {
        $data = $request->validate([
            'physical_activity' => ['nullable', 'string', 'max:10000'],
            'dietary_habits' => ['nullable', 'string', 'max:10000'],
            'smoking_history' => ['nullable', 'string', 'max:10000'],
            'notes' => ['nullable', 'string', 'max:10000'],
        ]);

        $patient->lifestyleProfile()->updateOrCreate([], $data);

        return redirect()
            ->route('patients.lifestyle.edit', $patient)
            ->with(
                'success',
                'Lifestyle and diet information saved successfully.'
            );
    }
}
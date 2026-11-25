<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ClinicalAlertController extends Controller
{
    public function index(Patient $patient, int $visit): View
    {
        $studyVisit = $patient->visits()->findOrFail($visit);

        $alerts = $studyVisit->clinicalAlerts()
            ->with(['recordedBy', 'resolvedBy'])
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->get();

        return view('patients.clinical-alerts', compact(
            'patient',
            'studyVisit',
            'alerts'
        ));
    }

    public function store(
        Request $request,
        Patient $patient,
        int $visit
    ): RedirectResponse {
        $studyVisit = $patient->visits()->findOrFail($visit);

        $data = $request->validate([
            'alert_type' => ['required', 'string', 'max:100'],
            'message' => ['required', 'string', 'max:10000'],
        ]);

        $alert = $studyVisit->clinicalAlerts()->make($data);

        $alert->status = 'open';
        $alert->recorded_by = $request->user()->id;
        $alert->save();

        return redirect()
            ->route('patients.visits.alerts.index', [
                $patient,
                $studyVisit,
            ])
            ->with('success', 'Clinical alert created successfully.');
    }

    public function resolve(
        Request $request,
        Patient $patient,
        int $visit,
        int $alert
    ): RedirectResponse {
        $studyVisit = $patient->visits()->findOrFail($visit);

        $record = $studyVisit->clinicalAlerts()->findOrFail($alert);

        if ($record->status !== 'open') {
            return back()->withErrors([
                'alert' => 'Only an open alert can be resolved.',
            ]);
        }

        $data = $request->validate([
            'resolution_notes' => ['required', 'string', 'max:10000'],
        ]);

        $updated = $studyVisit->clinicalAlerts()
            ->whereKey($record->getKey())
            ->where('status', 'open')
            ->update([
                'status' => 'resolved',
                'resolved_at' => now(),
                'resolved_by' => $request->user()->id,
                'resolution_notes' => $data['resolution_notes'],
            ]);

        return redirect()
            ->route('patients.visits.alerts.index', [
                $patient,
                $studyVisit,
            ])
            ->with(
                'success',
                $updated
                    ? 'Clinical alert resolved successfully.'
                    : 'This alert has already been changed. Refresh to see its status.'
            );
    }
}
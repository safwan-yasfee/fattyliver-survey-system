<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdverseEventController extends Controller
{
    public function index(
        Request $request,
        Patient $patient,
        int $visit
    ): View {
        $studyVisit = $patient->visits()->findOrFail($visit);

        $events = $studyVisit->adverseEvents()
            ->with('recordedBy')
            ->orderByDesc('event_date')
            ->orderByDesc('id')
            ->get();

        $event = $request->has('event')
            ? $studyVisit->adverseEvents()
                ->findOrFail($request->integer('event'))
            : null;

        return view('patients.adverse-events', compact(
            'patient',
            'studyVisit',
            'events',
            'event'
        ));
    }

    public function store(
        Request $request,
        Patient $patient,
        int $visit
    ): RedirectResponse {
        $studyVisit = $patient->visits()->findOrFail($visit);

        $event = $studyVisit->adverseEvents()
            ->make($this->validateEvent($request));

        $event->recorded_by = $request->user()->id;
        $event->save();

        return redirect()
            ->route('patients.visits.adverse-events.index', [
                $patient,
                $studyVisit,
            ])
            ->with('success', 'Adverse event recorded successfully.');
    }

    public function update(
        Request $request,
        Patient $patient,
        int $visit,
        int $event
    ): RedirectResponse {
        $studyVisit = $patient->visits()->findOrFail($visit);

        $record = $studyVisit->adverseEvents()->findOrFail($event);

        $record->update($this->validateEvent($request));

        return redirect()
            ->route('patients.visits.adverse-events.index', [
                $patient,
                $studyVisit,
            ])
            ->with('success', 'Adverse event updated successfully.');
    }

    private function validateEvent(Request $request): array
    {
        return $request->validate([
            'event_date' => [
                'required',
                'date_format:Y-m-d',
                'before_or_equal:today',
            ],
            'description' => ['required', 'string', 'max:10000'],
            'severity' => ['nullable', 'string', 'max:30'],
            'is_serious' => ['nullable', 'boolean'],
            'treatment' => ['nullable', 'string', 'max:10000'],
            'outcome' => ['nullable', 'string', 'max:10000'],
            'notes' => ['nullable', 'string', 'max:10000'],
        ]);
    }
}
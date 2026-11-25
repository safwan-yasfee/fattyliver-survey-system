<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FollowUpController extends Controller
{
    public function index(
        Request $request,
        Patient $patient,
        int $visit
    ): View {
        $studyVisit = $patient->visits()->findOrFail($visit);

        $entries = $studyVisit->followUps()
            ->with('recordedBy')
            ->orderByDesc('follow_up_date')
            ->orderByDesc('id')
            ->get();

        $entry = $request->has('entry')
            ? $studyVisit->followUps()
                ->findOrFail($request->integer('entry'))
            : null;

        return view('patients.follow-up', compact(
            'patient',
            'studyVisit',
            'entries',
            'entry'
        ));
    }

    public function store(
        Request $request,
        Patient $patient,
        int $visit
    ): RedirectResponse {
        $studyVisit = $patient->visits()->findOrFail($visit);

        $entry = $studyVisit->followUps()
            ->make($this->validateEntry($request));

        $entry->recorded_by = $request->user()->id;
        $entry->save();

        return redirect()
            ->route('patients.visits.follow-up.index', [
                $patient,
                $studyVisit,
            ])
            ->with('success', 'Follow-up note saved successfully.');
    }

    public function update(
        Request $request,
        Patient $patient,
        int $visit,
        int $entry
    ): RedirectResponse {
        $studyVisit = $patient->visits()->findOrFail($visit);

        $record = $studyVisit->followUps()->findOrFail($entry);
        $record->update($this->validateEntry($request));

        return redirect()
            ->route('patients.visits.follow-up.index', [
                $patient,
                $studyVisit,
            ])
            ->with('success', 'Follow-up note updated successfully.');
    }

    private function validateEntry(Request $request): array
    {
        return $request->validate([
            'follow_up_date' => [
                'required',
                'date_format:Y-m-d',
                'before_or_equal:today',
            ],
            'progress_notes' => ['required', 'string', 'max:10000'],
            'assessment' => ['nullable', 'string', 'max:10000'],
            'plan' => ['nullable', 'string', 'max:10000'],
            'next_follow_up_date' => [
                'nullable',
                'date_format:Y-m-d',
                'after_or_equal:follow_up_date',
            ],
        ]);
    }
}
<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class EligibilityReviewController extends Controller
{
    public function edit(Patient $patient): View
    {
        Gate::authorize('view-patient-records');

        $latestReview = $patient->latestEligibilityReview()
            ->with('reviewer')
            ->first();

        $reviews = $patient->eligibilityReviews()
            ->with('reviewer')
            ->orderByDesc('id')
            ->paginate(10);

        return view('patients.eligibility', compact(
            'patient',
            'latestReview',
            'reviews'
        ));
    }

    public function store(
        Request $request,
        Patient $patient
    ): RedirectResponse {
        Gate::authorize('review-patient-eligibility');

        $data = $request->validate([
            'status' => [
                'required',
                Rule::in([
                    'eligible',
                    'pending',
                    'not_eligible',
                ]),
            ],
            'notes' => [
                'nullable',
                'required_if:status,not_eligible',
                'string',
                'max:10000',
            ],
        ], [
            'notes.required_if' =>
                'Explain why the patient is not eligible.',
        ]);

        $review = $patient->eligibilityReviews()->make([
            'status' => $data['status'],
            'notes' => $data['notes'] ?? null,
        ]);

        $review->reviewed_by = $request->user()->id;
        $review->reviewed_at = now();
        $review->save();

        return redirect()
            ->route('patients.eligibility.edit', $patient)
            ->with(
                'success',
                'Eligibility review saved successfully.'
            );
    }
}
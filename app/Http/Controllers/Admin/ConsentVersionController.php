<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ConsentVersion;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class ConsentVersionController extends Controller
{
    public function index(): View
    {
        Gate::authorize('manage-users');

        $versions = ConsentVersion::query()
            ->withCount('consents')
            ->orderByDesc('id')
            ->paginate(10);

        return view('admin.consent-versions.index', compact('versions'));
    }

    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('manage-users');

        $data = $request->validate([
            'version_code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('consent_versions', 'version_code'),
            ],
            'title' => [
                'required',
                'string',
                'max:255',
            ],
            'effective_date' => [
                'required',
                'date_format:Y-m-d',
            ],
            'is_active' => [
                'required',
                'boolean',
            ],
            'notes' => [
                'nullable',
                'string',
                'max:10000',
            ],
        ]);

        $data['is_active'] = $request->boolean('is_active');

        if (
            $data['is_active'] &&
            $data['effective_date'] > today()->toDateString()
        ) {
            throw ValidationException::withMessages([
                'effective_date' =>
                    'A version with a future effective date must remain inactive.',
            ]);
        }

        ConsentVersion::create($data);

        return redirect()
            ->route('admin.consent-versions.index')
            ->with('success', 'Consent version created successfully.');
    }

    public function availability(
        Request $request,
        ConsentVersion $consentVersion
    ): RedirectResponse {
        Gate::authorize('manage-users');

        $request->validate([
            'is_active' => ['required', 'boolean'],
        ]);

        $active = $request->boolean('is_active');

        if (
            $active &&
            (
                ! $consentVersion->version_code ||
                ! $consentVersion->title ||
                ! $consentVersion->effective_date ||
                $consentVersion->effective_date->isAfter(today())
            )
        ) {
            throw ValidationException::withMessages([
                'version' =>
                    'Activation requires a version code, title and an effective date that has arrived.',
            ]);
        }

        $consentVersion->is_active = $active;
        $consentVersion->save();

        return back()->with(
            'success',
            $active
                ? 'Consent version activated.'
                : 'Consent version deactivated.'
        );
    }
}
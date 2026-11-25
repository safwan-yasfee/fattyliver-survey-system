@extends('layouts.patients')

@section('title', 'Consent Versions')

@section('content')
    <header class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
        <div>
            <h1 class="h2 fw-bold mb-1">Consent Versions</h1>

            <p class="text-secondary mb-0">
                Manage the document versions available for consent recording.
            </p>
        </div>

        <a href="{{ route('dashboard.research') }}"
           class="btn btn-outline-secondary">
            Research Dashboard
        </a>
    </header>

    <section class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4">
            <h2 class="h5 mb-3">Create Consent Version</h2>

            <form method="POST"
                  action="{{ route('admin.consent-versions.store') }}">
                @csrf

                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="version_code" class="form-label">
                            Version Code
                        </label>

                        <input id="version_code"
                               name="version_code"
                               type="text"
                               class="form-control"
                               maxlength="50"
                               value="{{ old('version_code') }}"
                               required>

                        <div class="form-text">
                            Enter the version identifier from your consent document.
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label for="effective_date" class="form-label">
                            Effective Date
                        </label>

                        <input id="effective_date"
                               name="effective_date"
                               type="date"
                               class="form-control"
                               value="{{ old('effective_date') }}"
                               required>
                    </div>

                    <div class="col-12">
                        <label for="title" class="form-label">
                            Document Title
                        </label>

                        <input id="title"
                               name="title"
                               type="text"
                               class="form-control"
                               maxlength="255"
                               value="{{ old('title') }}"
                               required>
                    </div>

                    <div class="col-md-6">
                        <label for="is_active" class="form-label">
                            Availability
                        </label>

                        <select id="is_active"
                                name="is_active"
                                class="form-select"
                                required>
                            <option value="0"
                                    @selected((string) old('is_active', '0') === '0')>
                                Inactive
                            </option>

                            <option value="1"
                                    @selected((string) old('is_active', '0') === '1')>
                                Active — available for selection
                            </option>
                        </select>
                    </div>

                    <div class="col-12">
                        <label for="notes" class="form-label">
                            Notes
                        </label>

                        <textarea id="notes"
                                  name="notes"
                                  class="form-control"
                                  rows="3"
                                  maxlength="10000">{{ old('notes') }}</textarea>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary mt-4">
                    Save Consent Version
                </button>
            </form>
        </div>
    </section>

    <section class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4">
            <h2 class="h5 mb-3">Recorded Versions</h2>

            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th scope="col">Version</th>
                            <th scope="col">Title</th>
                            <th scope="col">Effective Date</th>
                            <th scope="col">Status</th>
                            <th scope="col">Consent Records</th>
                            <th scope="col">Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($versions as $version)
                            <tr>
                                <td>{{ $version->version_code ?? '—' }}</td>
                                <td>{{ $version->title ?? '—' }}</td>

                                <td>
                                    {{ $version->effective_date?->format('d M Y') ?? '—' }}
                                </td>

                                <td>
                                    <span class="badge text-bg-{{ $version->is_active ? 'success' : 'secondary' }}">
                                        {{ $version->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>

                                <td>{{ $version->consents_count }}</td>

                                <td>
                                    <form method="POST"
                                          action="{{ route('admin.consent-versions.availability', $version) }}"
                                          onsubmit="return confirm('Change availability for this consent version?');">
                                        @csrf
                                        @method('PATCH')

                                        <input type="hidden"
                                               name="is_active"
                                               value="{{ $version->is_active ? '0' : '1' }}">

                                        <button type="submit"
                                                class="btn btn-sm btn-outline-primary">
                                            {{ $version->is_active ? 'Deactivate' : 'Activate' }}
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">
                                    No consent versions recorded.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($versions->hasPages())
                <div class="mt-3">
                    {{ $versions->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>
    </section>
@endsection
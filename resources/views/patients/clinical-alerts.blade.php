@extends('layouts.patients')

@section('title', 'Clinical Alerts')

@section('content')
    <header class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
        <div>
            <h1 class="h2 fw-bold mb-1">Clinical Alerts</h1>

            <p class="text-secondary mb-0">
                {{ $patient->full_name }}
                &middot; Code {{ $patient->study_code }}
                &middot;
                {{ ucwords(str_replace('_', ' ', $studyVisit->time_point)) }}
            </p>
        </div>

        <a href="{{ route('patients.show', $patient) }}"
           class="btn btn-outline-secondary">
            Patient Profile
        </a>
    </header>

    <section class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4">
            <h2 class="h5 mb-3">Create Alert</h2>

            <form method="POST"
                  action="{{ route('patients.visits.alerts.store', [$patient, $studyVisit]) }}">
                @csrf

                <div class="mb-3">
                    <label for="alert_type" class="form-label">
                        Alert Category
                    </label>

                    <input id="alert_type"
                           name="alert_type"
                           type="text"
                           maxlength="100"
                           class="form-control"
                           value="{{ old('alert_type') }}"
                           required>
                </div>

                <div class="mb-3">
                    <label for="message" class="form-label">
                        Alert Details
                    </label>

                    <textarea id="message"
                              name="message"
                              rows="4"
                              maxlength="10000"
                              class="form-control"
                              required>{{ old('message') }}</textarea>
                </div>

                <button type="submit" class="btn btn-danger">
                    Save Alert
                </button>
            </form>
        </div>
    </section>

    <section class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4">
            <h2 class="h5 mb-3">Recorded Alerts</h2>

            @forelse ($alerts as $alert)
                <article class="border rounded-3 p-3 mb-3">
                    <div class="d-flex justify-content-between align-items-start gap-3">
                        <h3 class="h6">{{ $alert->alert_type ?? 'Uncategorized' }}</h3>

                        @if ($alert->status === 'open')
                            <span class="badge text-bg-danger">Open</span>
                        @elseif ($alert->status === 'resolved')
                            <span class="badge text-bg-success">Resolved</span>
                        @else
                            <span class="badge text-bg-secondary">
                                Status not recorded
                            </span>
                        @endif
                    </div>

                    <p style="white-space: pre-line">{{ $alert->message }}</p>

                    <p class="small text-secondary">
                        Created by:
                        {{ $alert->recordedBy?->name ?? 'Unavailable' }}
                        &middot;
                        {{ $alert->created_at?->format('d M Y H:i') }}
                    </p>

                    @if ($alert->status === 'open')
                        <form method="POST"
                              action="{{ route('patients.visits.alerts.resolve', [$patient, $studyVisit, $alert]) }}"
                              onsubmit="return confirm('Mark this alert as resolved?');">
                            @csrf
                            @method('PATCH')

                            <div class="mb-3">
                                <label for="resolution_{{ $alert->id }}"
                                       class="form-label">
                                    Resolution Notes
                                </label>

                                <textarea id="resolution_{{ $alert->id }}"
                                          name="resolution_notes"
                                          rows="3"
                                          maxlength="10000"
                                          class="form-control"
                                          required></textarea>
                            </div>

                            <button type="submit"
                                    class="btn btn-sm btn-outline-success">
                                Mark Resolved
                            </button>
                        </form>
                    @elseif ($alert->status === 'resolved')
                        <div class="border-top pt-3">
                            <p class="small text-secondary mb-2">
                                Resolved by:
                                {{ $alert->resolvedBy?->name ?? 'Unavailable' }}
                                &middot;
                                {{ $alert->resolved_at?->format('d M Y H:i') }}
                            </p>

                            <p class="mb-0" style="white-space: pre-line">{{ $alert->resolution_notes ?? 'No resolution notes recorded.' }}</p>
                        </div>
                    @endif
                </article>
            @empty
                <p class="text-secondary mb-0">
                    No clinical alerts recorded for this visit.
                </p>
            @endforelse
        </div>
    </section>
@endsection
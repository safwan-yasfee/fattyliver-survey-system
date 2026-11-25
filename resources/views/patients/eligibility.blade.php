@extends('layouts.patients')

@section('title', 'Eligibility Review')

@section('content')
    @php
        $statusLabels = [
            'eligible' => 'Eligible',
            'pending' => 'Pending Review',
            'not_eligible' => 'Not Eligible',
        ];

        $statusColors = [
            'eligible' => 'success',
            'pending' => 'warning',
            'not_eligible' => 'danger',
        ];
    @endphp

    <header class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
        <div>
            <h1 class="h2 fw-bold mb-1">Eligibility Review</h1>

            <p class="text-secondary mb-0">
                {{ $patient->full_name }}
                · Code {{ $patient->study_code }}
            </p>
        </div>

        <a href="{{ route('patients.show', $patient) }}"
           class="btn btn-outline-secondary">
            Patient Profile
        </a>
    </header>

    <section class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4">
            <h2 class="h5 mb-3">Current Eligibility</h2>

            @if ($latestReview)
                <span class="badge text-bg-{{ $statusColors[$latestReview->status] ?? 'secondary' }}">
                    {{ $statusLabels[$latestReview->status] ?? 'Unknown Status' }}
                </span>

                <p class="text-secondary mt-3 mb-2">
                    Reviewed by
                    {{ $latestReview->reviewer?->name ?? 'Reviewer unavailable' }}
                    on
                    {{ $latestReview->reviewed_at?->format('d M Y, h:i A') ?? '—' }}
                </p>

                @if ($latestReview->notes)
                    <p class="mb-0" style="white-space: pre-wrap">{{ $latestReview->notes }}</p>
                @endif
            @else
                <p class="text-secondary mb-0">
                    No eligibility review has been recorded.
                </p>
            @endif
        </div>
    </section>

    @can('review-patient-eligibility')
        <section class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-4">
                <h2 class="h5 mb-3">Record New Review</h2>

                <p class="text-secondary">
                    Record your decision using the approved study criteria.
                    Each save adds a new entry to the review history.
                </p>

                <form method="POST"
                      action="{{ route('patients.eligibility.store', $patient) }}">
                    @csrf

                    <div class="mb-3">
                        <label for="status" class="form-label">
                            Eligibility Decision
                        </label>

                        <select id="status"
                                name="status"
                                class="form-select"
                                required>
                            @foreach ($statusLabels as $value => $label)
                                <option value="{{ $value }}"
                                        @selected(old('status', 'pending') === $value)>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="notes" class="form-label">
                            Review Notes
                        </label>

                        <textarea id="notes"
                                  name="notes"
                                  class="form-control"
                                  rows="5"
                                  maxlength="10000"
                                  aria-describedby="notes-help">{{ old('notes') }}</textarea>

                        <div id="notes-help" class="form-text">
                            An explanation is required for Not Eligible.
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">
                        Save Eligibility Review
                    </button>
                </form>
            </div>
        </section>
    @endcan

    <section class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4">
            <h2 class="h5 mb-3">Review History</h2>

            @forelse ($reviews as $review)
                <article class="border-bottom pb-3 mb-3">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <span class="badge text-bg-{{ $statusColors[$review->status] ?? 'secondary' }}">
                            {{ $statusLabels[$review->status] ?? 'Unknown Status' }}
                        </span>

                        <span class="text-secondary small">
                            {{ $review->reviewed_at?->format('d M Y, h:i A') ?? '—' }}
                        </span>
                    </div>

                    <p class="small text-secondary mt-2 mb-2">
                        Reviewer:
                        {{ $review->reviewer?->name ?? 'Reviewer unavailable' }}
                    </p>

                    @if ($review->notes)
                        <p class="mb-0" style="white-space: pre-wrap">{{ $review->notes }}</p>
                    @else
                        <p class="text-secondary mb-0">
                            No notes recorded.
                        </p>
                    @endif
                </article>
            @empty
                <p class="text-secondary mb-0">
                    No reviews recorded.
                </p>
            @endforelse

            @if ($reviews->hasPages())
                <div class="mt-3">
                    {{ $reviews->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>
    </section>
@endsection
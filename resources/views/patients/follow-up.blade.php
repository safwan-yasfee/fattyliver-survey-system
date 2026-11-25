@extends('layouts.patients')

@section('title', 'Follow-up Notes')

@section('content')
    @php
        $timePoints = [
            'baseline' => 'Baseline',
            'month_3' => '3 Months',
            'month_6' => '6 Months',
            'month_12' => '12 Months',
        ];

        $fields = [
            'progress_notes' => 'Progress Notes',
            'assessment' => 'Assessment',
            'plan' => 'Follow-up Plan',
        ];
    @endphp

    <header class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
        <div>
            <h1 class="h2 fw-bold mb-1">Follow-up Notes</h1>

            <p class="text-secondary mb-0">
                {{ $patient->full_name }}
                &middot; Code {{ $patient->study_code }}
                &middot;
                {{ $timePoints[$studyVisit->time_point] ?? $studyVisit->time_point }}
            </p>
        </div>

        <a href="{{ route('patients.show', $patient) }}"
           class="btn btn-outline-secondary">
            Patient Profile
        </a>
    </header>

    <section class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4">
            <h2 class="h5 mb-3">
                {{ $entry ? 'Edit Follow-up Note' : 'Add Follow-up Note' }}
            </h2>

            <form method="POST"
                  action="{{ $entry
                      ? route('patients.visits.follow-up.update', [$patient, $studyVisit, $entry])
                      : route('patients.visits.follow-up.store', [$patient, $studyVisit]) }}">
                @csrf

                @if ($entry)
                    @method('PUT')
                @endif

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label for="follow_up_date" class="form-label">
                            Follow-up Date
                        </label>

                        <input id="follow_up_date"
                               name="follow_up_date"
                               type="date"
                               class="form-control"
                               max="{{ now()->toDateString() }}"
                               value="{{ old('follow_up_date', $entry?->follow_up_date?->format('Y-m-d')) }}"
                               required>
                    </div>

                    <div class="col-md-6">
                        <label for="next_follow_up_date" class="form-label">
                            Next Follow-up Date
                        </label>

                        <input id="next_follow_up_date"
                               name="next_follow_up_date"
                               type="date"
                               class="form-control"
                               value="{{ old('next_follow_up_date', $entry?->next_follow_up_date?->format('Y-m-d')) }}">

                        <div class="form-text">
                            Records a planned date; does not schedule a study visit.
                        </div>
                    </div>
                </div>

                @foreach ($fields as $field => $label)
                    <div class="mb-4">
                        <label for="{{ $field }}" class="form-label">
                            {{ $label }}
                        </label>

                        <textarea id="{{ $field }}"
                                  name="{{ $field }}"
                                  rows="4"
                                  maxlength="10000"
                                  class="form-control"
                                  @required($field === 'progress_notes')>{{ old($field, $entry?->{$field}) }}</textarea>
                    </div>
                @endforeach

                <div class="d-flex flex-wrap gap-2">
                    <button type="submit" class="btn btn-primary">
                        {{ $entry ? 'Save Changes' : 'Save Follow-up Note' }}
                    </button>

                    <a href="{{ route('patients.visits.follow-up.index', [$patient, $studyVisit]) }}"
                       class="btn btn-outline-secondary">
                        {{ $entry ? 'Cancel Edit' : 'Clear Form' }}
                    </a>
                </div>
            </form>
        </div>
    </section>

    <section class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4">
            <h2 class="h5 mb-3">Recorded Follow-ups</h2>

            @forelse ($entries as $record)
                <article class="border rounded-3 p-3 mb-3">
                    <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 mb-3">
                        <div>
                            <h3 class="h6 mb-1">
                                {{ $record->follow_up_date?->format('d M Y') }}
                            </h3>

                            <p class="small text-secondary mb-0">
                                Recorded by:
                                {{ $record->recordedBy?->name ?? 'Unavailable' }}
                            </p>
                        </div>

                        <a href="{{ route('patients.visits.follow-up.index', [
                            'patient' => $patient,
                            'visit' => $studyVisit->getKey(),
                            'entry' => $record->id,
                        ]) }}"
                           class="btn btn-sm btn-outline-primary">
                            Edit
                        </a>
                    </div>

                    @foreach ($fields as $field => $label)
                        <h4 class="h6">{{ $label }}</h4>
                        <p style="white-space: pre-line">{{ $record->{$field} ?? 'Not recorded' }}</p>
                    @endforeach

                    <p class="mb-0">
                        <strong>Next Follow-up:</strong>
                        {{ $record->next_follow_up_date?->format('d M Y') ?? 'Not recorded' }}
                    </p>
                </article>
            @empty
                <p class="text-secondary mb-0">
                    No follow-up notes recorded for this visit.
                </p>
            @endforelse
        </div>
    </section>
@endsection
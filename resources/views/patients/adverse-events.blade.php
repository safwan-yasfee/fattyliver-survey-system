@extends('layouts.patients')

@section('title', 'Adverse Events')

@section('content')
    @php
        $timePoints = [
            'baseline' => 'Baseline',
            'month_3' => '3 Months',
            'month_6' => '6 Months',
            'month_12' => '12 Months',
        ];

        $seriousValue = old(
            'is_serious',
            $event?->is_serious === null
                ? ''
                : ($event->is_serious ? '1' : '0')
        );

        $textFields = [
            'description' => 'Event Description',
            'treatment' => 'Action Taken / Treatment',
            'outcome' => 'Outcome',
            'notes' => 'Additional Notes',
        ];
    @endphp

    <header class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
        <div>
            <h1 class="h2 fw-bold mb-1">Adverse Events</h1>

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
            <h2 class="h5 mb-2">
                {{ $event ? 'Edit Adverse Event' : 'Record Adverse Event' }}
            </h2>

            <p class="text-secondary mb-4">
                Record reported event details and the clinician's assessment.
                Leave unavailable information blank.
            </p>

            <form method="POST"
                  action="{{ $event
                      ? route('patients.visits.adverse-events.update', [$patient, $studyVisit, $event])
                      : route('patients.visits.adverse-events.store', [$patient, $studyVisit]) }}">
                @csrf

                @if ($event)
                    @method('PUT')
                @endif

                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="event_date" class="form-label">
                            Event Date
                        </label>

                        <input id="event_date"
                               name="event_date"
                               type="date"
                               class="form-control"
                               max="{{ now()->toDateString() }}"
                               value="{{ old('event_date', $event?->event_date?->format('Y-m-d')) }}"
                               required>
                    </div>

                    <div class="col-md-4">
                        <label for="severity" class="form-label">
                            Reported Severity
                        </label>

                        <input id="severity"
                               name="severity"
                               type="text"
                               maxlength="30"
                               class="form-control"
                               value="{{ old('severity', $event?->severity) }}">
                    </div>

                    <div class="col-md-4">
                        <label for="is_serious" class="form-label">
                            Assessed as Serious?
                        </label>

                        <select id="is_serious"
                                name="is_serious"
                                class="form-select">
                            <option value=""
                                @selected($seriousValue === null || $seriousValue === '')>
                                Not assessed / Unknown
                            </option>

                            <option value="1"
                                @selected((string) $seriousValue === '1')>
                                Yes
                            </option>

                            <option value="0"
                                @selected((string) $seriousValue === '0')>
                                No
                            </option>
                        </select>
                    </div>

                    @foreach ($textFields as $field => $label)
                        <div class="col-12">
                            <label for="{{ $field }}" class="form-label">
                                {{ $label }}
                            </label>

                            <textarea id="{{ $field }}"
                                      name="{{ $field }}"
                                      rows="3"
                                      maxlength="10000"
                                      class="form-control"
                                      @required($field === 'description')>{{ old($field, $event?->{$field}) }}</textarea>
                        </div>
                    @endforeach
                </div>

                <div class="d-flex flex-wrap gap-2 mt-4">
                    <button type="submit" class="btn btn-primary">
                        {{ $event ? 'Save Changes' : 'Save Adverse Event' }}
                    </button>

                    <a href="{{ route('patients.visits.adverse-events.index', [$patient, $studyVisit]) }}"
                       class="btn btn-outline-secondary">
                        {{ $event ? 'Cancel Edit' : 'Clear Form' }}
                    </a>
                </div>
            </form>
        </div>
    </section>

    <section class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4">
            <h2 class="h5 mb-3">Recorded Events</h2>

            @forelse ($events as $record)
                <article class="border rounded-3 p-3 mb-3">
                    <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                        <div>
                            <h3 class="h6 mb-1">
                                Event on {{ $record->event_date?->format('d M Y') }}
                            </h3>

                            <p class="small text-secondary mb-2">
                                Recorded by
                                {{ $record->recordedBy?->name ?? 'Unavailable' }}
                            </p>
                        </div>

                        <a href="{{ route('patients.visits.adverse-events.index', [
                            'patient' => $patient,
                            'visit' => $studyVisit->getKey(),
                            'event' => $record->id,
                        ]) }}"
                           class="btn btn-sm btn-outline-primary">
                            Edit
                        </a>
                    </div>

                    <p style="white-space: pre-line">{{ $record->description }}</p>

                    <dl class="row mb-0">
                        <dt class="col-sm-3">Severity</dt>
                        <dd class="col-sm-9">
                            {{ $record->severity ?? 'Not recorded' }}
                        </dd>

                        <dt class="col-sm-3">Serious?</dt>
                        <dd class="col-sm-9">
                            @if ($record->is_serious === null)
                                Not assessed / Unknown
                            @elseif ($record->is_serious)
                                Yes
                            @else
                                No
                            @endif
                        </dd>

                        <dt class="col-sm-3">Action / Treatment</dt>
                        <dd class="col-sm-9" style="white-space: pre-line">{{ $record->treatment ?? 'Not recorded' }}</dd>

                        <dt class="col-sm-3">Outcome</dt>
                        <dd class="col-sm-9" style="white-space: pre-line">{{ $record->outcome ?? 'Not recorded' }}</dd>

                        <dt class="col-sm-3">Notes</dt>
                        <dd class="col-sm-9" style="white-space: pre-line">{{ $record->notes ?? 'Not recorded' }}</dd>
                    </dl>
                </article>
            @empty
                <p class="text-secondary mb-0">
                    No adverse events have been recorded for this visit.
                </p>
            @endforelse
        </div>
    </section>
@endsection
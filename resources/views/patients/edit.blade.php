@extends('layouts.patients')

@section('title', 'Edit Patient')

@section('content')
    <header class="d-flex justify-content-between align-items-center
                                   flex-wrap gap-3 mb-4">
        <div>
            <h1 class="h2 mb-1">Edit Patient</h1>
            <p class="text-secondary mb-0">
                Patient ID {{ $patient->patient_id }}
            </p>
        </div>

        <a href="{{ route('patients.index') }}" class="btn btn-outline-secondary">
            Patient List
        </a>
    </header>

    <section class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4">
            <form method="POST" action="{{ route('patients.update', $patient) }}">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="patient_code" class="form-label">
                        Patient Code
                    </label>

                    <input id="patient_code" type="text" class="form-control" value="{{ $patient->study_code }}" readonly>
                </div>

                <div class="col-md-6">
                    <label for="full_name" class="form-label">
                        Full Name *
                    </label>

                    <input id="full_name" name="full_name" type="text" class="form-control"
                        value="{{ old('full_name', $patient->full_name) }}" maxlength="255" required>
                </div>

                <div class="col-md-6">
                    <label for="date_of_birth" class="form-label">
                        Date of Birth
                    </label>

                    <input id="date_of_birth" name="date_of_birth" type="date" class="form-control"
                        value="{{ old('date_of_birth', $patient->date_of_birth?->format('Y-m-d')) }}"
                        max="{{ now()->toDateString() }}">
                </div>

                <div class="col-md-6">
                    <label for="sex" class="form-label">Sex</label>

                    <select id="sex" name="sex" class="form-select">
                        <option value="">Select sex</option>

                        @foreach (['Male', 'Female', 'Other'] as $sex)
                            <option value="{{ $sex }}" @selected(old('sex', $patient->sex) === $sex)>
                                {{ $sex }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6">
                    <label for="phone" class="form-label">Phone</label>

                    <input id="phone" name="phone" type="tel" class="form-control" maxlength="14"
                        value="{{ old('phone', $patient->phone) }}">
                </div>

                @include('patients.partials.profile-details', [
                    'patient' => $patient,
                ])



                <div class="d-flex gap-2 mt-4">
                    <button type="submit" class="btn btn-primary">
                        Save Changes
                    </button>

                    <a href="{{ route('patients.index') }}" class="btn btn-outline-secondary">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </section>
@endsection
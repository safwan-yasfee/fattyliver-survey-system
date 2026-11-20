@extends('layouts.patients')

@section('title', 'Add Patient')

@section('content')
    <header class="d-flex justify-content-between align-items-center
                   flex-wrap gap-3 mb-4">
        <div>
            <h1 class="h2 fw-bold mb-1">Add Patient</h1>
            <p class="text-secondary mb-0">
                Enter the patient's profile information.
            </p>
        </div>

        <a href="{{ route('patients.index') }}"
           class="btn btn-outline-secondary">
            Patient List
        </a>
    </header>

    <section class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4">
            <form method="POST" action="{{ route('patients.store') }}">
                @csrf

                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="study_code" class="form-label">
                            Code *
                        </label>

                        <input
                            id="study_code"
                            name="study_code"
                            type="text"
                            class="form-control"
                            value="{{ old('study_code') }}"
                            maxlength="30"
                            required
                        >
                    </div>

                    <div class="col-md-6">
                        <label for="full_name" class="form-label">
                            Full Name *
                        </label>

                        <input
                            id="full_name"
                            name="full_name"
                            type="text"
                            class="form-control"
                            value="{{ old('full_name') }}"
                            maxlength="255"
                            required
                        >
                    </div>

                    <div class="col-md-6">
                        <label for="date_of_birth" class="form-label">
                            Date of Birth
                        </label>

                        <input
                            id="date_of_birth"
                            name="date_of_birth"
                            type="date"
                            class="form-control"
                            value="{{ old('date_of_birth') }}"
                            max="{{ now()->toDateString() }}"
                        >
                    </div>

                    <div class="col-md-6">
                        <label for="sex" class="form-label">Sex</label>

                        <select id="sex" name="sex" class="form-select">
                            <option value="">Select sex</option>

                            @foreach (['Male', 'Female', 'Other'] as $sex)
                                <option value="{{ $sex }}"
                                        @selected(old('sex') === $sex)>
                                    {{ $sex }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label for="phone" class="form-label">Phone</label>

                        <input
                            id="phone"
                            name="phone"
                            type="tel"
                            class="form-control"
                            value="{{ old('phone') }}"
                            maxlength="30"
                        >
                    </div>
                </div>

                <div class="d-flex gap-2 mt-4">
                    <button type="submit" class="btn btn-primary">
                        Save Patient
                    </button>

                    <a href="{{ route('patients.index') }}"
                       class="btn btn-outline-secondary">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </section>
@endsection
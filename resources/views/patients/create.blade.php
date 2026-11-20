@extends('layouts.patients')

@section('title', 'Add Patient')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h2">Add Patient</h1>

        <a href="{{ route('patients.index') }}"
           class="btn btn-outline-secondary">
            Patient List
        </a>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4">
            <form method="POST" action="{{ route('patients.store') }}">
                @csrf

                <div class="row g-3">
                    <div class="col-md-6">
                        <label for="study_code" class="form-label">Code *</label>
                        <input id="study_code" name="study_code" type="text"
                               class="form-control"
                               value="{{ old('study_code') }}"
                               maxlength="30" required>
                    </div>

                    <div class="col-md-6">
                        <label for="full_name" class="form-label">Full Name *</label>
                        <input id="full_name" name="full_name" type="text"
                               class="form-control"
                               value="{{ old('full_name') }}"
                               maxlength="255" required>
                    </div>

                    <div class="col-md-6">
                        <label for="date_of_birth" class="form-label">
                            Date of Birth
                        </label>
                        <input id="date_of_birth" name="date_of_birth"
                               type="date" class="form-control"
                               value="{{ old('date_of_birth') }}"
                               max="{{ now()->toDateString() }}">
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
                        <input id="phone" name="phone" type="tel"
                               class="form-control"
                               value="{{ old('phone') }}"
                               maxlength="30">
                    </div>
                </div>

                <button type="submit" class="btn btn-primary mt-4">
                    Save Patient
                </button>
            </form>
        </div>
    </div>
@endsection

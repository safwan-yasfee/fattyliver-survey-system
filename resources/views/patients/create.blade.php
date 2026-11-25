@extends('layouts.patients')

@section('title', 'Add Patient')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h2">Add Patient</h1>

        <a href="{{ route('patients.index') }}" class="btn btn-outline-secondary">
            Patient List
        </a>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4">
            <form method="POST" action="{{ route('patients.store') }}">
                @csrf

                <div class="mb-3">
                    <label class="form-label">Patient Code</label>

                    <p class="form-control-plaintext text-secondary mb-0">
                        Generated automatically after registration.
                    </p>
                </div>
                <div class="col-md-6">
                    <label for="full_name" class="form-label">Full Name *</label>
                    <input id="full_name" name="full_name" type="text" class="form-control" value="{{ old('full_name') }}"
                        maxlength="255" required>
                </div>

                <div class="col-md-6">
                    <label for="date_of_birth" class="form-label">
                        Date of Birth
                    </label>
                    <input id="date_of_birth" name="date_of_birth" type="date" class="form-control"
                        value="{{ old('date_of_birth') }}" max="{{ now()->toDateString() }}">
                </div>

                <div class="col-md-6">
                    <label for="sex" class="form-label">Sex</label>
                    <select id="sex" name="sex" class="form-select">
                        <option value="">Select sex</option>
                        @foreach (['Male', 'Female', 'Other'] as $sex)
                            <option value="{{ $sex }}" @selected(old('sex') === $sex)>
                                {{ $sex }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label for="phone" class="form-label">
                        Mobile Number
                    </label>

                    <input id="phone" name="phone" type="tel" class="form-control" autocomplete="tel" maxlength="14"
                        pattern="(01[3-9][0-9]{8}|\+8801[3-9][0-9]{8})" placeholder="Enter mobile number"
                        value="{{ old('phone') }}" aria-describedby="phone-help">

                    <div id="phone-help" class="form-text">
                        Optional. Enter a Bangladesh mobile number without spaces or hyphens.
                    </div>

                    @error('phone')
                        <div class="text-danger small mt-1">
                            {{ $message }}
                        </div>
                    @enderror
                </div>

                @include('patients.partials.profile-details', [
                    'patient' => null,
                ])

        </div>

        <button type="submit" class="btn btn-primary mt-4">
            Save Patient
        </button>
        </form>
    </div>

@endsection
<div class="row g-3 mb-3">
    <div class="col-md-6">
        <label for="occupation" class="form-label">
            Occupation
        </label>

        <input id="occupation"
               name="occupation"
               type="text"
               class="form-control"
               maxlength="150"
               value="{{ old('occupation', $patient?->occupation) }}">
    </div>

    <div class="col-md-6">
        <label for="education_level" class="form-label">
            Education Level
        </label>

        <input id="education_level"
               name="education_level"
               type="text"
               class="form-control"
               maxlength="100"
               value="{{ old('education_level', $patient?->education_level) }}">
    </div>

    <div class="col-md-6">
        <label for="email" class="form-label">
            Patient Email
        </label>

        <input id="email"
               name="email"
               type="email"
               class="form-control"
               maxlength="255"
               autocomplete="email"
               value="{{ old('email', $patient?->email) }}">
    </div>

    <div class="col-12">
        <label for="address" class="form-label">
            Address
        </label>

        <textarea id="address"
                  name="address"
                  class="form-control"
                  rows="3"
                  maxlength="2000"
                  autocomplete="street-address">{{ old('address', $patient?->address) }}</textarea>
    </div>
</div>
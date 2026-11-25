<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Patient extends Model
{
    use SoftDeletes;

    protected $primaryKey = 'patient_id';

    protected $fillable = [
        'study_code',
        'full_name',
        'date_of_birth',
        'sex',
        'phone',
        'occupation',
        'education_level',
        'address',
        'email',
    ];


    public function consents(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(
            Consent::class,
            'patient_id',
            'patient_id'
        );
    }

    public function latestConsent(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(
            Consent::class,
            'patient_id',
            'patient_id'
        )->latestOfMany('id');
    }

    public function eligibilityReviews(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(
            EligibilityReview::class,
            'patient_id',
            'patient_id'
        );
    }

    public function latestEligibilityReview(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(
            EligibilityReview::class,
            'patient_id',
            'patient_id'
        )->latestOfMany('id');
    }





    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
        ];
    }

    public function medicalHistory(): HasOne
    {
        return $this->hasOne(
            MedicalHistory::class,
            'patient_id',
            'patient_id'
        );
    }

    public function visits(): HasMany
    {
        return $this->hasMany(
            Visit::class,
            'patient_id',
            'patient_id'
        );
    }

    public function lifestyleProfile(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(
            LifestyleProfile::class,
            'patient_id',
            'patient_id'
        );
    }
}
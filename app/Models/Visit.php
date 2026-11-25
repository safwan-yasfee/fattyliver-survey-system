<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Visit extends Model
{
    protected $primaryKey = 'visit_id';

    protected $fillable = [
        'time_point',
        'due_date',
        'visited_at',
    ];

    public function labResults(): \Illuminate\Database\Eloquent\Relations\HasMany
{
    return $this->hasMany(
        LabResult::class,
        'visit_id',
        'visit_id'
    );
}

    protected function casts(): array
    {
        return [
            'due_date' => 'date',
            'visited_at' => 'datetime',
        ];


        
    }


    public function adverseEvents(): \Illuminate\Database\Eloquent\Relations\HasMany
{
    return $this->hasMany(
        AdverseEvent::class,
        'visit_id',
        'visit_id'
    );
}

    public function patient(): BelongsTo
    {
        return $this->belongsTo(
            Patient::class,
            'patient_id',
            'patient_id'
        );
    }


    public function dietPlan(): \Illuminate\Database\Eloquent\Relations\HasOne
{
    return $this->hasOne(
        DietPlan::class,
        'visit_id',
        'visit_id'
    );
}


public function followUps(): \Illuminate\Database\Eloquent\Relations\HasMany
{
    return $this->hasMany(
        FollowUp::class,
        'visit_id',
        'visit_id'
    );
}

    public function treatments(): \Illuminate\Database\Eloquent\Relations\HasMany
{
    return $this->hasMany(
        Treatment::class,
        'visit_id',
        'visit_id'
    );
}

    public function elastographyResult(): \Illuminate\Database\Eloquent\Relations\HasOne
{
    return $this->hasOne(
        ElastographyResult::class,
        'visit_id',
        'visit_id'
    );
}

public function clinicalAlerts(): \Illuminate\Database\Eloquent\Relations\HasMany
{
    return $this->hasMany(
        ClinicalAlert::class,
        'visit_id',
        'visit_id'
    );
}

    public function vitals(): HasOne
    {
        return $this->hasOne(
            VisitVital::class,
            'visit_id',
            'visit_id'
        );
    }
}
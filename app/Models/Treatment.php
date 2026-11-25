<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Treatment extends Model
{
    protected $fillable = [
        'medication_name',
        'dose',
        'frequency',
        'route',
        'start_date',
        'end_date',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
        ];
    }

    public function visit(): BelongsTo
    {
        return $this->belongsTo(
            Visit::class,
            'visit_id',
            'visit_id'
        );
    }


    public function adherence(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(
            Adherence::class,
            'treatment_id',
            'id'
        );
    }

    public function recordedBy(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'recorded_by',
            'id'
        );
    }
}
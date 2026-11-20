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

    protected function casts(): array
    {
        return [
            'due_date' => 'date',
            'visited_at' => 'datetime',
        ];
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(
            Patient::class,
            'patient_id',
            'patient_id'
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
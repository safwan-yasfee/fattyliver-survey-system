<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdverseEvent extends Model
{
    protected $fillable = [
        'event_date',
        'description',
        'severity',
        'is_serious',
        'treatment',
        'outcome',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'event_date' => 'date',
            'is_serious' => 'boolean',
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

    public function recordedBy(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'recorded_by',
            'id'
        );
    }
}
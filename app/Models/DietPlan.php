<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DietPlan extends Model
{
    protected $fillable = [
        'counselling_date',
        'diet_advice',
        'exercise_advice',
        'goals',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'counselling_date' => 'date',
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
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FollowUp extends Model
{
    protected $table = 'follow_up';

    protected $fillable = [
        'follow_up_date',
        'progress_notes',
        'assessment',
        'plan',
        'next_follow_up_date',
    ];

    protected function casts(): array
    {
        return [
            'follow_up_date' => 'date',
            'next_follow_up_date' => 'date',
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
        return $this->belongsTo(User::class, 'recorded_by', 'id');
    }
}
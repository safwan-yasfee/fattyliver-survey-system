<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Adherence extends Model
{
    protected $table = 'adherence';

    protected $fillable = [
        'assessment_date',
        'tablets_expected',
        'tablets_taken',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'assessment_date' => 'date',
            'tablets_expected' => 'integer',
            'tablets_taken' => 'integer',
            'adherence_percentage' => 'decimal:2',
        ];
    }

    public function treatment(): BelongsTo
    {
        return $this->belongsTo(Treatment::class, 'treatment_id', 'id');
    }
}
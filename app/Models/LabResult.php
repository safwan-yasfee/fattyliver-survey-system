<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LabResult extends Model
{
    protected $fillable = [
        'lab_test_id',
        'result_value',
        'result_text',
        'unit',
        'tested_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'result_value' => 'decimal:4',
            'tested_at' => 'date',
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

    public function labTest(): BelongsTo
    {
        return $this->belongsTo(
            LabTest::class,
            'lab_test_id',
            'id'
        );
    }
}
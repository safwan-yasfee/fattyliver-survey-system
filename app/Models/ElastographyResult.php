<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ElastographyResult extends Model
{
    protected $fillable = [
        'assessment_date',
        'lsm_kpa',
        'cap_db_m',
        'fibrosis_stage',
        'steatosis_grade',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'assessment_date' => 'date',
            'lsm_kpa' => 'decimal:2',
            'cap_db_m' => 'decimal:2',
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
}
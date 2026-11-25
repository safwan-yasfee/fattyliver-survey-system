<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MedicalHistory extends Model
{
    protected $table = 'medical_history';

    protected $fillable = [
        'diseases',
        'alcohol_history',
        'drug_history',
        'biopsy_history',
        'notes',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(
            Patient::class,
            'patient_id',
            'patient_id'
        );
    }
}
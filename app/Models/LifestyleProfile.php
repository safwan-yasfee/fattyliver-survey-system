<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LifestyleProfile extends Model
{
    protected $table = 'lifestyle_profile';

    protected $fillable = [
        'physical_activity',
        'dietary_habits',
        'smoking_history',
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
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VisitVital extends Model
{
    protected $table = 'visit_vitals';

    protected $primaryKey = 'visit_vital_id';

    protected $fillable = [
        'weight_kg',
        'height_cm',
        'waist_cm',
        'systolic_bp',
        'diastolic_bp',
        'heart_rate',
    ];

    public function visit(): BelongsTo
    {
        return $this->belongsTo(
            Visit::class,
            'visit_id',
            'visit_id'
        );
    }
}
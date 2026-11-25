<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Consent extends Model
{
    protected $fillable = [
        'consent_version_id',
        'signed_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'signed_at' => 'datetime',
            'withdrawn_at' => 'datetime',
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

    public function consentVersion(): BelongsTo
    {
        return $this->belongsTo(
            ConsentVersion::class,
            'consent_version_id',
            'id'
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

    public function withdrawnBy(): BelongsTo
    {
        return $this->belongsTo(
            User::class,
            'withdrawn_by',
            'id'
        );
    }
}
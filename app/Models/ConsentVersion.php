<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ConsentVersion extends Model
{
    protected $fillable = [
        'version_code',
        'title',
        'effective_date',
        'is_active',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'effective_date' => 'date',
            'is_active' => 'boolean',
        ];
    }

    public function consents(): HasMany
    {
        return $this->hasMany(
            Consent::class,
            'consent_version_id',
            'id'
        );
    }
}
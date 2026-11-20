<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Patient extends Model
{
    protected $primaryKey = 'patient_id';

    protected $fillable = [
        'study_code',
        'full_name',
        'date_of_birth',
        'sex',
        'phone',
    ];

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
        ];
    }

    public function visits(): HasMany
    {
        return $this->hasMany(
            Visit::class,
            'patient_id',
            'patient_id'
        );
    }
}
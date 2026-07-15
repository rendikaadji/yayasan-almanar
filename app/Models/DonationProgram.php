<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DonationProgram extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'target_amount',
        'collected_amount',
        'deadline',
        'image',
    ];

    protected $casts = [
        'target_amount' => 'integer',
        'collected_amount' => 'integer',
        'deadline' => 'datetime',
    ];

    public function reports(): HasMany
    {
        return $this->hasMany(DonationReport::class);
    }
}

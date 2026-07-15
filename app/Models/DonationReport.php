<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DonationReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'donation_program_id',
        'description',
        'amount',
        'date',
    ];

    protected $casts = [
        'amount' => 'integer',
        'date' => 'date',
    ];

    public function donationProgram(): BelongsTo
    {
        return $this->belongsTo(DonationProgram::class);
    }
}

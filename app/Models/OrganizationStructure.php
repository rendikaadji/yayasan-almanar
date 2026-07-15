<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrganizationStructure extends Model
{
    use HasFactory;

    // Map model to the organization_structure table name in DB
    protected $table = 'organization_structure';

    protected $fillable = [
        'name',
        'position',
        'photo',
        'order',
    ];

    protected $casts = [
        'order' => 'integer',
    ];
}

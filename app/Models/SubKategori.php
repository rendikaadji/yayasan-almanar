<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubKategori extends Model
{
    protected $table = 'sub_kategori';

    protected $fillable = ['bidang_id', 'kategori', 'nama_sub_kategori', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function bidang()
    {
        return $this->belongsTo(Bidang::class, 'bidang_id');
    }
}

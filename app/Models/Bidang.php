<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bidang extends Model
{
    protected $table = 'bidang';

    protected $fillable = ['nama_bidang', 'kode_bidang'];

    public function subKategori()
    {
        return $this->hasMany(SubKategori::class, 'bidang_id');
    }

    public function periodeLaporan()
    {
        return $this->hasMany(PeriodeLaporan::class, 'bidang_id');
    }

    public function users()
    {
        return $this->hasMany(User::class, 'bidang_id');
    }
}

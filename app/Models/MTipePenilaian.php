<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MTipePenilaian extends Model
{

    protected $table = "m_tipe_penilaian";

    protected $fillable = [
        'id_tipe',
        'id_jabatan',
    ];

    public $timestamps = false;

    public function tipePenilaianByTipe()
    {
        return $this->hasMany(MTipePenilaian::class, 'id_tipe', 'id_tipe');
    }

    public function tipe()
    {
        return $this->belongsTo(MTipe::class, 'id_tipe');
    }

    public function jabatan()
    {
        return $this->belongsTo(MJabatan::class, 'id_jabatan');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MValidPenilai extends Model
{
    protected $table = "m_valid_penilai";

    protected $fillable = [
        'id_sub',
        'id_jabatan_penilai',
    ];

    public $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function mSubPenilaian()
    {
        return $this->belongsTo(MSubPenilaian::class, 'id_sub');
    }

    public function jabatanPenilai()
    {
        return $this->belongsTo(MJabatan::class, 'id_jabatan_penilai');
    }
}

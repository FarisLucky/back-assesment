<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MSubPenilaian extends Model
{
    use SoftDeletes;

    const MEDIS = 1;

    const NON_MEDIS = 0;

    protected $table = "m_sub_penilaian";

    protected $fillable = [
        'id_penilaian',
        'nama',
        'kategori',
        'created_by'
    ];

    public $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function getKategoriDescAttribute()
    {
        if (is_null($this->kategori)) {
            return 'ALL';
        }
        return $this->kategori == self::MEDIS ? 'MEDIS' : 'NON MEDIS';
    }

    public function penilaian()
    {
        return $this->belongsTo(MPenilaian::class, 'id_penilaian');
    }

    public function jabatanPenilai()
    {
        return $this->belongsTo(MJabatan::class, 'id_jabatan_penilai');
    }

    public function jabatanKinerja()
    {
        return $this->belongsTo(MJabatan::class, 'id_jabatan_kinerja');
    }

    public function unitPenilai()
    {
        return $this->belongsTo(MUnit::class, 'id_unit_penilai');
    }
    public function mValidasiPenilai()
    {
        return $this->hasMany(MValidPenilai::class, 'id_sub');
    }
}

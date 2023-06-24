<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MSubPenilaian extends Model
{
    use SoftDeletes;

    protected $table = "m_sub_penilaian";

    protected $fillable = [
        'id_penilaian',
        'nama',
        'id_jabatan_penilai',
        'id_jabatan_kinerja',
        'id_unit_penilai',
        'id_parent',
    ];

    public $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

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
}

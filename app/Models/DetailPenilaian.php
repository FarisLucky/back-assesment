<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DetailPenilaian extends Model
{
    use SoftDeletes;

    protected $table = "detail_penilaian";

    protected $fillable = [
        'id_pk',
        'id_tipe_pk',
        'nama_penilaian',
        'ttl_nilai',
        'rata_nilai',
        'id_penilai',
        'nama_penilai',
        'jabatan_penilai',
        'catatan',
        'bobot',
        'updated_by',
    ];

    public $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function getRataNilaiDescAttribute()
    {
        return number_format($this->rata_nilai, 1);
    }

    public function penilaian()
    {
        return $this->belongsTo(PenilaianKaryawan::class, 'id_pk');
    }

    public function tipePenilaian()
    {
        return $this->belongsTo(TipePenilaian::class, 'id_tipe_pk');
    }

    public function subPenilaian()
    {
        return $this->hasMany(SubPenilaianKaryawan::class, 'id_detail');
    }
}

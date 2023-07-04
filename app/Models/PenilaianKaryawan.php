<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PenilaianKaryawan extends Model
{
    use SoftDeletes;

    const STATUS = [
        'draft',
        'selesai',
    ];

    protected $table = "penilaian_karyawan";

    protected $fillable = [
        'id_karyawan',
        'nama_karyawan',
        'jabatan',
        'id_penilai',
        'nama_penilai',
        'jabatan_penilai',
        'tgl_nilai',
        'ttl_nilai',
        'rata_nilai',
        'tipe',
        'status',
        'validasi_by',
        'created_by',
        'updated_by',
    ];

    public $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'tgl_nilai' => 'date',
    ];

    public function detail()
    {
        return $this->hasMany(DetailPenilaian::class, 'id_pk');
    }

    public function karyawan()
    {
        return $this->belongsTo(MKaryawan::class, 'id_karyawan');
    }

    public function tipePenilaian()
    {
        return $this->hasMany(TipePenilaian::class, 'id_pk');
    }

    public function analisisSwot()
    {
        return $this->hasOne(AnalisisSwot::class, 'id_pk');
    }
}

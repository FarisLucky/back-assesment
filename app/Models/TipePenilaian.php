<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TipePenilaian extends Model
{
    use SoftDeletes;

    protected $table = "tipe_penilaian";

    protected $fillable = [
        'id_pk',
        'nama_tipe',
        'tipe_pk',
        'id_karyawan',
        'nama_penilai',
        'jabatan_penilai',
        'catatan',
    ];

    public $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get all of the comments for the TipePenilaian
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function detailPenilaian()
    {
        return $this->hasMany(DetailPenilaian::class, 'id_tipe_pk');
    }
}

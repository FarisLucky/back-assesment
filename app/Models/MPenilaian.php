<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MPenilaian extends Model
{
    use SoftDeletes;

    const TIPE = [
        'pk_umum',
        'pk_khusus',
    ];
    protected $table = "m_penilaian";

    protected $fillable = [
        'nama',
        'tipe',
        'id_jabatan_penilai',
        'level',
    ];

    public $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function subPenilaian()
    {
        return $this->hasMany(MSubPenilaian::class, 'id_penilaian');
    }

    public function jabatan()
    {
        return $this->belongsTo(MJabatan::class, 'level', 'level');
    }

    public static function boot()
    {
        parent::boot();
        static::deleting(function ($penilaian) {
            $penilaian->subPenilaian()->delete();
        });
    }
}

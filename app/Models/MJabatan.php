<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MJabatan extends Model
{
    use SoftDeletes;

    const MEDIS = 1;

    const NON_MEDIS = 0;

    protected $table = "m_jabatan";

    protected $fillable = [
        'nama',
        'id_parent',
        'level',
    ];

    public $casts = [
        "created_at" => "datetime",
        "updated_at" => "datetime",
    ];

    protected $appends = [
        'kategori_desc'
    ];

    // protected $appends = ['level_custom'];

    public function getKategoriDescAttribute()
    {
        return $this->kategori == self::MEDIS ? 'MEDIS' : 'NON MEDIS';
    }

    public function parent()
    {
        return $this->belongsTo(MJabatan::class, 'id_parent');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MJabatan extends Model
{
    use SoftDeletes;

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

    // protected $appends = ['level_custom'];

    public function parent()
    {
        return $this->belongsTo(MJabatan::class, 'id_parent');
    }
}

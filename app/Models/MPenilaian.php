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
        'id_tipe',
        'bobot'
    ];

    public $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public static function boot()
    {
        parent::boot();

        static::deleting(function (MPenilaian $penilaian) {
            $penilaian->subPenilaian()->delete();
        });
    }

    /**
     * Get the tipe that owns the MPenilaian
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function mTipe()
    {
        return $this->belongsTo(MTipe::class, 'id_tipe');
    }

    /**
     * Get all of the subPenilaian for the MPenilaian
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function subPenilaian()
    {
        return $this->hasMany(MSubPenilaian::class, 'id_penilaian');
    }
}

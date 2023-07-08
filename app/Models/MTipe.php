<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MTipe extends Model
{
    use SoftDeletes;

    protected $table = "m_tipe";

    protected $fillable = [
        'nama',
        'tipe',
    ];

    protected $hidden = [
        'deleted_at'
    ];

    public $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];


    public static function boot()
    {
        parent::boot();

        static::deleting(function (MTipe $tipe) {
            $tipe->penilaian()->delete();
        });
    }

    /**
     * Get all of the penilaian for the MTipe
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function penilaian()
    {
        return $this->hasMany(MPenilaian::class, 'id_tipe');
    }
}

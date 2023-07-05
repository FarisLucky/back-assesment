<?php

namespace App\Models;

use App\Models\Traits\SearchableTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MKaryawan extends Model
{
    use SoftDeletes, SearchableTrait;

    protected $table = "m_karyawan";

    public $searchable = [
        /**
         * Columns and their priority in search results.
         * Columns with higher values are more important.
         * Columns with equal values have equal importance.
         *
         * @var array
         */
        'columns' => [
            'm_karyawan.nama' => 10,
            'm_karyawan.nip' => 10,
            'm_karyawan.tgl_lahir' => 2,
            'm_karyawan.jabatan' => 3,
            'm_karyawan.alamat' => 1,
        ],
    ];

    protected $fillable = [
        'nip',
        'nama',
        'sex',
        'tgl_lahir',
        'alamat',
        'pendidikan',
        'tgl_lulus',
        'status',
        'id_jabatan',
        'id_unit',
    ];

    public $casts = [
        "tgl_lahir" => "datetime",
        "created_at" => "datetime",
        "updated_at" => "datetime",
    ];

    public function scopeWithWhereHas($query, $relation, $constraint)
    {
        return $query->whereHas($relation, $constraint)
            ->with([$relation => $constraint]);
    }

    public function jabatan()
    {
        return $this->belongsTo(MJabatan::class, 'id_jabatan');
    }

    public function unit()
    {
        return $this->belongsTo(MUnit::class, 'id_unit');
    }

    public function penilaianKaryawanKhusus()
    {
        return $this->hasMany(PenilaianKaryawan::class, 'id_karyawan')->where('tipe', MPenilaian::TIPE[1]);
    }

    public function penilaianKaryawan()
    {
        return $this->hasMany(PenilaianKaryawan::class, 'id_karyawan');
    }

    public function penilaianKaryawanUmum()
    {
        return $this->hasMany(PenilaianKaryawan::class, 'id_karyawan')->where('tipe', MPenilaian::TIPE[0]);
    }
}

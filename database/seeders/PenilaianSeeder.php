<?php

namespace Database\Seeders;

use App\Models\AnalisisSwot;
use App\Models\DetailPenilaian;
use App\Models\PenilaianKaryawan;
use App\Models\SubDetailPenilaian;
use App\Models\SubPenilaianKaryawan;
use Illuminate\Database\Seeder;

class PenilaianSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        PenilaianKaryawan::insert([
            [
                'id_karyawan' => 1,
                'nama_karyawan' => 'Salman Al Farisi',
                'id_penilai' => 3,
                'nama_penilai' => 'Choky Chandra',
                'jabatan_penilai' => 'Kepala Unit IT',
                'tgl_nilai' => now()->format('Y-m-d'),
                'ttl_nilai' => doubleval(789),
                'rata_nilai' => doubleval(79),
                'tipe' => 'pk_umum',
                'status' => 'draft',
                'validasi_by' => 3,
                'created_by' => 3,
                'updated_by' => 3,
            ],
            [
                'id_karyawan' => 1,
                'nama_karyawan' => 'Sunanto',
                'id_penilai' => 3,
                'nama_penilai' => 'Choky Chandra',
                'jabatan_penilai' => 'Kepala Unit IT',
                'tgl_nilai' => now()->format('Y-m-d'),
                'ttl_nilai' => doubleval(789),
                'rata_nilai' => doubleval(79),
                'tipe' => 'pk_umum',
                'status' => 'draft',
                'validasi_by' => 3,
                'created_by' => 3,
                'updated_by' => 3,
            ],
        ]);

        DetailPenilaian::insert([
            [
                'id_pk' => 1,
                'nama_penilaian' => 'PRESTASI KERJA DAN KETELITIAN',
                'ttl_nilai' => doubleval(270),
                'rata_nilai' => doubleval(73),
                'id_penilai' => 3,
                'nama_penilai' => 'Choky Candra',
                'jabatan_penilai' => 'Kepala Unit IT',
                'catatan' => null,
                'updated_by' => 3,
            ],
            [
                'id_pk' => 1,
                'nama_penilaian' => 'TANGGUNG JAWAB',
                'ttl_nilai' => doubleval(270),
                'rata_nilai' => doubleval(73),
                'id_penilai' => 3,
                'nama_penilai' => 'Choky Candra',
                'jabatan_penilai' => 'Kepala Unit IT',
                'catatan' => null,
                'updated_by' => 3,
            ],
            [
                'id_pk' => 1,
                'nama_penilaian' => 'PRAKARSA / INISIATIF',
                'ttl_nilai' => doubleval(270),
                'rata_nilai' => doubleval(73),
                'id_penilai' => 3,
                'nama_penilai' => 'Choky Candra',
                'jabatan_penilai' => 'Kepala Unit IT',
                'catatan' => null,
                'updated_by' => 3,
            ],
        ]);

        SubPenilaianKaryawan::insert([
            [
                "id_detail" => 1,
                "penilaian" => 'PRESTASI KERJA DAN KETELITIAN',
                "sub_penilaian" => 'Kecakapan dan bidang tugas',
                "nilai" => intval(70),
                "updated_by" => 3,
            ],
            [
                "id_detail" => 1,
                "penilaian" => 'PRESTASI KERJA DAN KETELITIAN',
                "sub_penilaian" => 'Kecakapan dan bidang tugas',
                "nilai" => intval(73),
                "updated_by" => 3,
            ],
        ]);

        AnalisisSwot::insert([
            [
                "id_pk" => 1,
                "kelebihan" => 'Kelebihann -',
                "kekurangan" => 'Kekurangan -',
                "kesempatan" => 'Kesempatan -',
                "ancaman" => 'Ancaman -',
            ],
        ]);
    }
}

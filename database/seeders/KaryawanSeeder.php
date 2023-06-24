<?php

namespace Database\Seeders;

use App\Models\MKaryawan;
use Illuminate\Database\Seeder;

class KaryawanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        MKaryawan::insert([
            [
                'nama' => 'Salman Al Farisi',
                'nip' => '1209329876542345',
                'sex' => 'L',
                'tgl_lahir' => '2000-04-06',
                'alamat' => 'Jl. Pakuniran Desa Pandean Kec. Paiton, Kab. Probolinggo',
                'pendidikan' => 'D4 Teknik Informatika',
                'tgl_lulus' => '2021-10-04',
                'status' => 'Kontrak',
                'id_jabatan' => 2,
                'id_unit' => 1,
            ],
            [
                'nama' => 'Sunanto',
                'nip' => '1209329876542345',
                'sex' => 'L',
                'tgl_lahir' => '2000-04-03',
                'alamat' => 'Jl. Pakuniran Desa Petunjungan Kec. Paiton, Kab. Probolinggo',
                'pendidikan' => 'S1 Teknik Informatika',
                'tgl_lulus' => '2020-10-04',
                'status' => 'Kontrak',
                'id_jabatan' => 2,
                'id_unit' => 1,
            ],
            [
                'nama' => 'Choky Chandra',
                'nip' => '1209329876542345',
                'sex' => 'L',
                'tgl_lahir' => '2000-04-03',
                'alamat' => 'Jl. Pakuniran Desa Petunjungan Kec. Paiton, Kab. Probolinggo',
                'pendidikan' => 'S1 Teknik Informatika',
                'tgl_lulus' => '2020-10-04',
                'status' => 'Kontrak',
                'id_jabatan' => 1,
                'id_unit' => 1,
            ],
        ]);
    }
}

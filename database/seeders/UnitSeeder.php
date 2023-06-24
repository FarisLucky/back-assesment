<?php

namespace Database\Seeders;

use App\Models\MUnit;
use Illuminate\Database\Seeder;

class UnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $units = array(
            array(
                "nama" => "Direktur",
                "id_parent" => NULL,
            ),
            array(
                "nama" => "Dokter",
                "id_parent" => 1,
            ),
            array(
                "nama" => "Komite Medis",
                "id_parent" => 1,
            ),
            array(
                "nama" => "Sub Komite Mutu Profesi (Medis)",
                "id_parent" => 3,
            ),
            array(
                "nama" => "Komite Keperawatan",
                "id_parent" => 1,
            ),
            array(
                "nama" => "Komite Komite Mutu dan Keselamatan Pasien",
                "id_parent" => 1,
            ),
            array(
                "nama" => "Komite Farmasi dan Terapi",
                "id_parent" => 1,
            ),
            array(
                "nama" => "Komite Etik dan Hukum",
                "id_parent" => 1,
            ),
            array(
                "nama" => "Komite PPI",
                "id_parent" => 1,
            ),
            array(
                "nama" => "Komite K3RS",
                "id_parent" => 1,
            ),
            array(
                "nama" => "Komite PPRA",
                "id_parent" => 1,
            ),
            array(
                "nama" => "Case Manager",
                "id_parent" => 1,
            ),
            array(
                "nama" => "Bidang Pelayanan",
                "id_parent" => 1,
            ),
            array(
                "nama" => "Kelompok Staf Medis",
                "id_parent" => 13,
            ),
            array(
                "nama" => "Seksi Pelayanan Medis",
                "id_parent" => 13,
            ),
            array(
                "nama" => "Unit Kamar Operasi",
                "id_parent" => 15,
            ),
            array(
                "nama" => "Unit Maternal",
                "id_parent" => 15,
            ),
            array(
                "nama" => "Unit Kamar Bersalin",
                "id_parent" => 15,
            ),
            array(
                "nama" => "Neonatologi",
                "id_parent" => 15,
            ),
            array(
                "nama" => "ICU",
                "id_parent" => 15,
            ),
            array(
                "nama" => "Unit Rawat Inap General",
                "id_parent" => 15,
            ),
            array(
                "nama" => "Unit Rawat Jalan",
                "id_parent" => 15,
            ),
            array(
                "nama" => "Paviliun",
                "id_parent" => 15,
            ),
            array(
                "nama" => "Anak",
                "id_parent" => 15,
            ),
            array(
                "nama" => "Instalasi Gawat Darurat",
                "id_parent" => 13,
            ),
            array(
                "nama" => "Instalasi Laboratorium",
                "id_parent" => 13,
            ),
            array(
                "nama" => "Seksi Penunjang Medis",
                "id_parent" => 13,
            ),
            array(
                "nama" => "Unit Radiologi",
                "id_parent" => 27,
            ),
            array(
                "nama" => "Unit Gizi",
                "id_parent" => 27,
            ),
            array(
                "nama" => "Unit Rekam Medis",
                "id_parent" => 27,
            ),
            array(
                "nama" => "Unit Sterilisasi & Linen",
                "id_parent" => 27,
            ),
            array(
                "nama" => "Instalasi Farmasi",
                "id_parent" => 13,
            ),
            array(
                "nama" => "Bidang Keperawatan",
                "id_parent" => 1,
            ),
            array(
                "nama" => "Bagian Umum & Keuangan",
                "id_parent" => 1,
            ),
            array(
                "nama" => "Sub Bagian Umum",
                "id_parent" => 34,
            ),
            array(
                "nama" => "Unit SDM",
                "id_parent" => 35,
            ),
            array(
                "nama" => "Unit Rumah Tangga",
                "id_parent" => 35,
            ),
            array(
                "nama" => "Unit TPP dan Informasi",
                "id_parent" => 35,
            ),
            array(
                "nama" => "Unit Tata Usaha",
                "id_parent" => 35,
            ),
            array(
                "nama" => "Unit IT",
                "id_parent" => 35,
            ),
            array(
                "nama" => "Sub Bagian Keuangan",
                "id_parent" => 34,
            ),
            array(
                "nama" => "Unit Pembendaharaan, Akutansi, dan Pajak",
                "id_parent" => 42,
            ),
            array(
                "nama" => "Unit Asuransi Kesehatan",
                "id_parent" => 42,
            ),
            array(
                "nama" => "Unit Pengadaan",
                "id_parent" => 42,
            ),
            array(
                "nama" => "General",
                "id_parent" => 1,
            ),
            array(
                "nama" => "IGD",
                "id_parent" => 1,
            ),
        );

        MUnit::insert($units);
    }
}

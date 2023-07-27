<?php

namespace Database\Seeders;

use App\Models\MJabatan;
use App\Models\MKaryawan;
use App\Models\MPenilaian;
use App\Models\MSubPenilaian;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // $penilaian = MPenilaian::whereNotNull('deleted_at');
        // $penilaian->forceDelete();
        // $penilaian->each(function ($nilai) {
        //     $nilai->sub()->delete();
        //     $nilai->save();
        // });

        // \App\Models\User::create([
        //     'name' => 'Salman Al Farisi',
        //     'email' => 'salman@gmail.com',
        //     'password' => bcrypt('123')
        // ]);

        // $this->call([
        //     // UnitSeeder::class,
        //     // JabatanSeeder::class,
        //     // KaryawanSeeder::class,
        //     // PenilaianSeeder::class,
        // ]);

        // MJabatan::query()->update(['kategori' => 1]);
        $karyawans = MKaryawan::all();
        $users = [];
        $pass = bcrypt('HDS-123');
        $karyawans->each(function ($karyawan) use (&$users, $pass) {
            $users[] = [
                'id_karyawan' => $karyawan->id,
                'name' => $karyawan->nama,
                'email' => $karyawan->nip,
                'password' => $pass,
                'role' => 'USER',
            ];
        });
        User::insert($users);
    }
}

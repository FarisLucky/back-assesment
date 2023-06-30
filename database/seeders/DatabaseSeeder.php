<?php

namespace Database\Seeders;

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
        // \App\Models\User::create([
        //     'name' => 'Salman Al Farisi',
        //     'email' => 'salman@gmail.com',
        //     'password' => bcrypt('123')
        // ]);

        $this->call([
            // UnitSeeder::class,
            // JabatanSeeder::class,
            KaryawanSeeder::class,
            // PenilaianSeeder::class,
        ]);
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterMPenilaianTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('m_penilaian', function (Blueprint $table) {
            $table->string('bobot', 3)->nullable();
        });

        Schema::table('m_sub_penilaian', function (Blueprint $table) {
            $table->char('kategori', 1)
                ->comment('1 = medis, 0 = non medis');
        });

        Schema::table('m_jabatan', function (Blueprint $table) {
            $table->char('kategori', 1)
                ->comment('1 = medis, 0 = non medis');
        });

        Schema::table('penilaian_karyawan', function (Blueprint $table) {
            $table->char('kategori', 1)
                ->comment('1 = medis, 0 = non medis');
        });

        Schema::create('m_valid_penilai', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_sub');
            $table->unsignedBigInteger('id_jabatan_penilai');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}

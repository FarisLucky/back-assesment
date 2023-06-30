<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterPenilaianTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('m_penilaian', function (Blueprint $table) {
            $table->unsignedSmallInteger('id_tipe')->nullable()->after('level');
        });

        Schema::table('tipe_penilaian', function (Blueprint $table) {
            $table->string('nama_penilai')->nullable()->after('id_karyawan');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('id_karyawan')->nullable()->after('id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('m_penilaian', function (Blueprint $table) {
            $table->dropColumn('id_tipe');
        });

        Schema::table('tipe_penilaian', function (Blueprint $table) {
            $table->dropColumn('nama_penilai');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('id_karyawan');
        });
    }
}

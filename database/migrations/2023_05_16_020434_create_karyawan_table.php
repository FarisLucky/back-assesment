<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKaryawanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('m_karyawan', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 150)->nullable(false);
            $table->string('nip', 20)->nullable(true);
            $table->char('sex', 1)->nullable(false);
            $table->date('tgl_lahir')->nullable(false);
            $table->string('alamat', 165)->nullable(true);
            $table->string('pendidikan', 165)->nullable(true);
            $table->date('tgl_lulus')->nullable();
            $table->string('status', 30)->nullable();
            $table->unsignedSmallInteger('id_jabatan')->nullable(false);
            $table->unsignedSmallInteger('id_unit')->nullable(false);
            $table->softDeletes();
            $table->timestamps();
        });

        Schema::table('m_karyawan', function (Blueprint $table) {
            $table->foreign('id_jabatan')
                ->references('id')
                ->on('m_jabatan')
                ->cascadeOnUpdate();
            $table->foreign('id_unit')
                ->references('id')
                ->on('m_unit')
                ->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('karyawan');
    }
}

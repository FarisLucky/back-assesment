<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMastersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('m_unit', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->string('nama')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('m_jabatan', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->string('nama')->nullable();
            $table->string('level', 3)->nullable();
            $table->unsignedSmallInteger('id_parent')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('m_penilaian', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->string('nama', 100)
                ->nullable();
            $table->string('tipe', 20)
                ->default('pk_umum')
                ->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('m_sub_penilaian', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('id_penilaian');
            $table->string('nama', 150)->nullable();
            $table->unsignedSmallInteger('id_jabatan_penilai')->nullable();
            $table->unsignedSmallInteger('id_jabatan_kinerja')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('m_sub_penilaian', function (Blueprint $table) {
            $table->foreign('id_penilaian')
                ->references('id')
                ->on('m_penilaian')
                ->restrictOnUpdate();
            $table->foreign('id_jabatan_penilai')
                ->references('id')
                ->on('m_jabatan')
                ->restrictOnUpdate();
            $table->foreign('id_jabatan_kinerja')
                ->references('id')
                ->on('m_jabatan')
                ->restrictOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('m_unit');
        Schema::dropIfExists('m_jabatan');
        Schema::dropIfExists('m_penilaian');
        Schema::dropIfExists('m_sub_penilaian');
    }
}

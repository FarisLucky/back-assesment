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
            // $table->string('type', 10)->nullable();
            $table->unsignedSmallInteger('id_jabatan_penilai')->nullable();
            $table->unsignedSmallInteger('id_jabatan_kinerja')->nullable();
            $table->unsignedSmallInteger('id_unit_penilai')->nullable();
            $table->unsignedBigInteger('id_parent')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('m_penilaian', function (Blueprint $table) {
            $table->foreign('id_jabatan_penilai')
                ->references('id')
                ->on('m_jabatan')
                ->restrictOnUpdate();
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
            $table->foreign('id_unit_penilai')
                ->references('id')
                ->on('m_unit')
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
        Schema::dropIfExists('unit');
        Schema::dropIfExists('jabatan');
        Schema::dropIfExists('penilaian');
        Schema::dropIfExists('sub_penilaian');
        Schema::dropIfExists('kategori_penilaian');
        Schema::dropIfExists('detail_kategori');
    }
}

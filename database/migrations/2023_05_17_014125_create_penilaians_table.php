<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePenilaiansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('penilaian_karyawan', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_karyawan');
            $table->string('nama_karyawan', 150)->nullable();
            $table->string('jabatan', 250)->nullable();
            $table->unsignedBigInteger('id_penilai');
            $table->string('nama_penilai', 250)->nullable();
            $table->string('jabatan_penilai', 250)->nullable();
            $table->date('tgl_nilai', 250)->nullable();
            $table->double('ttl_nilai')->nullable();
            $table->double('rata_nilai')->nullable();
            $table->string('tipe', 15)
                ->default('pk_umum')
                ->nullable();
            $table->string('status')->nullable(); // draft, tersimpan, validasi
            $table->unsignedBigInteger('validasi_by');
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('updated_by');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('tipe_penilaian', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_detail')->nullable();
            $table->unsignedBigInteger('id_pk')->nullable();
            $table->unsignedBigInteger('id_karyawan')->nullable();
            $table->string('nama_tipe')->nullable();
            $table->string('tipe_pk', 20)->nullable();
            $table->string('catatan', 250)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('detail_penilaian', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_pk')->nullable();
            $table->unsignedBigInteger('id_tipe_pk')->nullable();
            $table->string('nama_penilaian', 250)->nullable();
            $table->integer('ttl_nilai')->nullable();
            $table->integer('rata_nilai')->nullable();
            $table->unsignedBigInteger('id_penilai');
            $table->string('nama_penilai', 250)->nullable();
            $table->string('jabatan_penilai', 250)->nullable();
            $table->unsignedBigInteger('updated_by');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('sub_detail_penilaian', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_detail')->nullable();
            $table->string('penilaian', 250)->nullable();
            $table->string('sub_penilaian', 150)->nullable();
            $table->smallInteger('nilai')->nullable();
            // $table->string('tipe_penilaian', 10)->nullable();
            $table->unsignedBigInteger('updated_by');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('analisis_swot', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_pk')->nullable();
            $table->string('kelebihan', 250)->nullable();
            $table->string('kekurangan', 250)->nullable();
            $table->string('kesempatan', 250)->nullable();
            $table->string('ancaman', 250)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::table('detail_penilaian', function (Blueprint $table) {
            $table->foreign('id_tipe_pk')
                ->references('id')
                ->on('tipe_penilaian')
                ->restrictOnUpdate();
        });

        Schema::table('sub_detail_penilaian', function (Blueprint $table) {
            $table->foreign('id_detail')
                ->references('id')
                ->on('detail_penilaian')
                ->restrictOnUpdate();
        });
        Schema::table('analisis_swot', function (Blueprint $table) {
            $table->foreign('id_pk')
                ->references('id')
                ->on('penilaian_karyawan')
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
        Schema::dropIfExists('penilaian_karyawan');
        Schema::dropIfExists('detail_penilaian');
        Schema::dropIfExists('sub_detail_penilaian');
        Schema::dropIfExists('analisis_swot');
    }
}

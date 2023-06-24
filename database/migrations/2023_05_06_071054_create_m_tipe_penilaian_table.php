<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMTipePenilaianTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('m_tipe', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->string('nama')
                ->nullable();
            $table->string('tipe', 20)
                ->default('pk_umum')
                ->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('m_tipe_penilaian', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('id_tipe')->nullable();
            $table->unsignedSmallInteger('id_jabatan')->nullable();
            $table->unsignedSmallInteger('id_penilaian')->nullable();
            $table->string('tipe', 20)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('m_tipe_penilaian');
        Schema::dropIfExists('tipe_penilaian');
    }
}

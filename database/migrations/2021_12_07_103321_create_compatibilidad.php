<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCompatibilidad extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('compatibilidad', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_usuario_o');
            $table->foreign('id_usuario_o')->references('id')->on('usuarios')->onDelete('cascade');
            $table->unsignedBigInteger('id_usuario_d');
            $table->foreign('id_usuario_d')->references('id')->on('usuarios')->onDelete('cascade');
            $table->integer('porcentaje');
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
        Schema::dropIfExists('compatibilidad');
    }
}

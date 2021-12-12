<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsuarios extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('usuarios', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('apellidos');
            $table->string('correo')->unique( );
            $table->string('pass');
            $table->unsignedBigInteger('id_genero');
            $table->foreign('id_genero')->references('id')->on('generos')->onDelete('cascade');
            $table->date('fecha_nacimiento');
            $table->string('ciudad');
            $table->string('descripcion');
            $table->string('foto');
            $table->string('hijos')->nullable();
            $table->integer('conectado');
            $table->integer('activo');
            $table->string('tema');
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
        Schema::dropIfExists('usuarios');
    }
}

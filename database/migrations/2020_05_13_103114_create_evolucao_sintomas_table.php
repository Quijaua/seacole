<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEvolucaoSintomasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('evolucao_sintomas', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('paciente_id')->unsigned();
            $table->foreign('paciente_id')->references('id')->on('pacientes')->onDelete('cascade');
            $table->date('data_inicio_sintoma')->nullable();
            $table->string('sintoma_manifestado')->nullable();
            $table->decimal('febre_temperatura_maxima')->nullable();
            $table->date('data_medicao_temperatura')->nullable();
            $table->decimal('temperatura_atual')->nullable();
            $table->integer('cansaco_saturacao')->nullable();
            $table->integer('cansaco_frequencia_respiratoria')->nullable();
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
        Schema::dropIfExists('evolucao_sintomas');
    }
}

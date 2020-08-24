<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMonitoramentosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('monitoramentos', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('paciente_id')->unsigned();
            $table->foreign('paciente_id')->references('id')->on('pacientes')->onDelete('cascade');
            $table->string('horario_monotiramento')->nullable();
            $table->string('sintomas_atuais')->nullable();
            $table->string('temperatura_atual')->nullable();
            $table->string('saturacao_atual')->nullable();
            $table->string('frequencia_respiratoria_atual')->nullable();
            $table->string('frequencia_cardiaca_atual')->nullable();
            $table->string('pressao_arterial_atual')->nullable();
            $table->string('medicamento')->nullable();
            $table->string('algum_sinal')->nullable();
            $table->string('equipe_medica')->nullable();
            $table->string('fazendo_uso_pic')->nullable();
            $table->string('fez_escalapes')->nullable();
            $table->string('melhora_sintoma_escaldapes')->nullable();
            $table->string('fes_inalacao')->nullable();
            $table->string('melhoria_sintomas_inalacao')->nullable();
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
        Schema::dropIfExists('monitoramentos');
    }
}

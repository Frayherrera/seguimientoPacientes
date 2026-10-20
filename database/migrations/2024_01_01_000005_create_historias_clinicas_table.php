<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('historias_clinicas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('paciente_id')->constrained()->onDelete('cascade');
            $table->foreignId('medico_id')->constrained('users');
            $table->date('fecha_atencion');
            $table->time('hora_atencion');
            $table->string('tipo_atencion', 50); // Control, Urgencias, Primera vez
            $table->text('motivo_consulta');
            $table->text('enfermedad_actual')->nullable();
            $table->text('antecedentes')->nullable();
            $table->text('examen_fisico')->nullable();
            $table->text('impresion_diagnostica');
            $table->text('plan_manipulacion')->nullable();
            $table->text('indicaciones')->nullable();
            $table->enum('estado', ['Abierta', 'Cerrada', 'En seguimiento'])->default('Abierta');
            $table->timestamps();

            $table->index(['paciente_id', 'fecha_atencion'], 'idx_hc_paciente_fecha');
            $table->index('medico_id', 'idx_hc_medico');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('historias_clinicas');
    }
};

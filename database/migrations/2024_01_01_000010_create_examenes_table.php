<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('examenes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('historia_clinica_id')->constrained('historias_clinicas')->onDelete('cascade');
            $table->foreignId('paciente_id')->constrained()->onDelete('cascade');
            $table->string('tipo_examen', 100); // Laboratorio, Imagenología, etc.
            $table->string('nombre_examen', 200);
            $table->text('descripcion')->nullable();
            $table->date('fecha_solicitud');
            $table->date('fecha_realizacion')->nullable();
            $table->string('laboratorio', 200)->nullable();
            $table->enum('estado', ['Solicitado', 'En Proceso', 'Completado', 'Cancelado'])->default('Solicitado');
            $table->text('resultados')->nullable();
            $table->text('observaciones_resultado')->nullable();
            $table->string('archivo_resultado', 500)->nullable(); // Ruta a PDF/imagen
            $table->timestamps();

            $table->index(['paciente_id', 'estado'], 'idx_exam_paciente_estado');
            $table->index('fecha_solicitud', 'idx_exam_fecha');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('examenes');
    }
};

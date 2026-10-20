<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prescripciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('historia_clinica_id')->constrained('historias_clinicas')->onDelete('cascade');
            $table->foreignId('paciente_id')->constrained()->onDelete('cascade');
            $table->foreignId('medicamento_id')->constrained('medicamentos');
            $table->string('dosis', 100); // 500mg, 1 tableta, etc.
            $table->string('frecuencia', 50); // Cada 8 horas, 2 veces al día, etc.
            $table->string('via_administracion', 50); // Oral, IV, IM, etc.
            $table->integer('duracion_dias')->nullable();
            $table->text('indicaciones_especiales')->nullable();
            $table->date('fecha_inicio');
            $table->date('fecha_fin')->nullable();
            $table->boolean('es_activo')->default(true);
            $table->timestamps();

            $table->index(['paciente_id', 'es_activo'], 'idx_presc_paciente_activo');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prescripciones');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('paciente_enfermedades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('paciente_id')->constrained()->onDelete('cascade');
            $table->foreignId('tipo_enfermedad_id')->constrained('tipos_enfermedades');
            $table->date('fecha_diagnostico');
            $table->enum('estado', ['Activo', 'En Remisión', 'Controlado'])->default('Activo');
            $table->text('observaciones')->nullable();
            $table->boolean('es_principal')->default(false); // Enfermedad principal
            $table->timestamps();

            // Un paciente no puede tener la misma enfermedad registrada dos veces
            $table->unique(['paciente_id', 'tipo_enfermedad_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('paciente_enfermedades');
    }
};

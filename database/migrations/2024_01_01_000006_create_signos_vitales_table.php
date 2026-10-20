<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('signos_vitales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('historia_clinica_id')->constrained('historias_clinicas')->onDelete('cascade');
            $table->foreignId('paciente_id')->constrained()->onDelete('cascade');
            $table->decimal('presion_sistolica', 5, 2)->nullable(); // mmHg
            $table->decimal('presion_diastolica', 5, 2)->nullable(); // mmHg
            $table->decimal('frecuencia_cardiaca', 5, 2)->nullable(); // lpm
            $table->decimal('frecuencia_respiratoria', 5, 2)->nullable(); // rpm
            $table->decimal('temperatura', 5, 2)->nullable(); // °C
            $table->decimal('peso', 6, 2)->nullable(); // kg
            $table->decimal('talla', 5, 2)->nullable(); // cm
            $table->decimal('imc', 5, 2)->nullable(); // Índice de masa corporal
            $table->decimal('glucosa', 6, 2)->nullable(); // mg/dL
            $table->decimal('saturacion_oxigeno', 5, 2)->nullable(); // %
            $table->timestamps();

            $table->index(['paciente_id', 'created_at'], 'idx_sv_paciente_fecha');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('signos_vitales');
    }
};

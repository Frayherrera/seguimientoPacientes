<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('configuracion_alertas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tipo_enfermedad_id')->constrained('tipos_enfermedades');
            $table->string('nombre_parametro', 100); // glucosa, presion_sistolica, etc.
            $table->decimal('valor_minimo', 10, 2)->nullable();
            $table->decimal('valor_maximo', 10, 2)->nullable();
            $table->string('unidad', 20); // mg/dL, mmHg, etc.
            $table->enum('nivel_alerta', ['Bajo', 'Medio', 'Alto'])->default('Medio');
            $table->text('mensaje_alerta')->nullable();
            $table->boolean('es_activo')->default(true);
            $table->timestamps();

            $table->unique(['tipo_enfermedad_id', 'nombre_parametro', 'nivel_alerta'], 'uniq_config_alerta');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('configuracion_alertas');
    }
};

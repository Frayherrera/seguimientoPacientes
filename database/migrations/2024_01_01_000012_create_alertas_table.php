<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('alertas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('paciente_id')->constrained()->onDelete('cascade');
            $table->foreignId('signo_vital_id')->nullable()->constrained('signos_vitales')->nullOnDelete();
            $table->foreignId('historia_clinica_id')->nullable()->constrained('historias_clinicas')->nullOnDelete();
            $table->string('tipo_alerta', 50); // Valor crítico, Cita vencida, etc.
            $table->string('parametro', 100); // Parámetro que generó la alerta
            $table->decimal('valor_detectado', 10, 2)->nullable();
            $table->decimal('valor_referencia_min', 10, 2)->nullable();
            $table->decimal('valor_referencia_max', 10, 2)->nullable();
            $table->enum('nivel_severidad', ['Info', 'Advertencia', 'Crítico'])->default('Advertencia');
            $table->text('mensaje');
            $table->boolean('es_leida')->default(false);
            $table->timestamp('fecha_lectura')->nullable();
            $table->foreignId('usuario_lectura_id')->nullable()->constrained('users');
            $table->text('observaciones_atencion')->nullable();
            $table->timestamps();

            $table->index(['paciente_id', 'es_leida'], 'idx_alerta_paciente_leida');
            $table->index('nivel_severidad', 'idx_alerta_severidad');
            $table->index('created_at', 'idx_alerta_fecha');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alertas');
    }
};

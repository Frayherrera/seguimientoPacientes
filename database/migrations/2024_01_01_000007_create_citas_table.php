<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('citas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('paciente_id')->constrained()->onDelete('cascade');
            $table->foreignId('medico_id')->constrained('users');
            $table->date('fecha_cita');
            $table->time('hora_inicio');
            $table->time('hora_fin')->nullable();
            $table->string('tipo_cita', 50); // Control, Urgencia, Primera vez, Teleconsulta
            $table->string('consultorio', 20)->nullable();
            $table->enum('estado', ['Programada', 'Confirmada', 'Atendida', 'Cancelada', 'No Presentado'])->default('Programada');
            $table->text('motivo')->nullable();
            $table->text('observaciones')->nullable();
            $table->boolean('recordatorio_enviado')->default(false);
            $table->timestamp('fecha_recordatorio')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['fecha_cita', 'estado'], 'idx_cita_fecha_estado');
            $table->index('medico_id', 'idx_cita_medico');
            $table->index('paciente_id', 'idx_cita_paciente');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('citas');
    }
};

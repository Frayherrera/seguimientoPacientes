<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pacientes', function (Blueprint $table) {
            $table->id();
            $table->string('documento_identidad', 20)->unique();
            $table->string('tipo_documento', 10)->default('CC'); // CC, CE, TI, PAS
            $table->string('nombres', 100);
            $table->string('apellidos', 100);
            $table->date('fecha_nacimiento');
            $table->enum('genero', ['M', 'F', 'Otro']);
            $table->string('telefono', 20)->nullable();
            $table->string('celular', 20)->nullable();
            $table->string('email', 150)->nullable();
            $table->text('direccion')->nullable();
            $table->string('ciudad', 100)->nullable();
            $table->string('departamento', 100)->nullable();
            $table->string('eps', 150)->nullable(); // Entidad Promotora de Salud
            $table->string('ips_primaria', 150)->nullable(); // IPS de atención primaria
            $table->enum('estado', ['Activo', 'Inactivo', 'Fallecido'])->default('Activo');
            $table->text('observaciones')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Índices para búsquedas frecuentes
            $table->index('apellidos', 'idx_paciente_apellidos');
            $table->index('estado', 'idx_paciente_estado');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pacientes');
    }
};

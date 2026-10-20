<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tipos_enfermedades', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100)->unique(); // Diabetes, Hipertensión, etc.
            $table->text('descripcion')->nullable();
            $table->string('codigo_cie10', 10)->nullable(); // Código CIE-10
            $table->json('parametros_control')->nullable(); // Parámetros a monitorear
            $table->boolean('es_activo')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tipos_enfermedades');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('medicamentos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 200);
            $table->string('principio_activo', 200)->nullable();
            $table->string('forma_farmaceutica', 50); // Tableta, Cápsula, Solución, etc.
            $table->string('concentracion', 50)->nullable();
            $table->string('laboratorio', 150)->nullable();
            $table->boolean('es_controlado')->default(false);
            $table->boolean('es_activo')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index('nombre', 'idx_medicamento_nombre');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('medicamentos');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->string('accion', 50); // Create, Read, Update, Delete
            $table->string('tabla_afectada', 100);
            $table->unsignedBigInteger('registro_id')->nullable();
            $table->json('datos_anteriores')->nullable();
            $table->json('datos_nuevos')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent', 500)->nullable();
            $table->timestamps();

            $table->index(['tabla_afectada', 'registro_id'], 'idx_audit_tabla_registro');
            $table->index('user_id', 'idx_audit_usuario');
            $table->index('created_at', 'idx_audit_fecha');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};

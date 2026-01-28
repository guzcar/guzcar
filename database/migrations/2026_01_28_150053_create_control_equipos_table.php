<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // 1. Cabecera Control
        Schema::create('control_equipos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('equipo_id');
            $table->dateTime('fecha');
            $table->unsignedBigInteger('responsable_id');
            $table->unsignedBigInteger('propietario_id')->nullable();
            $table->string('evidencia_url')->nullable();

            $table->index('equipo_id', 'idx_ce_equipo');
            $table->index('responsable_id', 'idx_ce_responsable');
            $table->index('propietario_id', 'idx_ce_propietario');

            $table->foreign('equipo_id')->references('id')->on('equipos')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreign('responsable_id')->references('id')->on('users')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreign('propietario_id')->references('id')->on('users')->cascadeOnUpdate()->restrictOnDelete();
            $table->timestamps();
        });

        // 2. Detalle Control
        Schema::create('control_equipo_detalles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('control_equipo_id');
            $table->unsignedBigInteger('equipo_detalle_id');
            $table->unsignedBigInteger('implemento_id');
            $table->enum('estado', ['OPERATIVO', 'MERMA', 'PERDIDO'])->default('OPERATIVO');
            $table->text('observacion')->nullable();
            $table->enum('prev_estado', ['OPERATIVO', 'MERMA', 'PERDIDO'])->nullable();
            $table->dateTime('prev_deleted_at')->nullable();

            $table->unique(['control_equipo_id', 'equipo_detalle_id'], 'uk_ced_control_detalle');
            
            $table->foreign('control_equipo_id')->references('id')->on('control_equipos')->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreign('equipo_detalle_id')->references('id')->on('equipo_detalles')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreign('implemento_id')->references('id')->on('implementos')->cascadeOnUpdate()->restrictOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('control_equipo_detalles');
        Schema::dropIfExists('control_equipos');
    }
};
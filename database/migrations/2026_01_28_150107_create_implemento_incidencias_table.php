<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('implemento_incidencias', function (Blueprint $table) {
            $table->id();
            $table->dateTime('fecha');
            $table->enum('tipo_origen', ['EQUIPO', 'STOCK']); // Cambiado MALETA por EQUIPO
            $table->unsignedBigInteger('equipo_detalle_id')->nullable();
            $table->unsignedBigInteger('implemento_id');
            $table->unsignedInteger('cantidad')->default(1);
            $table->unsignedBigInteger('propietario_id')->nullable();
            $table->unsignedBigInteger('responsable_id');
            $table->enum('motivo', ['MERMA', 'PERDIDO']);
            $table->enum('prev_estado', ['OPERATIVO', 'MERMA', 'PERDIDO'])->nullable();
            $table->dateTime('prev_deleted_at')->nullable();
            $table->text('observacion')->nullable();

            $table->foreign('equipo_detalle_id')->references('id')->on('equipo_detalles')->restrictOnDelete();
            $table->foreign('implemento_id')->references('id')->on('implementos')->restrictOnDelete();
            $table->foreign('propietario_id')->references('id')->on('users')->restrictOnDelete();
            $table->foreign('responsable_id')->references('id')->on('users')->restrictOnDelete();

            $table->timestamps();
        });

        DB::statement('
            ALTER TABLE implemento_incidencias 
            ADD CONSTRAINT chk_ii_coherencia CHECK (
                (tipo_origen = "EQUIPO" AND cantidad = 1) OR
                (tipo_origen = "STOCK" AND cantidad > 0)
            )
        ');
    }

    public function down(): void
    {
        Schema::dropIfExists('implemento_incidencias');
    }
};
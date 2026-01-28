<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('equipo_detalles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('equipo_id');
            $table->unsignedBigInteger('implemento_id');
            $table->enum('ultimo_estado', ['OPERATIVO', 'MERMA', 'PERDIDO'])->nullable();
            $table->string('evidencia_url')->nullable();

            $table->index('equipo_id', 'idx_ed_equipo');
            $table->index('implemento_id', 'idx_ed_implemento');

            $table->foreign('equipo_id')
                ->references('id')->on('equipos')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->foreign('implemento_id')
                ->references('id')->on('implementos')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('equipo_detalles');
    }
};
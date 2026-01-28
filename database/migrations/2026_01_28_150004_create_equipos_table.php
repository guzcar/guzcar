<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('equipos', function (Blueprint $table) {
            $table->id();
            $table->string('codigo')->unique();
            $table->unsignedBigInteger('propietario_id')->nullable();
            
            // Constraint nombre único
            $table->unique('codigo', 'uk_equipos_codigo');
            
            $table->foreign('propietario_id')
                ->references('id')->on('users')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
                
            $table->string('evidencia')->nullable();
            $table->text('observacion')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('equipos');
    }
};
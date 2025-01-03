<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('trabajos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehiculo_id')
                ->constrained('vehiculos')
                ->onDelete('restrict')
                ->onUpdate('cascade');
            $table->foreignId('taller_id')
                ->constrained('talleres')
                ->onDelete('restrict')
                ->onUpdate('cascade');
            $table->date('fecha_ingreso');
            $table->date('fecha_salida')->nullable();
            $table->text('descripcion_servicio');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trabajos');
    }
};

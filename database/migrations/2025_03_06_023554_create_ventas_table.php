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
        Schema::create('ventas', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 20)->unique();
            $table->date('fecha');
            $table->time('hora');
            $table->text('observacion')->nullable();
            $table->foreignId('responsable_id')
                ->constrained('users')
                ->onDelete('restrict')
                ->onUpdate('cascade');
            $table->foreignId('cliente_id')
                ->constrained('clientes')
                ->onDelete('restrict')
                ->onUpdate('cascade');
            $table->foreignId('vehiculo_id')
                ->nullable()
                ->constrained('vehiculos')
                ->onDelete('restrict')
                ->onUpdate('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ventas');
    }
};

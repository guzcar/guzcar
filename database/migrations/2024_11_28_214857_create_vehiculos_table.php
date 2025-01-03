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
        Schema::create('vehiculos', function (Blueprint $table) {
            $table->id();
            $table->string('placa', 7)->unique()->nullable();
            $table->string('marca');
            $table->string('modelo')->nullable();
            $table->string('color');
            $table->foreignId('tipo_vehiculo_id')
                ->constrained('tipo_vehiculos')
                ->onDelete('restrict')
                ->onUpdate('cascade');
            $table->timestamps();
            $table->softDeletes();
        });

        DB::statement("
            ALTER TABLE vehiculos
            ADD COLUMN nombre_completo VARCHAR(255)
            GENERATED ALWAYS AS (
                CONCAT(
                    IFNULL(placa, '1'), ' - ',
                    marca, ' ',
                    modelo
                )
            ) VIRTUAL;
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehiculos');
    }
};

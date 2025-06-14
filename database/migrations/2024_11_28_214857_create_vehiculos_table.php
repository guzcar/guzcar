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
            // $table->string('marca');
            // $table->string('modelo')->nullable();
            $table->foreignId('marca_id')
                ->nullable()
                ->constrained('vehiculo_marcas')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->foreignId('modelo_id')
                ->nullable()
                ->constrained('vehiculo_modelos')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->string('color');
            $table->string('vin')->nullable();
            $table->string('motor')->nullable();
            $table->unsignedSmallInteger('ano')->nullable();
            $table->foreignId('tipo_vehiculo_id')
                ->constrained('tipo_vehiculos')
                ->onDelete('restrict')
                ->onUpdate('cascade');
            $table->timestamps();
            $table->softDeletes();
        });

        // DB::statement("
        //     ALTER TABLE vehiculos
        //     ADD COLUMN nombre_completo VARCHAR(255)
        //     GENERATED ALWAYS AS (
        //         CONCAT(
        //             IFNULL(placa, 'Sin Placa'), ' - ',
        //             marca, ' ',
        //             modelo
        //         )
        //     ) VIRTUAL;
        // ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehiculos');
    }
};

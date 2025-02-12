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
            $table->string('codigo', 14);
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
            $table->enum('desembolso', ['A CUENTA', 'COBRADO', 'POR COBRAR'])->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        DB::statement("
            CREATE TRIGGER generar_codigo_trabajo
                BEFORE INSERT ON trabajos
                FOR EACH ROW
                BEGIN
                    DECLARE placa_vehiculo VARCHAR(7);
                    SELECT placa INTO placa_vehiculo
                    FROM vehiculos
                    WHERE id = NEW.vehiculo_id;
                    IF placa_vehiculo IS NULL THEN
                        SET placa_vehiculo = 'SINPLACA';
                    END IF;
                    SET NEW.codigo = CONCAT(DATE_FORMAT(NEW.fecha_ingreso, '%y%m%d'), '-', placa_vehiculo);
                END;
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trabajos');
    }
};

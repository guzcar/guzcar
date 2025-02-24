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
            $table->boolean('control')->default(false);
            $table->string('codigo', 16)->unique();
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
            $table->decimal('importe')->default(0);
            $table->decimal('a_cuenta')->default(0);
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
                DECLARE fecha_base VARCHAR(6);
                DECLARE hora_base INT;
                DECLARE hora_actual INT;
                DECLARE codigo_aleatorio VARCHAR(7);
                DECLARE contador INT DEFAULT 0;

                -- Obtener la placa del vehículo
                SELECT placa INTO placa_vehiculo
                FROM vehiculos
                WHERE id = NEW.vehiculo_id;

                -- Si no hay placa, generar un código aleatorio de 7 caracteres en mayúsculas
                IF placa_vehiculo IS NULL THEN
                    SET codigo_aleatorio = UPPER(SUBSTRING(MD5(RAND()), 1, 7)); -- Código aleatorio de 7 caracteres en mayúsculas
                    SET placa_vehiculo = codigo_aleatorio;
                END IF;

                -- Obtener la fecha base (AAMMDD)
                SET fecha_base = DATE_FORMAT(NOW(), '%y%m%d'); -- Formato: Año (2 dígitos), Mes, Día

                -- Obtener la hora actual (HH)
                SET hora_base = DATE_FORMAT(NOW(), '%H'); -- Hora actual (0-23)
                SET hora_actual = hora_base; -- Inicia con la hora actual

                -- Buscar la primera hora disponible, iniciando en la hora actual
                WHILE EXISTS (
                    SELECT 1 FROM trabajos 
                    WHERE codigo = CONCAT(fecha_base, LPAD(hora_actual, 2, '0'), '-', placa_vehiculo)
                    COLLATE utf8mb4_unicode_ci
                ) DO
                    -- Aumentar la hora en 1, sin salirse de 2 dígitos (00-99)
                    SET hora_actual = (hora_actual + 1) % 100; -- Si llega a 99, vuelve a 00
                    SET contador = contador + 1;

                    -- Si ya se probaron todas las horas (100 intentos), salir del bucle
                    IF contador >= 100 THEN
                        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'No se pudo generar un código único.';
                    END IF;
                END WHILE;

                -- Asignar el código final en el formato AAMMDDCC-PLA-CAS
                SET NEW.codigo = CONCAT(fecha_base, LPAD(hora_actual, 2, '0'), '-', placa_vehiculo);
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

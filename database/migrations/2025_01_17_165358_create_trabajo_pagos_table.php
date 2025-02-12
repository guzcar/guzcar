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
        Schema::create('trabajo_pagos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trabajo_id')
                ->constrained('trabajos')
                ->onDelete('restrict')
                ->onUpdate('cascade');
            $table->decimal('monto');
            $table->date('fecha_pago');
            $table->foreignId('detalle_id')
                ->constrained('trabajo_pago_detalles')
                ->onDelete('restrict')
                ->onUpdate('cascade');
            $table->string('observacion')->nullable();
            $table->timestamps();
        });

        DB::statement("
            CREATE TRIGGER actualizar_desembolso_after_insert_pagos
                AFTER INSERT
                ON trabajo_pagos
                FOR EACH ROW
                BEGIN
                    DECLARE total_importe DECIMAL(10, 2);
                    DECLARE total_pagos DECIMAL(10, 2);

                    -- Calcular el importe total (precio * cantidad de servicios)
                    SELECT COALESCE(SUM(precio * cantidad), 0)
                    INTO total_importe
                    FROM trabajo_servicios
                    WHERE trabajo_id = NEW.trabajo_id;

                    -- Calcular el total de pagos realizados
                    SELECT COALESCE(SUM(monto), 0)
                    INTO total_pagos
                    FROM trabajo_pagos
                    WHERE trabajo_id = NEW.trabajo_id;

                    -- Verificar si FECHA_SALIDA es NULL
                    IF (SELECT FECHA_SALIDA FROM trabajos WHERE id = NEW.trabajo_id) IS NOT NULL THEN
                        -- Actualizar el campo 'desembolso'
                        IF total_pagos >= total_importe THEN
                            UPDATE trabajos
                            SET desembolso = 'COBRADO'
                            WHERE id = NEW.trabajo_id;
                        ELSEIF total_pagos > 0 THEN
                            UPDATE trabajos
                            SET desembolso = 'A CUENTA'
                            WHERE id = NEW.trabajo_id;
                        ELSE
                            UPDATE trabajos
                            SET desembolso = 'POR COBRAR'
                            WHERE id = NEW.trabajo_id;
                        END IF;
                    END IF;
                END;
        ");

        DB::statement("
            CREATE TRIGGER actualizar_desembolso_after_update_pagos
                AFTER UPDATE
                ON trabajo_pagos
                FOR EACH ROW
                BEGIN
                    DECLARE total_importe DECIMAL(10, 2);
                    DECLARE total_pagos DECIMAL(10, 2);

                    -- Calcular el importe total (precio * cantidad de servicios)
                    SELECT COALESCE(SUM(precio * cantidad), 0)
                    INTO total_importe
                    FROM trabajo_servicios
                    WHERE trabajo_id = NEW.trabajo_id;

                    -- Calcular el total de pagos realizados
                    SELECT COALESCE(SUM(monto), 0)
                    INTO total_pagos
                    FROM trabajo_pagos
                    WHERE trabajo_id = NEW.trabajo_id;

                    -- Verificar si FECHA_SALIDA es NULL
                    IF (SELECT FECHA_SALIDA FROM trabajos WHERE id = NEW.trabajo_id) IS NOT NULL THEN
                        -- Actualizar el campo 'desembolso'
                        IF total_pagos >= total_importe THEN
                            UPDATE trabajos
                            SET desembolso = 'COBRADO'
                            WHERE id = NEW.trabajo_id;
                        ELSEIF total_pagos > 0 THEN
                            UPDATE trabajos
                            SET desembolso = 'A CUENTA'
                            WHERE id = NEW.trabajo_id;
                        ELSE
                            UPDATE trabajos
                            SET desembolso = 'POR COBRAR'
                            WHERE id = NEW.trabajo_id;
                        END IF;
                    END IF;
                END;
        ");

        DB::unprepared("
            CREATE TRIGGER actualizar_desembolso_after_delete_pagos
                AFTER DELETE
                ON trabajo_pagos
                FOR EACH ROW
                BEGIN
                    DECLARE total_importe DECIMAL(10, 2);
                    DECLARE total_pagos DECIMAL(10, 2);

                    -- Calcular el importe total (precio * cantidad de servicios)
                    SELECT COALESCE(SUM(precio * cantidad), 0)
                    INTO total_importe
                    FROM trabajo_servicios
                    WHERE trabajo_id = OLD.trabajo_id;

                    -- Calcular el total de pagos realizados
                    SELECT COALESCE(SUM(monto), 0)
                    INTO total_pagos
                    FROM trabajo_pagos
                    WHERE trabajo_id = OLD.trabajo_id;

                    -- Verificar si FECHA_SALIDA es NULL
                    IF (SELECT FECHA_SALIDA FROM trabajos WHERE id = OLD.trabajo_id) IS NOT NULL THEN
                        -- Actualizar el campo 'desembolso'
                        IF total_pagos >= total_importe THEN
                            UPDATE trabajos
                            SET desembolso = 'COBRADO'
                            WHERE id = OLD.trabajo_id;
                        ELSEIF total_pagos > 0 THEN
                            UPDATE trabajos
                            SET desembolso = 'A CUENTA'
                            WHERE id = OLD.trabajo_id;
                        ELSE
                            UPDATE trabajos
                            SET desembolso = 'POR COBRAR'
                            WHERE id = OLD.trabajo_id;
                        END IF;
                    END IF;
                END;
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pagos');
    }
};

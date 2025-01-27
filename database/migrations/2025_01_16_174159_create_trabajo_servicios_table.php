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
        Schema::create('trabajo_servicios', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('sort')->default(0);
            $table->foreignId('trabajo_id')
                ->constrained('trabajos')
                ->onDelete('restrict')
                ->onUpdate('cascade');
            $table->foreignId('servicio_id')
                ->constrained('servicios')
                ->onDelete('restrict')
                ->onUpdate('cascade');
            $table->decimal('precio');
            $table->unsignedInteger('cantidad')->default(1);
            $table->timestamps();
        });

        DB::unprepared("
            CREATE TRIGGER actualizar_desembolso_after_insert_servicios
                AFTER INSERT
                ON trabajo_servicios
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
            CREATE TRIGGER actualizar_desembolso_after_update_servicios
                AFTER UPDATE
                ON trabajo_servicios
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
            CREATE TRIGGER actualizar_desembolso_after_delete_servicios
                AFTER DELETE
                ON trabajo_servicios
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
        Schema::dropIfExists('trabajo_servicios');
    }
};

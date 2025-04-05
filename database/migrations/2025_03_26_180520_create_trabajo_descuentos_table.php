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
        Schema::create('trabajo_descuentos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trabajo_id')
                ->nullable()
                ->constrained('trabajos')
                ->onDelete('restrict')
                ->onUpdate('cascade');
            $table->text('detalle');
            $table->decimal('descuento');
            $table->timestamps();
        });

        DB::unprepared("
            CREATE TRIGGER finanzas_after_insert_trabajo_descuento
            AFTER INSERT ON trabajo_descuentos
            FOR EACH ROW
            BEGIN
                DECLARE total_articulos DECIMAL(10, 2);
                DECLARE total_servicios DECIMAL(10, 2);
                DECLARE total_otros DECIMAL(10, 2);
                DECLARE total_sin_descuento DECIMAL(10, 2);
                DECLARE factor_descuento DECIMAL(10, 2) DEFAULT 1;
                DECLARE descuento_total DECIMAL(10, 2);

                -- Obtener el descuento total (suma de todos los descuentos para este trabajo)
                SELECT COALESCE(SUM(descuento), 0) INTO descuento_total
                FROM trabajo_descuentos
                WHERE trabajo_id = NEW.trabajo_id;

                -- Calcular el factor de descuento
                IF descuento_total > 0 THEN
                    SET factor_descuento = 1 - (descuento_total / 100);
                END IF;

                -- Calcular el total de artículos para el trabajo (solo donde presupuesto = true)
                SELECT COALESCE(SUM(precio * cantidad), 0) INTO total_articulos
                FROM trabajo_articulos
                WHERE trabajo_id = NEW.trabajo_id AND presupuesto = true;

                -- Calcular el total de servicios para el trabajo
                SELECT COALESCE(SUM(precio * cantidad), 0) INTO total_servicios
                FROM trabajo_servicios
                WHERE trabajo_id = NEW.trabajo_id;

                -- Calcular el total de otros para el trabajo
                SELECT COALESCE(SUM(precio * cantidad), 0) INTO total_otros
                FROM trabajo_otros
                WHERE trabajo_id = NEW.trabajo_id;

                -- Calcular el total sin descuento
                SET total_sin_descuento = total_articulos + total_servicios + total_otros;

                -- Actualizar el campo `importe` en la tabla `trabajos` aplicando el descuento
                UPDATE trabajos
                SET importe = total_sin_descuento * factor_descuento
                WHERE id = NEW.trabajo_id;
            END;
        ");

        DB::unprepared("
            CREATE TRIGGER finanzas_after_update_trabajo_descuento
            AFTER UPDATE ON trabajo_descuentos
            FOR EACH ROW
            BEGIN
                DECLARE total_articulos DECIMAL(10, 2);
                DECLARE total_servicios DECIMAL(10, 2);
                DECLARE total_otros DECIMAL(10, 2);
                DECLARE total_sin_descuento DECIMAL(10, 2);
                DECLARE factor_descuento DECIMAL(10, 2) DEFAULT 1;
                DECLARE descuento_total DECIMAL(10, 2);

                -- Obtener el descuento total (suma de todos los descuentos para este trabajo)
                SELECT COALESCE(SUM(descuento), 0) INTO descuento_total
                FROM trabajo_descuentos
                WHERE trabajo_id = NEW.trabajo_id;

                -- Calcular el factor de descuento
                IF descuento_total > 0 THEN
                    SET factor_descuento = 1 - (descuento_total / 100);
                END IF;

                -- Calcular el total de artículos para el trabajo (solo donde presupuesto = true)
                SELECT COALESCE(SUM(precio * cantidad), 0) INTO total_articulos
                FROM trabajo_articulos
                WHERE trabajo_id = NEW.trabajo_id AND presupuesto = true;

                -- Calcular el total de servicios para el trabajo
                SELECT COALESCE(SUM(precio * cantidad), 0) INTO total_servicios
                FROM trabajo_servicios
                WHERE trabajo_id = NEW.trabajo_id;

                -- Calcular el total de otros para el trabajo
                SELECT COALESCE(SUM(precio * cantidad), 0) INTO total_otros
                FROM trabajo_otros
                WHERE trabajo_id = NEW.trabajo_id;

                -- Calcular el total sin descuento
                SET total_sin_descuento = total_articulos + total_servicios + total_otros;

                -- Actualizar el campo `importe` en la tabla `trabajos` aplicando el descuento
                UPDATE trabajos
                SET importe = total_sin_descuento * factor_descuento
                WHERE id = NEW.trabajo_id;
            END;
        ");

        DB::unprepared("
            CREATE TRIGGER finanzas_after_delete_trabajo_descuento
            AFTER DELETE ON trabajo_descuentos
            FOR EACH ROW
            BEGIN
                DECLARE total_articulos DECIMAL(10, 2);
                DECLARE total_servicios DECIMAL(10, 2);
                DECLARE total_otros DECIMAL(10, 2);
                DECLARE total_sin_descuento DECIMAL(10, 2);
                DECLARE factor_descuento DECIMAL(10, 2) DEFAULT 1;
                DECLARE descuento_total DECIMAL(10, 2);

                -- Obtener el descuento total (suma de todos los descuentos para este trabajo)
                SELECT COALESCE(SUM(descuento), 0) INTO descuento_total
                FROM trabajo_descuentos
                WHERE trabajo_id = OLD.trabajo_id;

                -- Calcular el factor de descuento
                IF descuento_total > 0 THEN
                    SET factor_descuento = 1 - (descuento_total / 100);
                END IF;

                -- Calcular el total de artículos para el trabajo (solo donde presupuesto = true)
                SELECT COALESCE(SUM(precio * cantidad), 0) INTO total_articulos
                FROM trabajo_articulos
                WHERE trabajo_id = OLD.trabajo_id AND presupuesto = true;

                -- Calcular el total de servicios para el trabajo
                SELECT COALESCE(SUM(precio * cantidad), 0) INTO total_servicios
                FROM trabajo_servicios
                WHERE trabajo_id = OLD.trabajo_id;

                -- Calcular el total de otros para el trabajo
                SELECT COALESCE(SUM(precio * cantidad), 0) INTO total_otros
                FROM trabajo_otros
                WHERE trabajo_id = OLD.trabajo_id;

                -- Calcular el total sin descuento
                SET total_sin_descuento = total_articulos + total_servicios + total_otros;

                -- Actualizar el campo `importe` en la tabla `trabajos` aplicando el descuento
                UPDATE trabajos
                SET importe = total_sin_descuento * factor_descuento
                WHERE id = OLD.trabajo_id;
            END;
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trabajo_descuentos');

        DB::unprepared("DROP TRIGGER IF EXISTS finanzas_after_insert_trabajo_descuento");
        DB::unprepared("DROP TRIGGER IF EXISTS finanzas_after_update_trabajo_descuento");
        DB::unprepared("DROP TRIGGER IF EXISTS finanzas_after_delete_trabajo_descuento");
    }
};

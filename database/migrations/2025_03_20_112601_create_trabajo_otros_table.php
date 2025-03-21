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
        Schema::create('trabajo_otros', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trabajo_id')
                ->nullable()
                ->constrained('trabajos')
                ->onDelete('restrict')
                ->onUpdate('cascade');
            $table->text('descripcion');
            $table->decimal('precio')->unsigned();
            $table->unsignedInteger('cantidad')->default(1);
            $table->timestamps();
        });

        DB::unprepared("
            CREATE TRIGGER finanzas_after_insert_trabajo_articulo
            AFTER INSERT ON trabajo_articulos
            FOR EACH ROW
            BEGIN
                DECLARE total_articulos DECIMAL(10, 2);
                DECLARE total_servicios DECIMAL(10, 2);
                DECLARE total_otros DECIMAL(10, 2);

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

                -- Actualizar el campo `importe` en la tabla `trabajos`
                UPDATE trabajos
                SET importe = total_articulos + total_servicios + total_otros
                WHERE id = NEW.trabajo_id;
            END;
        ");

        DB::unprepared("
            CREATE TRIGGER finanzas_after_update_trabajo_articulo
            AFTER UPDATE ON trabajo_articulos
            FOR EACH ROW
            BEGIN
                DECLARE total_articulos DECIMAL(10, 2);
                DECLARE total_servicios DECIMAL(10, 2);
                DECLARE total_otros DECIMAL(10, 2);

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

                -- Actualizar el campo `importe` en la tabla `trabajos`
                UPDATE trabajos
                SET importe = total_articulos + total_servicios + total_otros
                WHERE id = NEW.trabajo_id;
            END;
        ");

        DB::unprepared("
            CREATE TRIGGER finanzas_after_delete_trabajo_articulo
            AFTER DELETE ON trabajo_articulos
            FOR EACH ROW
            BEGIN
                DECLARE total_articulos DECIMAL(10, 2);
                DECLARE total_servicios DECIMAL(10, 2);
                DECLARE total_otros DECIMAL(10, 2);

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

                -- Actualizar el campo `importe` en la tabla `trabajos`
                UPDATE trabajos
                SET importe = total_articulos + total_servicios + total_otros
                WHERE id = OLD.trabajo_id;
            END;
        ");

        DB::unprepared("
            CREATE TRIGGER finanzas_after_insert_trabajo_servicio
            AFTER INSERT ON trabajo_servicios
            FOR EACH ROW
            BEGIN
                DECLARE total_articulos DECIMAL(10, 2);
                DECLARE total_servicios DECIMAL(10, 2);
                DECLARE total_otros DECIMAL(10, 2);

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

                -- Actualizar el campo `importe` en la tabla `trabajos`
                UPDATE trabajos
                SET importe = total_articulos + total_servicios + total_otros
                WHERE id = NEW.trabajo_id;
            END;
        ");

        DB::unprepared("
            CREATE TRIGGER finanzas_after_update_trabajo_servicio
            AFTER UPDATE ON trabajo_servicios
            FOR EACH ROW
            BEGIN
                DECLARE total_articulos DECIMAL(10, 2);
                DECLARE total_servicios DECIMAL(10, 2);
                DECLARE total_otros DECIMAL(10, 2);

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

                -- Actualizar el campo `importe` en la tabla `trabajos`
                UPDATE trabajos
                SET importe = total_articulos + total_servicios + total_otros
                WHERE id = NEW.trabajo_id;
            END;
        ");

        DB::unprepared("
            CREATE TRIGGER finanzas_after_delete_trabajo_servicio
            AFTER DELETE ON trabajo_servicios
            FOR EACH ROW
            BEGIN
                DECLARE total_articulos DECIMAL(10, 2);
                DECLARE total_servicios DECIMAL(10, 2);
                DECLARE total_otros DECIMAL(10, 2);

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

                -- Actualizar el campo `importe` en la tabla `trabajos`
                UPDATE trabajos
                SET importe = total_articulos + total_servicios + total_otros
                WHERE id = OLD.trabajo_id;
            END;
        ");

        DB::unprepared("
            CREATE TRIGGER finanzas_after_insert_trabajo_otro
            AFTER INSERT ON trabajo_otros
            FOR EACH ROW
            BEGIN
                DECLARE total_articulos DECIMAL(10, 2);
                DECLARE total_servicios DECIMAL(10, 2);
                DECLARE total_otros DECIMAL(10, 2);

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

                -- Actualizar el campo `importe` en la tabla `trabajos`
                UPDATE trabajos
                SET importe = total_articulos + total_servicios + total_otros
                WHERE id = NEW.trabajo_id;
            END;
        ");

        DB::unprepared("
            CREATE TRIGGER finanzas_after_update_trabajo_otro
            AFTER UPDATE ON trabajo_otros
            FOR EACH ROW
            BEGIN
                DECLARE total_articulos DECIMAL(10, 2);
                DECLARE total_servicios DECIMAL(10, 2);
                DECLARE total_otros DECIMAL(10, 2);

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

                -- Actualizar el campo `importe` en la tabla `trabajos`
                UPDATE trabajos
                SET importe = total_articulos + total_servicios + total_otros
                WHERE id = NEW.trabajo_id;
            END;
        ");

        DB::unprepared("
            CREATE TRIGGER finanzas_after_delete_trabajo_otro
            AFTER DELETE ON trabajo_otros
            FOR EACH ROW
            BEGIN
                DECLARE total_articulos DECIMAL(10, 2);
                DECLARE total_servicios DECIMAL(10, 2);
                DECLARE total_otros DECIMAL(10, 2);

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

                -- Actualizar el campo `importe` en la tabla `trabajos`
                UPDATE trabajos
                SET importe = total_articulos + total_servicios + total_otros
                WHERE id = OLD.trabajo_id;
            END;
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trabajo_otros');

        DB::unprepared("DROP TRIGGER IF EXISTS finanzas_after_insert_trabajo_articulo");
        DB::unprepared("DROP TRIGGER IF EXISTS finanzas_after_update_trabajo_articulo");
        DB::unprepared("DROP TRIGGER IF EXISTS finanzas_after_delete_trabajo_articulo");

        DB::unprepared("DROP TRIGGER IF EXISTS finanzas_after_insert_trabajo_servicio");
        DB::unprepared("DROP TRIGGER IF EXISTS finanzas_after_update_trabajo_servicio");
        DB::unprepared("DROP TRIGGER IF EXISTS finanzas_after_delete_trabajo_servicio");

        DB::unprepared("DROP TRIGGER IF EXISTS finanzas_after_insert_trabajo_otro");
        DB::unprepared("DROP TRIGGER IF EXISTS finanzas_after_update_trabajo_otro");
        DB::unprepared("DROP TRIGGER IF EXISTS finanzas_after_delete_trabajo_otro");
    }
};

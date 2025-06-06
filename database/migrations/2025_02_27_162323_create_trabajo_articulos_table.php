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
        Schema::create('trabajo_articulos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('despacho_id')
                ->nullable()
                ->constrained('despachos')
                ->onDelete('restrict')
                ->onUpdate('cascade');
            $table->date('fecha');
            $table->time('hora');
            $table->foreignId('trabajo_id')
                ->nullable()
                ->constrained('trabajos')
                ->onDelete('restrict')
                ->onUpdate('cascade');
            $table->foreignId('articulo_id')
                ->constrained('articulos')
                ->onDelete('restrict')
                ->onUpdate('cascade');
            $table->decimal('precio')->unsigned();
            $table->decimal('cantidad')->unsigned()->default(1);
            $table->foreignId('tecnico_id')
                ->constrained('users')
                ->onDelete('restrict')
                ->onUpdate('cascade');
            $table->foreignId('responsable_id')
                ->constrained('users')
                ->onDelete('restrict')
                ->onUpdate('cascade');
            $table->enum('movimiento', [
                'consumo_completo',
                'abrir_nuevo',
                'terminar_abierto',
                'consumo_parcial',
            ])->default('consumo_completo');
            $table->text('observacion')->nullable();
            $table->boolean('confirmado')->default(false);
            $table->boolean('presupuesto')->default(true);
            $table->timestamps();
        });

        DB::unprepared('
            CREATE TRIGGER proforma_before_trabajo_articulos_insert
            BEFORE INSERT ON trabajo_articulos
            FOR EACH ROW
            BEGIN
                DECLARE articulo_precio DECIMAL(10, 2);
                DECLARE articulo_costo DECIMAL(10, 2);

                -- Obtener el precio y costo del artículo
                SELECT precio, costo INTO articulo_precio, articulo_costo
                FROM articulos
                WHERE id = NEW.articulo_id;

                -- Establecer el precio en trabajo_articulos
                IF articulo_precio IS NOT NULL THEN
                    SET NEW.precio = articulo_precio;
                ELSEIF articulo_costo IS NOT NULL THEN
                    SET NEW.precio = articulo_costo;
                ELSE
                    SET NEW.precio = 0;
                END IF;
            END
        ');

        DB::unprepared('
            CREATE TRIGGER inventario_after_trabajo_articulos_insert
            AFTER INSERT ON trabajo_articulos
            FOR EACH ROW
            BEGIN
                DECLARE es_fraccionable BOOLEAN;
        
                -- Obtener el valor de fraccionable del artículo
                SELECT fraccionable INTO es_fraccionable
                FROM articulos
                WHERE id = NEW.articulo_id;
        
                IF NEW.movimiento = "consumo_completo" THEN
                    IF es_fraccionable = FALSE THEN
                        -- Si no es fraccionable, restar la cantidad directamente
                        UPDATE articulos
                        SET stock = stock - NEW.cantidad
                        WHERE id = NEW.articulo_id;
                    ELSE
                        -- Si es fraccionable, aplicar CEIL
                        UPDATE articulos
                        SET stock = stock - CEIL(NEW.cantidad)
                        WHERE id = NEW.articulo_id;
                    END IF;
                ELSEIF NEW.movimiento = "abrir_nuevo" THEN
                    UPDATE articulos
                    SET stock = stock - 1,
                        abiertos = abiertos + 1
                    WHERE id = NEW.articulo_id;
                ELSEIF NEW.movimiento = "terminar_abierto" THEN
                    UPDATE articulos
                    SET abiertos = abiertos - 1
                    WHERE id = NEW.articulo_id;
                END IF;
            END
        ');

        DB::unprepared('
            CREATE TRIGGER inventario_after_trabajo_articulos_update
            AFTER UPDATE ON trabajo_articulos
            FOR EACH ROW
            BEGIN
                DECLARE es_fraccionable BOOLEAN;

                -- Obtener el valor de fraccionable del artículo
                SELECT fraccionable INTO es_fraccionable
                FROM articulos
                WHERE id = OLD.articulo_id;

                -- Revertir cambios anteriores
                IF OLD.movimiento = "consumo_completo" THEN
                    IF es_fraccionable = FALSE THEN
                        -- Si no es fraccionable, sumar la cantidad directamente
                        UPDATE articulos
                        SET stock = stock + OLD.cantidad
                        WHERE id = OLD.articulo_id;
                    ELSE
                        -- Si es fraccionable, aplicar CEIL
                        UPDATE articulos
                        SET stock = stock + CEIL(OLD.cantidad)
                        WHERE id = OLD.articulo_id;
                    END IF;
                ELSEIF OLD.movimiento = "abrir_nuevo" THEN
                    UPDATE articulos
                    SET stock = stock + 1,
                        abiertos = abiertos - 1
                    WHERE id = OLD.articulo_id;
                ELSEIF OLD.movimiento = "terminar_abierto" THEN
                    UPDATE articulos
                    SET abiertos = abiertos + 1
                    WHERE id = OLD.articulo_id;
                END IF;

                -- Aplicar nuevos cambios
                SELECT fraccionable INTO es_fraccionable
                FROM articulos
                WHERE id = NEW.articulo_id;

                IF NEW.movimiento = "consumo_completo" THEN
                    IF es_fraccionable = FALSE THEN
                        -- Si no es fraccionable, restar la cantidad directamente
                        UPDATE articulos
                        SET stock = stock - NEW.cantidad
                        WHERE id = NEW.articulo_id;
                    ELSE
                        -- Si es fraccionable, aplicar CEIL
                        UPDATE articulos
                        SET stock = stock - CEIL(NEW.cantidad)
                        WHERE id = NEW.articulo_id;
                    END IF;
                ELSEIF NEW.movimiento = "abrir_nuevo" THEN
                    UPDATE articulos
                    SET stock = stock - 1,
                        abiertos = abiertos + 1
                    WHERE id = NEW.articulo_id;
                ELSEIF NEW.movimiento = "terminar_abierto" THEN
                    UPDATE articulos
                    SET abiertos = abiertos - 1
                    WHERE id = NEW.articulo_id;
                END IF;
            END
        ');

        DB::unprepared('
            CREATE TRIGGER inventario_after_trabajo_articulos_delete
            AFTER DELETE ON trabajo_articulos
            FOR EACH ROW
            BEGIN
                DECLARE es_fraccionable BOOLEAN;

                -- Obtener el valor de fraccionable del artículo
                SELECT fraccionable INTO es_fraccionable
                FROM articulos
                WHERE id = OLD.articulo_id;

                IF OLD.movimiento = "consumo_completo" THEN
                    IF es_fraccionable = FALSE THEN
                        -- Si no es fraccionable, sumar la cantidad directamente
                        UPDATE articulos
                        SET stock = stock + OLD.cantidad
                        WHERE id = OLD.articulo_id;
                    ELSE
                        -- Si es fraccionable, aplicar CEIL
                        UPDATE articulos
                        SET stock = stock + CEIL(OLD.cantidad)
                        WHERE id = OLD.articulo_id;
                    END IF;
                ELSEIF OLD.movimiento = "abrir_nuevo" THEN
                    UPDATE articulos
                    SET stock = stock + 1,
                        abiertos = abiertos - 1
                    WHERE id = OLD.articulo_id;
                ELSEIF OLD.movimiento = "terminar_abierto" THEN
                    UPDATE articulos
                    SET abiertos = abiertos + 1
                    WHERE id = OLD.articulo_id;
                END IF;
            END
        ');

        DB::unprepared("
            CREATE TRIGGER despacho_before_insert_trabajo_articulo
            BEFORE INSERT ON trabajo_articulos
            FOR EACH ROW
            BEGIN
                DECLARE despacho_fecha DATE;
                DECLARE despacho_hora TIME;
                DECLARE despacho_trabajo_id INT;
                DECLARE despacho_tecnico_id INT;
                DECLARE despacho_responsable_id INT;

                -- Obtener los valores del despacho si está relacionado
                IF NEW.despacho_id IS NOT NULL THEN
                    SELECT fecha, hora, trabajo_id, tecnico_id, responsable_id
                    INTO despacho_fecha, despacho_hora, despacho_trabajo_id, despacho_tecnico_id, despacho_responsable_id
                    FROM despachos
                    WHERE id = NEW.despacho_id;

                    -- Establecer los valores del despacho si los campos en trabajo_articulos son NULL
                    IF NEW.fecha IS NULL THEN
                        SET NEW.fecha = despacho_fecha;
                    END IF;

                    IF NEW.hora IS NULL THEN
                        SET NEW.hora = despacho_hora;
                    END IF;

                    IF NEW.trabajo_id IS NULL THEN
                        SET NEW.trabajo_id = despacho_trabajo_id;
                    END IF;

                    IF NEW.tecnico_id IS NULL THEN
                        SET NEW.tecnico_id = despacho_tecnico_id;
                    END IF;

                    IF NEW.responsable_id IS NULL THEN
                        SET NEW.responsable_id = despacho_responsable_id;
                    END IF;
                END IF;
            END;
        ");

        DB::unprepared("
            CREATE TRIGGER despacho_after_update_despacho
            AFTER UPDATE ON despachos
            FOR EACH ROW
            BEGIN
                -- Actualizar los campos en trabajo_articulos si el despacho_id coincide
                UPDATE trabajo_articulos
                SET fecha = NEW.fecha,
                    hora = NEW.hora,
                    trabajo_id = NEW.trabajo_id,
                    tecnico_id = NEW.tecnico_id,
                    responsable_id = NEW.responsable_id
                WHERE despacho_id = NEW.id;
            END;
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trabajo_articulos');

        DB::unprepared('DROP TRIGGER IF EXISTS despacho_before_insert_trabajo_articulo;');
        DB::unprepared('DROP TRIGGER IF EXISTS despacho_after_update_despacho;');

        DB::unprepared('DROP TRIGGER IF EXISTS proforma_before_trabajo_articulos_insert');

        DB::unprepared("DROP TRIGGER IF EXISTS inventario_after_trabajo_articulos_insert");
        DB::unprepared("DROP TRIGGER IF EXISTS inventario_after_trabajo_articulos_update");
        DB::unprepared("DROP TRIGGER IF EXISTS inventario_after_trabajo_articulos_delete");
    }
};

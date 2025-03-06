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
        Schema::create('venta_articulos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('venta_id')
                ->constrained('ventas')
                ->onDelete('restrict')
                ->onUpdate('cascade');
            $table->foreignId('articulo_id')
                ->constrained('articulos')
                ->onDelete('restrict')
                ->onUpdate('cascade');
            $table->decimal('precio');
            $table->unsignedInteger('cantidad')->default(1);
            $table->timestamps();
        });

        DB::unprepared("
            CREATE TRIGGER after_venta_articulo_insert
            AFTER INSERT ON venta_articulos
            FOR EACH ROW
            BEGIN
                DECLARE current_stock INT;
                SELECT stock INTO current_stock FROM articulos WHERE id = NEW.articulo_id;

                IF current_stock >= NEW.cantidad THEN
                    UPDATE articulos
                    SET stock = stock - NEW.cantidad
                    WHERE id = NEW.articulo_id;
                ELSE
                    UPDATE articulos
                    SET stock = 0
                    WHERE id = NEW.articulo_id;
                END IF;
            END;
        ");

        DB::unprepared("
            CREATE TRIGGER after_venta_articulo_update
            AFTER UPDATE ON venta_articulos
            FOR EACH ROW
            BEGIN
                DECLARE current_stock INT;

                -- Revertir la cantidad anterior
                UPDATE articulos
                SET stock = stock + OLD.cantidad
                WHERE id = OLD.articulo_id;

                -- Aplicar la nueva cantidad
                SELECT stock INTO current_stock FROM articulos WHERE id = NEW.articulo_id;

                IF current_stock >= NEW.cantidad THEN
                    UPDATE articulos
                    SET stock = stock - NEW.cantidad
                    WHERE id = NEW.articulo_id;
                ELSE
                    UPDATE articulos
                    SET stock = 0
                    WHERE id = NEW.articulo_id;
                END IF;
            END;
        ");

        DB::unprepared("
            CREATE TRIGGER after_venta_articulo_delete
            AFTER DELETE ON venta_articulos
            FOR EACH ROW
            BEGIN
                UPDATE articulos
                SET stock = stock + OLD.cantidad
                WHERE id = OLD.articulo_id;
            END;
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('venta_articulos');
    }
};

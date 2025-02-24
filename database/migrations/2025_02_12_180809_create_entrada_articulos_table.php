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
        Schema::create('entrada_articulos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('entrada_id')
                ->constrained('entradas')
                ->onDelete('restrict')
                ->onUpdate('cascade');
            $table->foreignId('articulo_id')
                ->constrained('articulos')
                ->onDelete('restrict')
                ->onUpdate('cascade');
            $table->decimal('costo');
            $table->unsignedInteger('cantidad')->default(1);
            $table->timestamps();
            $table->softDeletes();
        });

        DB::unprepared('
            CREATE TRIGGER after_insert_entrada_articulos
            AFTER INSERT ON entrada_articulos
            FOR EACH ROW
            BEGIN
                UPDATE articulos
                SET stock = stock + NEW.cantidad
                WHERE id = NEW.articulo_id;
            END
        ');

        DB::unprepared('
            CREATE TRIGGER after_update_entrada_articulos
            AFTER UPDATE ON entrada_articulos
            FOR EACH ROW
            BEGIN
                -- Restar la cantidad anterior
                UPDATE articulos
                SET stock = stock - OLD.cantidad
                WHERE id = OLD.articulo_id;

                -- Sumar la nueva cantidad
                UPDATE articulos
                SET stock = stock + NEW.cantidad
                WHERE id = NEW.articulo_id;
            END
        ');

        DB::unprepared('
            CREATE TRIGGER after_delete_entrada_articulos
            AFTER DELETE ON entrada_articulos
            FOR EACH ROW
            BEGIN
                UPDATE articulos
                SET stock = stock - OLD.cantidad
                WHERE id = OLD.articulo_id;
            END
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('entrada_articulos');

        DB::unprepared('DROP TRIGGER IF EXISTS after_insert_entrada_articulos');
        DB::unprepared('DROP TRIGGER IF EXISTS after_update_entrada_articulos');
        DB::unprepared('DROP TRIGGER IF EXISTS after_delete_entrada_articulos');
    }
};

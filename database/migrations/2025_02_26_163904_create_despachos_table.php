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
        Schema::create('despachos', function (Blueprint $table) {
            $table->id();
            $table->string('codigo', 20);
            $table->date('fecha');
            $table->time('hora');
            $table->text('observacion')->nullable();
            $table->foreignId('trabajo_id')
                ->nullable()
                ->constrained('trabajos')
                ->onDelete('restrict')
                ->onUpdate('cascade');
            $table->foreignId('tecnico_id')
                ->constrained('users')
                ->onDelete('restrict')
                ->onUpdate('cascade');
            $table->foreignId('responsable_id')
                ->constrained('users')
                ->onDelete('restrict')
                ->onUpdate('cascade');
            $table->timestamps();
        });

        Schema::table('trabajo_articulos', function (Blueprint $table) {
            $table->unsignedBigInteger('despacho_id')->nullable()->after('id');
            $table->foreign('despacho_id')->references('id')->on('despachos')->onDelete('restrict');
        });

        DB::unprepared("
            CREATE TRIGGER before_insert_trabajo_articulo
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
            CREATE TRIGGER after_update_despacho
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
        DB::unprepared('DROP TRIGGER IF EXISTS before_insert_trabajo_articulo;');
        DB::unprepared('DROP TRIGGER IF EXISTS after_update_despacho;');

        Schema::table('trabajo_articulos', function (Blueprint $table) {
            $table->dropForeign(['despacho_id']);
            $table->dropColumn('despacho_id');
        });

        Schema::dropIfExists('despachos');
    }
};

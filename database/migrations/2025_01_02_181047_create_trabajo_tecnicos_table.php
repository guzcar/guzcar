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
        Schema::create('trabajo_tecnicos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tecnico_id')
                ->constrained('users')
                ->onDelete('restrict')
                ->onUpdate('cascade');
            $table->foreignId('trabajo_id')
                ->constrained('trabajos')
                ->onDelete('restrict')
                ->onUpdate('cascade');
            $table->boolean('finalizado')->default(false);
            $table->timestamps();
        });

        DB::statement("
            CREATE TRIGGER before_insert_evidencia
            BEFORE INSERT ON evidencias
            FOR EACH ROW
            BEGIN
                -- Asignar la descripción del servicio solo si no se proporcionó una descripción
                IF NEW.observacion IS NULL OR NEW.observacion = '' THEN
                    SET NEW.observacion = (SELECT descripcion_servicio FROM trabajos WHERE id = NEW.trabajo_id);
                END IF;

                -- Asignar el user_id solo si no se proporcionó uno
                IF NEW.user_id IS NULL THEN
                    -- Obtener el primer técnico asociado al trabajo
                    SET NEW.user_id = (
                        SELECT tecnico_id
                        FROM trabajo_tecnicos
                        WHERE trabajo_id = NEW.trabajo_id
                        ORDER BY id ASC
                        LIMIT 1
                    );

                    -- Si no hay técnicos, asignar el valor 1
                    IF NEW.user_id IS NULL THEN
                        SET NEW.user_id = 1;
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
        DB::statement('DROP TRIGGER IF EXISTS before_insert_evidencia');
        Schema::dropIfExists('trabajo_tecnicos');
    }
};

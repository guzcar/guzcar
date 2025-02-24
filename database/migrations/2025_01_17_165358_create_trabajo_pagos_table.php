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

        DB::unprepared("
            CREATE TRIGGER after_insert_trabajo_pago
            AFTER INSERT ON trabajo_pagos
            FOR EACH ROW
            BEGIN
                -- Actualizar el campo `a_cuenta` en la tabla `trabajos`
                UPDATE trabajos
                SET a_cuenta = (
                    SELECT COALESCE(SUM(monto), 0)
                    FROM trabajo_pagos
                    WHERE trabajo_id = NEW.trabajo_id
                )
                WHERE id = NEW.trabajo_id;
            END;
        ");

        DB::unprepared("
            CREATE TRIGGER after_update_trabajo_pago
            AFTER UPDATE ON trabajo_pagos
            FOR EACH ROW
            BEGIN
                -- Actualizar el campo `a_cuenta` en la tabla `trabajos`
                UPDATE trabajos
                SET a_cuenta = (
                    SELECT COALESCE(SUM(monto), 0)
                    FROM trabajo_pagos
                    WHERE trabajo_id = NEW.trabajo_id
                )
                WHERE id = NEW.trabajo_id;
            END;
        ");

        DB::unprepared("
            CREATE TRIGGER after_delete_trabajo_pago
            AFTER DELETE ON trabajo_pagos
            FOR EACH ROW
            BEGIN
                -- Actualizar el campo `a_cuenta` en la tabla `trabajos`
                UPDATE trabajos
                SET a_cuenta = (
                    SELECT COALESCE(SUM(monto), 0)
                    FROM trabajo_pagos
                    WHERE trabajo_id = OLD.trabajo_id
                )
                WHERE id = OLD.trabajo_id;
            END;
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pagos');
        
        DB::unprepared("DROP TRIGGER IF EXISTS after_insert_trabajo_pago");
        DB::unprepared("DROP TRIGGER IF EXISTS after_update_trabajo_pago");
        DB::unprepared("DROP TRIGGER IF EXISTS after_delete_trabajo_pago");
    }
};

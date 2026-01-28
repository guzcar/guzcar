<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('entrega_equipos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('equipo_id');
            $table->unsignedBigInteger('propietario_id')->nullable(); 
            $table->unsignedBigInteger('responsable_id');
            $table->text('evidencia')->nullable();
            $table->dateTime('fecha');
            $table->timestamps();

            $table->foreign('equipo_id')->references('id')->on('equipos')->restrictOnDelete();
            $table->foreign('propietario_id')->references('id')->on('users')->restrictOnDelete();
            $table->foreign('responsable_id')->references('id')->on('users')->restrictOnDelete();
        });

        Schema::create('entrega_equipo_detalles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('entrega_equipo_id');
            $table->unsignedBigInteger('implemento_id');
            $table->timestamps();

            $table->foreign('entrega_equipo_id')->references('id')->on('entrega_equipos')->cascadeOnDelete();
            $table->foreign('implemento_id')->references('id')->on('implementos')->restrictOnDelete();
        });

        // Trigger para auto-asignar propietario
        DB::unprepared('
            CREATE TRIGGER tr_entrega_equipos_before_insert 
            BEFORE INSERT ON entrega_equipos 
            FOR EACH ROW 
            BEGIN
                DECLARE current_owner_id BIGINT;
                SELECT propietario_id INTO current_owner_id 
                FROM equipos 
                WHERE id = NEW.equipo_id;
                SET NEW.propietario_id = current_owner_id;
            END
        ');
    }

    public function down(): void
    {
        DB::unprepared('DROP TRIGGER IF EXISTS tr_entrega_equipos_before_insert');
        Schema::dropIfExists('entrega_equipo_detalles');
        Schema::dropIfExists('entrega_equipos');
    }   
};
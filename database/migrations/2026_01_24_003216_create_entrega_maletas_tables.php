<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Tabla: entrega_maletas
        Schema::create('entrega_maletas', function (Blueprint $table) {
            $table->id();
            
            $table->unsignedBigInteger('maleta_id');
            // El propietario se llenará automáticamente vía Trigger, pero debe ser nullable en el esquema inicial
            $table->unsignedBigInteger('propietario_id')->nullable(); 
            $table->unsignedBigInteger('responsable_id');
            $table->text('evidencia')->nullable();
            $table->dateTime('fecha');
            
            $table->timestamps();

            // Foreign Keys
            $table->foreign('maleta_id')
                ->references('id')->on('maletas')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->foreign('propietario_id')
                ->references('id')->on('users')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->foreign('responsable_id')
                ->references('id')->on('users')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
        });

        // 2. Tabla: entrega_maleta_detalles
        Schema::create('entrega_maleta_detalles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('entrega_maleta_id');
            $table->unsignedBigInteger('herramienta_id');
            
            $table->timestamps();

            // Foreign Keys
            $table->foreign('entrega_maleta_id')
                ->references('id')->on('entrega_maletas')
                ->cascadeOnUpdate()
                ->cascadeOnDelete(); // Si borras la entrega, se borran sus detalles

            $table->foreign('herramienta_id')
                ->references('id')->on('herramientas')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
        });

        // 3. TRIGGER: Auto-asignar propietario
        // Antes de insertar en entrega_maletas, busca el propietario actual de la maleta
        DB::unprepared('
            CREATE TRIGGER tr_entrega_maletas_before_insert 
            BEFORE INSERT ON entrega_maletas 
            FOR EACH ROW 
            BEGIN
                DECLARE current_owner_id BIGINT;
                
                -- Obtener el propietario actual de la maleta
                SELECT propietario_id INTO current_owner_id 
                FROM maletas 
                WHERE id = NEW.maleta_id;
                
                -- Asignarlo al campo propietario_id de la nueva fila
                SET NEW.propietario_id = current_owner_id;
            END
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Primero eliminamos el trigger
        DB::unprepared('DROP TRIGGER IF EXISTS tr_entrega_maletas_before_insert');
        
        Schema::dropIfExists('entrega_maleta_detalles');
        Schema::dropIfExists('entrega_maletas');
    }   
};
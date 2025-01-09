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
        Schema::create('evidencias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trabajo_id')
                ->constrained('trabajos')
                ->onDelete('restrict')
                ->onUpdate('cascade');
            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('restrict')
                ->onUpdate('cascade');
            $table->string('evidencia_url');
            $table->enum('tipo', ['imagen', 'video'])->default('video');
            $table->text('observacion')->nullable();
            $table->timestamps();
        });

        DB::statement("
            CREATE TRIGGER set_tipo_before_insert
            BEFORE INSERT ON evidencias
            FOR EACH ROW
            BEGIN
                IF LOWER(SUBSTRING_INDEX(NEW.evidencia_url, '.', -1)) IN ('jpg', 'jpeg', 'png', 'gif') THEN
                    SET NEW.tipo = 'imagen';
                ELSEIF LOWER(SUBSTRING_INDEX(NEW.evidencia_url, '.', -1)) IN ('mp4', 'webm', 'ogg') THEN
                    SET NEW.tipo = 'video';
                ELSE
                    SET NEW.tipo = 'video'; -- Valor predeterminado
                END IF;
            END;
        ");

        DB::statement("
            CREATE TRIGGER set_tipo_before_update
            BEFORE UPDATE ON evidencias
            FOR EACH ROW
            BEGIN
                IF LOWER(SUBSTRING_INDEX(NEW.evidencia_url, '.', -1)) IN ('jpg', 'jpeg', 'png', 'gif') THEN
                    SET NEW.tipo = 'imagen';
                ELSEIF LOWER(SUBSTRING_INDEX(NEW.evidencia_url, '.', -1)) IN ('mp4', 'webm', 'ogg') THEN
                    SET NEW.tipo = 'video';
                ELSE
                    SET NEW.tipo = 'video'; -- Valor predeterminado
                END IF;
            END;
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('DROP TRIGGER IF EXISTS set_tipo_before_insert');
        DB::statement('DROP TRIGGER IF EXISTS set_tipo_before_update');
        Schema::dropIfExists('evidencias');
    }
};

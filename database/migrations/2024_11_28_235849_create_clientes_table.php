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
        Schema::create('clientes', function (Blueprint $table) {
            $table->id();
            $table->string('identificador', 12)->unique()->nullable();
            $table->string('nombre');
            $table->string('telefono')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        DB::statement("
            ALTER TABLE clientes
                ADD COLUMN nombre_completo VARCHAR(255)
                GENERATED ALWAYS AS (
                    CASE
                        WHEN identificador IS NULL THEN nombre
                        ELSE CONCAT(identificador, ' - ', nombre)
                    END
                ) VIRTUAL;
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clientes');
    }
};

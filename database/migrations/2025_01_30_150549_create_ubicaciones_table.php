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
        Schema::create('ubicaciones', function (Blueprint $table) {
            $table->id();
            $table->string('estante', 5);
            $table->string('codigo', 5);
            $table->timestamps();
            $table->softDeletes();
        });

        DB::statement("
            ALTER TABLE ubicaciones
            ADD COLUMN nombre_completo VARCHAR(255)
            GENERATED ALWAYS AS (
                CONCAT(estante, ' - ', codigo)
            ) VIRTUAL;
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ubicaciones');
    }
};

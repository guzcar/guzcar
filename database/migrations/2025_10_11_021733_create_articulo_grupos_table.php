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
        Schema::create('articulo_grupos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('color');
            $table->timestamps();
        });

        Schema::table('articulos', function (Blueprint $table) {
            $table->foreignId('grupo_id')
                ->nullable()
                ->constrained('articulo_grupos')
                ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('articulos', function (Blueprint $table) {
            $table->dropForeign(['grupo_id']);
            $table->dropColumn('grupo_id');
        });

        Schema::dropIfExists('articulo_grupos');
    }
};

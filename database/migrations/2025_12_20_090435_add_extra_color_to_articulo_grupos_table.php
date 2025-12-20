<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('articulo_grupos', function (Blueprint $table) {

            // Hacer nullable la columna existente color
            $table
                ->string('color')
                ->nullable()
                ->change();

            // Nueva columna para mapear clases CSS
            $table
                ->string('extra_color', 50)
                ->nullable()
                ->after('color');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('articulo_grupos', function (Blueprint $table) {

            // Revertir color a NOT NULL (ajusta si antes tenía default)
            $table
                ->string('color')
                ->nullable(false)
                ->change();

            $table->dropColumn('extra_color');
        });
    }
};

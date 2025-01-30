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
            $table->string('nombre', 50);
            $table->foreignId('almacen_id')
                ->constrained('almacenes')
                ->onDelete('restrict')
                ->onUpdate('cascade');
            $table->timestamps();
        });

        Schema::table('articulos', function (Blueprint $table) {
            $table->bigInteger('ubicacion_id')
                ->unsigned()
                ->nullable()
                ->after('sub_categoria_id');
            $table->foreign('ubicacion_id')
                ->references('id')
                ->on('ubicaciones')
                ->onDelete('restrict')
                ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('articulos', function (Blueprint $table) {
            $table->dropForeign(['ubicacion_id']);
            $table->dropColumn('ubicacion_id');
        });

        Schema::dropIfExists('ubicacions');
    }
};

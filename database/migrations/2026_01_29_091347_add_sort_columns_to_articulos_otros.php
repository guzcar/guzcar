<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('trabajo_articulos', function (Blueprint $table) {
            $table->integer('orden_combinado')->default(0)->nullable();
        });

        Schema::table('trabajo_otros', function (Blueprint $table) {
            $table->integer('orden_combinado')->default(0)->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('trabajo_articulos', function (Blueprint $table) {
            $table->dropColumn('orden_combinado');
        });
        Schema::table('trabajo_otros', function (Blueprint $table) {
            $table->dropColumn('orden_combinado');
        });
    }
};

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
        Schema::table('herramienta_incidencias', function (Blueprint $table) {
            $table->text('evidencias')->nullable()->after('observacion');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('herramienta_incidencias', function (Blueprint $table) {
            $table->dropColumn('evidencias');
        });
    }
};

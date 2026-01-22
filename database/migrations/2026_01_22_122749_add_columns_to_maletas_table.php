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
        Schema::table('maletas', function (Blueprint $table) {
            $table->string('evidencia')->nullable()->after('propietario_id');
            $table->text('observacion')->nullable()->after('evidencia');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('maletas', function (Blueprint $table) {
            //
        });
    }
};

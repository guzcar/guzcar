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
        Schema::table('entrega_maletas', function (Blueprint $table) {
            $table->longText('firma_propietario')->nullable()->after('responsable_id');
            $table->longText('firma_responsable')->nullable()->after('firma_propietario');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('entrega_maletas', function (Blueprint $table) {
            $table->dropColumn(['firma_propietario', 'firma_responsable']);
        });
    }
};

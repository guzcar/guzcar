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
        Schema::table('evidencias', function (Blueprint $table) {
            DB::statement("ALTER TABLE evidencias MODIFY tipo ENUM('imagen', 'video', 'pdf', 'otro') DEFAULT 'imagen'");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('evidencias', function (Blueprint $table) {
            DB::statement("ALTER TABLE evidencias MODIFY tipo ENUM('imagen', 'video') DEFAULT 'video'");
        });
    }
};

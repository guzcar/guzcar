<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('implementos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre')->unique();
            $table->decimal('costo')->default(0);
            $table->unsignedInteger('stock')->default(0);
            $table->unsignedInteger('asignadas')->default(0);
            $table->unsignedInteger('mermas')->default(0);
            $table->unsignedInteger('perdidas')->default(0);
            $table->timestamps();
        });

        DB::statement("
            ALTER TABLE implementos
            ADD CONSTRAINT chk_implementos_nonneg
            CHECK (stock >= 0 AND asignadas >= 0 AND mermas >= 0 AND perdidas >= 0)
        ");
    }

    public function down(): void
    {
        try {
            DB::statement("ALTER TABLE implementos DROP CHECK chk_implementos_nonneg");
        } catch (\Throwable $e) {}
        Schema::dropIfExists('implementos');
    }
};
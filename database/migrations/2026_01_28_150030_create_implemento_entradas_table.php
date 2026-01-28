<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // 1. Cabecera
        Schema::create('implemento_entradas', function (Blueprint $table) {
            $table->id();
            $table->string('codigo');
            $table->dateTime('fecha');
            $table->text('observacion')->nullable();
            $table->unsignedBigInteger('responsable_id');
            $table->string('evidencia_url')->nullable();

            $table->unique('codigo', 'uk_ie_codigo');

            $table->foreign('responsable_id')
                ->references('id')->on('users')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->timestamps();
        });

        // 2. Detalles
        Schema::create('implemento_entrada_detalles', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('implemento_entrada_id');
            $table->unsignedBigInteger('implemento_id');
            $table->unsignedInteger('cantidad');
            $table->decimal('costo')->default(0);

            $table->index('implemento_entrada_id', 'idx_ied_ie');
            $table->index('implemento_id', 'idx_ied_i');

            $table->foreign('implemento_entrada_id')
                ->references('id')->on('implemento_entradas')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->foreign('implemento_id')
                ->references('id')->on('implementos')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->timestamps();
        });

        DB::statement("
            ALTER TABLE implemento_entrada_detalles
            ADD CONSTRAINT chk_ied_cantidad_gt_zero CHECK (cantidad > 0)
        ");
    }

    public function down(): void
    {
        Schema::dropIfExists('implemento_entrada_detalles');
        Schema::dropIfExists('implemento_entradas');
    }
};
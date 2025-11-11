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
        Schema::create('cotizacion_servicios', function (Blueprint $table) {
            $table->id();

            $table->foreignId('cotizacion_id')
                ->constrained('cotizaciones')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->string('descripcion');
            $table->unsignedInteger('cantidad')->default(1);

            $table->decimal('precio', 10, 2);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cotizacion_servicios');
    }
};

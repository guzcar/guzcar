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
        Schema::create('repuestos', function (Blueprint $table) {
            $table->id();
            $table->string('codigo')->unique();
            $table->integer('cantidad')->nullable();
            $table->string('nombre');

            $table->foreignId('categoria_id')
                ->constrained('categoria_repuestos')
                ->onDelete('restrict');

            $table->string('marca_modelo')->nullable();
            $table->string('motor')->nullable();
            $table->string('medidas_cod_oem')->nullable();
            $table->string('estado')->nullable();
            $table->text('notas')->nullable();
            $table->date('fecha')->nullable();

            $table->foreignId('tecnico_id')
                ->nullable()
                ->constrained('users')
                ->onDelete('restrict');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('repuestos');
    }
};

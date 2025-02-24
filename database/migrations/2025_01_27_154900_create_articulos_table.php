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
        Schema::create('articulos', function (Blueprint $table) {
            $table->id();
            $table->string('especificacion')->nullable();
            $table->string('marca');
            $table->string('tamano_presentacion');
            $table->string('color')->nullable();
            $table->text('descripcion')->nullable();
            $table->decimal('costo');
            $table->decimal('precio')->nullable();
            $table->foreignId('sub_categoria_id')
                ->constrained('sub_categorias')
                ->onDelete('restrict')
                ->onUpdate('cascade');
            $table->unsignedInteger('stock')->default(0);
            $table->decimal('abiertos')->unsigned()->default(0);
            $table->unsignedInteger('mermas')->default(0);
            $table->boolean('fraccionable')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('articulos');
    }
};

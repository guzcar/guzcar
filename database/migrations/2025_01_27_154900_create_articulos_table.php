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
            $table->foreignId('categoria_id')
                ->constrained('articulo_categorias')
                ->onDelete('restrict')
                ->onUpdate('cascade');
            $table->foreignId('marca_id')
                ->nullable()
                ->constrained('articulo_marcas')
                ->onDelete('restrict')
                ->onUpdate('cascade');
            $table->foreignId('sub_categoria_id')
                ->nullable()
                ->constrained('articulo_sub_categorias')
                ->onDelete('restrict')
                ->onUpdate('cascade');
            $table->string('especificacion')->nullable();
            $table->foreignId('presentacion_id')
                ->nullable()
                ->constrained('articulo_presentaciones')
                ->onDelete('restrict')
                ->onUpdate('cascade');
            $table->decimal('medida')->nullable();
            $table->foreignId('unidad_id')
                ->nullable()
                ->constrained('articulo_unidades')
                ->onDelete('restrict')
                ->onUpdate('cascade');
            $table->string('color')->nullable();
            $table->text('descripcion')->nullable();
            $table->decimal('costo');
            $table->decimal('precio')->nullable();
            $table->decimal('stock')->default(0);
            $table->decimal('abiertos')->default(0);
            $table->decimal('mermas')->default(0);
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

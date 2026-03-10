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
        Schema::create('importaciones', function (Blueprint $table) {
            $table->id();
            $table->string('archivo_nombre');
            $table->string('archivo_path');
            $table->timestamp('fecha_importacion');
            $table->timestamps();
        });

        Schema::create('registros_excel', function (Blueprint $table) {
            $table->id();
            $table->foreignId('importacion_id')->constrained('importaciones')->onDelete('cascade');
            $table->string('codigo')->nullable();
            $table->text('productos')->nullable();
            $table->string('clase_terapeutica')->nullable();
            $table->string('cliente')->nullable();
            $table->string('clase')->nullable();
            $table->integer('mes')->nullable();
            $table->integer('ano')->nullable();
            $table->decimal('unidades', 15, 3)->nullable();
            $table->timestamps();
            
            $table->index('importacion_id');
            $table->index('cliente');
            $table->index(['ano', 'mes']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('registros_excel');
        Schema::dropIfExists('importaciones');
    }
};

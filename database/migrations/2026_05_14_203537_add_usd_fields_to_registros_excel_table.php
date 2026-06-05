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
        Schema::table('registros_excel', function (Blueprint $table) {
            $table->decimal('tasa', 15, 3)->nullable();
            $table->decimal('valor_usd', 15, 3)->nullable();
            $table->decimal('valor_bs', 15, 3)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('registros_excel', function (Blueprint $table) {
            $table->dropColumn(['tasa', 'valor_usd', 'valor_bs']);
        });
    }
};

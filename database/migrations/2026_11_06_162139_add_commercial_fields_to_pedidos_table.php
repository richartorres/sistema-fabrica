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
        Schema::table('pedidos', function (Blueprint $table) {
            $table->foreignId('vendedor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('fecha_entrega_prometida')->nullable();
            $table->text('notas_produccion')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('pedidos', function (Blueprint $table) {
            $table->dropForeign(['vendedor_id']);
            $table->dropColumn(['vendedor_id', 'fecha_entrega_prometida', 'notas_produccion']);
        });
    }
};

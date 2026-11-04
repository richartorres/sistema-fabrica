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
    Schema::create('compras', function (Blueprint $table) {
        $table->id();
        $table->foreignId('proveedor_id')->constrained('proveedors')->onDelete('cascade');
        $table->decimal('total', 12, 2)->default(0);
        $table->decimal('abono', 12, 2)->default(0);
        $table->decimal('saldo_pendiente', 12, 2)->default(0);
        $table->enum('estado', ['pendiente', 'completado'])->default('pendiente');
        $table->date('fecha_compra');
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('compras');
    }
};

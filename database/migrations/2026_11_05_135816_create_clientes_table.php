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
    Schema::create('clientes', function (Blueprint $table) {
        $table->id();
        $table->string('nombre');
        $table->string('documento')->nullable()->unique(); // Cédula o NIT
        $table->string('telefono')->nullable();
        $table->string('direccion')->nullable();
        $table->enum('tipo', ['cliente_final', 'distribuidor', 'tienda_aliada'])->default('cliente_final');
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clientes');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reglas_honorarios', function (Blueprint $table) {
            $table->id();
            $table->decimal('umbral', 10, 2)->unique();
            $table->decimal('porcentaje', 5, 2); // lo que se lleva el médico, ej: 70 = 70%
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reglas_honorarios');
    }
};

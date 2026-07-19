<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('viajes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('ciudad_id')->constrained('ciudades')->cascadeOnDelete();
            $table->string('titulo');
            $table->text('descripcion');
            $table->string('imagen')->nullable();
            $table->date('fecha_salida');
            $table->unsignedSmallInteger('duracion_dias');
            $table->decimal('precio', 8, 2);
            $table->unsignedSmallInteger('plazas')->nullable();
            $table->string('contacto'); // WhatsApp, email o URL
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('viajes');
    }
};

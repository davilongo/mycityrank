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
        Schema::table('ciudades', function (Blueprint $table) {
            $table->string('pais')->nullable()->after('nombre');
            $table->dropUnique(['nombre']);
            $table->unique(['nombre', 'pais']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ciudades', function (Blueprint $table) {
            $table->dropUnique(['nombre', 'pais']);
            $table->unique(['nombre']);
            $table->dropColumn('pais');
        });
    }
};

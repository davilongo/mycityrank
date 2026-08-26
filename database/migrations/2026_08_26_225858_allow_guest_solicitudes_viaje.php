<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('solicitudes_viaje', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });

        DB::statement('ALTER TABLE solicitudes_viaje MODIFY user_id BIGINT UNSIGNED NULL');

        Schema::table('solicitudes_viaje', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
            $table->string('contacto')->nullable()->after('ciudad_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('solicitudes_viaje', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn('contacto');
        });

        DB::statement('ALTER TABLE solicitudes_viaje MODIFY user_id BIGINT UNSIGNED NOT NULL');

        Schema::table('solicitudes_viaje', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')->cascadeOnDelete();
        });
    }
};

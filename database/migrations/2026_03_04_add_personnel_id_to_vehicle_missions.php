<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('vehicle_missions', function (Blueprint $table) {
            // Ajouter la colonne personnel_id si elle n'existe pas
            if (!Schema::hasColumn('vehicle_missions', 'personnel_id')) {
                $table->foreignId('personnel_id')
                    ->nullable()
                    ->constrained('personnel')
                    ->nullOnDelete()
                    ->after('driver_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('vehicle_missions', function (Blueprint $table) {
            if (Schema::hasColumn('vehicle_missions', 'personnel_id')) {
                $table->dropForeignKeyIfExists(['personnel_id']);
                $table->dropColumn('personnel_id');
            }
        });
    }
};

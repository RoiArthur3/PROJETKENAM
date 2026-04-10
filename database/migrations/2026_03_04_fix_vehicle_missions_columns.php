<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('vehicle_missions')) {
            Schema::table('vehicle_missions', function (Blueprint $table) {
                // Ajouter les colonnes attendues par le contrôleur si elles n'existent pas
                if (!Schema::hasColumn('vehicle_missions', 'user_id')) {
                    $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                }
                
                if (!Schema::hasColumn('vehicle_missions', 'objective')) {
                    $table->text('objective')->nullable();
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('vehicle_missions')) {
            Schema::table('vehicle_missions', function (Blueprint $table) {
                if (Schema::hasColumn('vehicle_missions', 'user_id')) {
                    $table->dropForeign(['user_id']);
                    $table->dropColumn('user_id');
                }
                
                if (Schema::hasColumn('vehicle_missions', 'objective')) {
                    $table->dropColumn('objective');
                }
            });
        }
    }
};

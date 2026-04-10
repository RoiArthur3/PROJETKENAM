<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ecritures_comptables', function (Blueprint $table) {
            if (!Schema::hasColumn('ecritures_comptables', 'piece_jointe')) {
                $table->string('piece_jointe')->nullable()->after('piece_comptable')
                    ->comment('Chemin du fichier justificatif (obligatoire à la création)');
            }
            if (!Schema::hasColumn('ecritures_comptables', 'piece_jointe_nom')) {
                $table->string('piece_jointe_nom')->nullable()->after('piece_jointe')
                    ->comment('Nom original du fichier uploadé');
            }
        });
    }

    public function down(): void
    {
        Schema::table('ecritures_comptables', function (Blueprint $table) {
            $table->dropColumn(['piece_jointe', 'piece_jointe_nom']);
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vehicules', function (Blueprint $table) {
            if (!Schema::hasColumn('vehicules', 'prix_location')) {
                $table->decimal('prix_location', 12, 2)->nullable()->after('disponible');
            }
            if (!Schema::hasColumn('vehicules', 'date_debut_contrat')) {
                $table->date('date_debut_contrat')->nullable()->after('prix_location');
            }
        });
    }

    public function down(): void
    {
        Schema::table('vehicules', function (Blueprint $table) {
            $table->dropColumn(['prix_location', 'date_debut_contrat']);
        });
    }
};

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
        Schema::table('services_operationnels', function (Blueprint $table) {
            // Vérifier si la colonne existe avant de la supprimer
            if (Schema::hasColumn('services_operationnels', 'responsable_id')) {
                // Supprimer la clé étrangère si elle existe
                $table->dropForeign(['responsable_id']);
                // Supprimer la colonne
                $table->dropColumn('responsable_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Dans le cas où vous auriez besoin d'annuler cette migration
        Schema::table('services_operationnels', function (Blueprint $table) {
            if (!Schema::hasColumn('services_operationnels', 'responsable_id')) {
                $table->unsignedBigInteger('responsable_id')->nullable()->after('responsable');
                // Note: La clé étrangère ne peut pas être recréée ici car nous n'avons pas les informations sur la table de référence
            }
        });
    }
};

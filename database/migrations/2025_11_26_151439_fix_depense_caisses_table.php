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
        // Vérifier si la colonne created_by existe
        if (!Schema::hasColumn('depense_caisses', 'created_by')) {
            Schema::table('depense_caisses', function (Blueprint $table) {
                $table->foreignId('created_by')->constrained('users')->onDelete('restrict');
            });
        }

        // Ajouter beneficiaire_id seulement si created_by existe
        if (!Schema::hasColumn('depense_caisses', 'beneficiaire_id')) {
            Schema::table('depense_caisses', function (Blueprint $table) {
                $table->unsignedBigInteger('beneficiaire_id')->nullable()->after('created_by');
                $table->foreign('beneficiaire_id')
                    ->references('id')
                    ->on('users')
                    ->onDelete('set null');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('depense_caisses', function (Blueprint $table) {
            $table->dropForeign(['beneficiaire_id']);
            $table->dropColumn('beneficiaire_id');
        });
    }
};

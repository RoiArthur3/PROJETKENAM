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
        Schema::table('depense_caisses', function (Blueprint $table) {
            // Ajouter le statut d'encaissement pour les chèques (si les colonnes n'existent pas)
            if (!Schema::hasColumn('depense_caisses', 'est_encaisse')) {
                $table->boolean('est_encaisse')->default(false)->comment('Indique si le chèque est encaissé')->after('numero_cheque');
            }
            if (!Schema::hasColumn('depense_caisses', 'date_encaissement')) {
                $table->dateTime('date_encaissement')->nullable()->comment('Date d\'encaissement du chèque')->after('est_encaisse');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('depense_caisses', function (Blueprint $table) {
            $table->dropColumn(['est_encaisse', 'date_encaissement']);
        });
    }
};

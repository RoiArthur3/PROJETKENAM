<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('operations', function (Blueprint $table) {
            // Caisse choisie par la comptabilité pour exécuter le paiement
            if (!Schema::hasColumn('operations', 'caisse_executante_id')) {
                $table->unsignedBigInteger('caisse_executante_id')->nullable();
            }
            // Email de la caisse destinataire
            if (!Schema::hasColumn('operations', 'caisse_email')) {
                $table->string('caisse_email')->nullable();
            }
            // Référence du paiement (numéro de reçu, chèque, virement, etc.)
            if (!Schema::hasColumn('operations', 'payment_reference')) {
                $table->string('payment_reference')->nullable();
            }
            // Mode de paiement utilisé par la caisse
            if (!Schema::hasColumn('operations', 'mode_paiement')) {
                $table->string('mode_paiement')->nullable();
            }
            // Date à laquelle la comptabilité a envoyé le BON POUR ACCORD à la caisse
            if (!Schema::hasColumn('operations', 'bon_pour_accord_at')) {
                $table->timestamp('bon_pour_accord_at')->nullable();
            }
            // Qui a émis le BON POUR ACCORD (comptable/trésorier)
            if (!Schema::hasColumn('operations', 'bon_pour_accord_by')) {
                $table->unsignedBigInteger('bon_pour_accord_by')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('operations', function (Blueprint $table) {
            if (Schema::hasColumn('operations', 'caisse_executante_id')) {
                $table->dropColumn('caisse_executante_id');
            }
            if (Schema::hasColumn('operations', 'caisse_email')) {
                $table->dropColumn('caisse_email');
            }
            if (Schema::hasColumn('operations', 'payment_reference')) {
                $table->dropColumn('payment_reference');
            }
            if (Schema::hasColumn('operations', 'mode_paiement')) {
                $table->dropColumn('mode_paiement');
            }
            if (Schema::hasColumn('operations', 'bon_pour_accord_at')) {
                $table->dropColumn('bon_pour_accord_at');
            }
            if (Schema::hasColumn('operations', 'bon_pour_accord_by')) {
                $table->dropColumn('bon_pour_accord_by');
            }
        });
    }
};

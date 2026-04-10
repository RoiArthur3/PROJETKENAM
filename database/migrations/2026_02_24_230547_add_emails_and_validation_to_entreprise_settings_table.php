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
        Schema::table('entreprise_settings', function (Blueprint $table) {
            $table->string('email_tresorerie')->nullable()->after('email_support');
            $table->string('email_caisse_1')->nullable()->after('email_tresorerie');
            $table->string('email_caisse_2')->nullable()->after('email_caisse_1');
            $table->string('email_destinataire_principal')->nullable()->after('email_caisse_2');
            $table->string('email_dg')->nullable()->after('email_destinataire_principal');
            $table->decimal('seuil_validation_dg', 15, 2)->default(1000000)->after('email_dg');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('entreprise_settings', function (Blueprint $table) {
            $table->dropColumn([
                'email_tresorerie',
                'email_caisse_1',
                'email_caisse_2',
                'email_destinataire_principal',
                'email_dg',
                'seuil_validation_dg'
            ]);
        });
    }
};

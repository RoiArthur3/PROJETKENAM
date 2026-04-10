<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('bank_accounts')) {
            return;
        }

        // Alignement du schéma avec le module "banques-caisses" (type=banque/caisse)
        // On utilise du SQL brut pour éviter la dépendance doctrine/dbal.
        DB::statement("ALTER TABLE `bank_accounts` MODIFY `numero_compte` VARCHAR(255) NULL");
        DB::statement("ALTER TABLE `bank_accounts` MODIFY `banque` VARCHAR(255) NULL");
        DB::statement("ALTER TABLE `bank_accounts` MODIFY `type` ENUM('banque','caisse') NOT NULL DEFAULT 'banque'");
    }

    public function down(): void
    {
        if (!Schema::hasTable('bank_accounts')) {
            return;
        }

        // Restauration (au plus proche de la migration d'origine)
        DB::statement("ALTER TABLE `bank_accounts` MODIFY `numero_compte` VARCHAR(255) NOT NULL");
        DB::statement("ALTER TABLE `bank_accounts` MODIFY `banque` VARCHAR(255) NOT NULL");
        DB::statement("ALTER TABLE `bank_accounts` MODIFY `type` ENUM('courant','epargne','entreprise') NOT NULL DEFAULT 'courant'");
    }
};

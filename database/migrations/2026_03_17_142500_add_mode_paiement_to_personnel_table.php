<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('personnel')) {
            return;
        }

        Schema::table('personnel', function (Blueprint $table) {
            if (!Schema::hasColumn('personnel', 'mode_paiement')) {
                $table->enum('mode_paiement', ['VIREMENT', 'ESPECE', 'CHEQUE', 'MOBILE_MONEY'])->nullable()->after('devise');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('personnel')) {
            return;
        }

        Schema::table('personnel', function (Blueprint $table) {
            if (Schema::hasColumn('personnel', 'mode_paiement')) {
                $table->dropColumn('mode_paiement');
            }
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('factures')) {
            return;
        }

        Schema::table('factures', function (Blueprint $table) {
            if (!Schema::hasColumn('factures', 'mission')) {
                $table->string('mission')->nullable();
            }

            if (!Schema::hasColumn('factures', 'acompte_paye')) {
                $table->decimal('acompte_paye', 15, 2)->nullable();
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('factures')) {
            return;
        }

        Schema::table('factures', function (Blueprint $table) {
            if (Schema::hasColumn('factures', 'mission')) {
                $table->dropColumn('mission');
            }

            if (Schema::hasColumn('factures', 'acompte_paye')) {
                $table->dropColumn('acompte_paye');
            }
        });
    }
};

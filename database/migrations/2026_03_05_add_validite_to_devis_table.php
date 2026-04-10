<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('devis')) {
            Schema::table('devis', function (Blueprint $table) {
                if (!Schema::hasColumn('devis', 'validite')) {
                    $table->date('validite')->nullable()->after('due_date')->comment('Date de validité/expiration du devis');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('devis')) {
            Schema::table('devis', function (Blueprint $table) {
                if (Schema::hasColumn('devis', 'validite')) {
                    $table->dropColumn('validite');
                }
            });
        }
    }
};

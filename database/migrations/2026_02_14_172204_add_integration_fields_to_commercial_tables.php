<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('devis', function (Blueprint $table) {
            if (!Schema::hasColumn('devis', 'vehicule_id')) {
                $table->unsignedBigInteger('vehicule_id')->nullable()->after('client_id');
            }
        });

        Schema::table('factures', function (Blueprint $table) {
            if (!Schema::hasColumn('factures', 'devis_id')) {
                $table->unsignedBigInteger('devis_id')->nullable()->after('client_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('devis', function (Blueprint $table) {
            if (Schema::hasColumn('devis', 'vehicule_id')) {
                $table->dropColumn('vehicule_id');
            }
        });

        Schema::table('factures', function (Blueprint $table) {
            if (Schema::hasColumn('factures', 'devis_id')) {
                $table->dropColumn('devis_id');
            }
        });
    }
};

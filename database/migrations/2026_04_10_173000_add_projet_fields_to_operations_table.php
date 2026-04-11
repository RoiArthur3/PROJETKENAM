<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('operations', function (Blueprint $table) {
            $table->decimal('cout_estimatif', 10, 2)->nullable()->after('montant')->comment('Coût estimatif du projet (non obligatoire)');
            $table->decimal('montant_facturer', 10, 2)->nullable()->after('cout_estimatif')->comment('Montant à facturer au client');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('operations', function (Blueprint $table) {
            $table->dropColumn(['cout_estimatif', 'montant_facturer']);
        });
    }
};

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
        if (Schema::hasTable('recettes')) {
            Schema::table('recettes', function (Blueprint $table) {
                if (!Schema::hasColumn('recettes', 'encaissement_id')) {
                    $table->foreignId('encaissement_id')->nullable()->constrained('encaissements')->nullOnDelete();
                }
                if (!Schema::hasColumn('recettes', 'payment_id')) {
                    $table->foreignId('payment_id')->nullable()->constrained('payments')->nullOnDelete();
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('recettes', function (Blueprint $table) {
            $table->dropForeign(['encaissement_id']);
            $table->dropForeign(['payment_id']);
            $table->dropColumn(['encaissement_id', 'payment_id']);
        });
    }
};

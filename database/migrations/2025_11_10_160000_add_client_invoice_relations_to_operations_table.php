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
        Schema::table('operations', function (Blueprint $table) {
            // Relations avec clients et factures
            $table->foreignId('client_id')->nullable()->after('id')->constrained('clients')->onDelete('set null');
            $table->foreignId('invoice_id')->nullable()->after('client_id')->constrained('invoices')->onDelete('set null');
            
            // Index pour optimiser les performances
            $table->index('client_id');
            $table->index('invoice_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('operations', function (Blueprint $table) {
            $table->dropForeign(['client_id']);
            $table->dropForeign(['invoice_id']);
            $table->dropIndex(['client_id']);
            $table->dropIndex(['invoice_id']);
            $table->dropColumn(['client_id', 'invoice_id']);
        });
    }
};

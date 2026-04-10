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
        Schema::table('encaissements', function (Blueprint $table) {
            if (!Schema::hasColumn('encaissements', 'invoice_id')) {
                $table->foreignId('invoice_id')->nullable()->constrained('invoices')->nullOnDelete();
            }
            if (!Schema::hasColumn('encaissements', 'operation_id')) {
                $table->foreignId('operation_id')->nullable()->constrained('operations')->nullOnDelete();
            }
            if (!Schema::hasColumn('encaissements', 'project_id')) {
                $table->foreignId('project_id')->nullable()->constrained('projects')->nullOnDelete();
            }
            if (!Schema::hasColumn('encaissements', 'client_id')) {
                $table->foreignId('client_id')->nullable()->constrained('clients')->nullOnDelete();
            }
            if (!Schema::hasColumn('encaissements', 'fournisseur_id')) {
                $table->foreignId('fournisseur_id')->nullable()->constrained('fournisseurs')->nullOnDelete();
            }
            if (!Schema::hasColumn('encaissements', 'reference_externe')) {
                $table->string('reference_externe')->nullable();
            }
            if (!Schema::hasColumn('encaissements', 'notes')) {
                $table->text('notes')->nullable();
            }
            if (!Schema::hasColumn('encaissements', 'piece_jointe')) {
                $table->string('piece_jointe')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('encaissements', function (Blueprint $table) {
            $table->dropForeign(['invoice_id']);
            $table->dropForeign(['operation_id']);
            $table->dropForeign(['project_id']);
            $table->dropForeign(['client_id']);
            $table->dropForeign(['fournisseur_id']);
            $table->dropColumn(['invoice_id', 'operation_id', 'project_id', 'client_id', 'fournisseur_id', 'reference_externe', 'notes', 'piece_jointe']);
        });
    }
};

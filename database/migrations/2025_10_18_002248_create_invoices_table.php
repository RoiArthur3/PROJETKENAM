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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->unique(); // Ex: FAC-2025-0012
            $table->enum('type', ['client', 'supplier', 'internal']); // Client, Fournisseur, Interne
            $table->foreignId('client_id')->nullable()->constrained('users'); // Pour les clients (si applicable)
            $table->string('supplier_name')->nullable(); // Pour les fournisseurs externes
            $table->string('operation_reference')->nullable(); // Référence à une opération
            $table->text('description'); // Description de la prestation
            $table->decimal('quantity', 10, 2)->default(1);
            $table->decimal('unit_price', 15, 2);
            $table->decimal('total_amount', 15, 2);
            $table->decimal('tax_rate', 5, 2)->default(18); // TVA par défaut
            $table->decimal('tax_amount', 15, 2)->default(0);
            $table->decimal('discount', 15, 2)->default(0);
            $table->decimal('net_amount', 15, 2);
            $table->enum('payment_method', ['cash', 'bank_transfer', 'check', 'mobile_money', 'other'])->default('bank_transfer');
            $table->enum('status', ['draft', 'issued', 'paid', 'cancelled'])->default('draft');
            $table->date('due_date');
            $table->date('issue_date')->nullable();
            $table->text('notes')->nullable();
            $table->string('attachment')->nullable(); // Chemin vers le PDF
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('ecritures_comptables')) {
            Schema::create('ecritures_comptables', function (Blueprint $table) {
                $table->id();
                $table->foreignId('journal_id')->nullable()->constrained('journal_comptables')->nullOnDelete();
                $table->date('date');
                $table->string('reference', 100);
                $table->string('piece_comptable', 100)->nullable();
                $table->string('libelle');
                $table->string('compte_debit', 50);
                $table->string('compte_credit', 50);
                $table->decimal('montant', 15, 2);
                $table->text('description')->nullable();
                $table->string('source_type', 50)->default('manual');
                $table->unsignedBigInteger('source_id')->nullable();
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
                $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();

                $table->index(['date', 'journal_id']);
                $table->index(['source_type', 'source_id']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('ecritures_comptables');
    }
};
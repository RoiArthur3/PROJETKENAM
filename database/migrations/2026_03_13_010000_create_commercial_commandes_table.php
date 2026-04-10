<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('commercial_commandes')) {
            return;
        }

        Schema::create('commercial_commandes', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique();

            $table->unsignedBigInteger('client_id')->nullable();

            $table->string('type_engin');
            $table->unsignedInteger('quantite')->default(1);

            $table->string('lieu')->nullable();
            $table->date('date_debut')->nullable();
            $table->date('date_fin')->nullable();

            $table->decimal('budget', 15, 2)->nullable();
            $table->text('commentaire')->nullable();

            $table->string('email_service');
            $table->string('statut')->default('brouillon');

            $table->timestamp('sent_at')->nullable();
            $table->timestamp('responded_at')->nullable();
            $table->timestamp('validated_at')->nullable();

            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('validated_by')->nullable();

            $table->timestamps();

            $table->index(['statut']);
            $table->index(['client_id']);

            $table->foreign('client_id')
                ->references('id')
                ->on('clients')
                ->nullOnDelete();

            $table->foreign('created_by')
                ->references('id')
                ->on('users')
                ->nullOnDelete();

            $table->foreign('validated_by')
                ->references('id')
                ->on('users')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('commercial_commandes');
    }
};

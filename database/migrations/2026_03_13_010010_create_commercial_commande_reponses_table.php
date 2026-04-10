<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('commercial_commande_reponses')) {
            return;
        }

        Schema::create('commercial_commande_reponses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('commande_id');

            $table->text('commentaire')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();

            $table->timestamps();

            $table->index(['commande_id']);

            $table->foreign('commande_id')
                ->references('id')
                ->on('commercial_commandes')
                ->onDelete('cascade');

            $table->foreign('created_by')
                ->references('id')
                ->on('users')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('commercial_commande_reponses');
    }
};

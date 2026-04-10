<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('heures_sup', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->date('date');
            $table->time('heure_debut')->nullable();
            $table->time('heure_fin')->nullable();
            $table->unsignedInteger('duree_minutes')->nullable();
            $table->string('type')->nullable();
            $table->string('service')->nullable();
            $table->unsignedInteger('majoration')->nullable();
            $table->unsignedBigInteger('montant')->nullable();
            $table->string('statut')->default('en_attente');
            $table->text('commentaire')->nullable();
            $table->foreignId('validated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('validated_at')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('heures_sup');
    }
};

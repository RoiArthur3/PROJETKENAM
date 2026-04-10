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
        if (Schema::hasTable('visites_techniques')) {
            return;
        }

        Schema::create('visites_techniques', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->constrained('vehicles')->onDelete('cascade');
            $table->date('date_visite');
            $table->date('date_expiration');
            $table->string('centre_visite')->nullable();
            $table->string('numero_certificat')->nullable();
            $table->decimal('cout', 12, 2)->default(0);
            $table->string('statut')->default('VALIDE'); // VALIDE, EXPRIME, EN_ATTENTE
            $table->text('commentaires')->nullable();
            $table->string('piece_jointe')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visites_techniques');
    }
};

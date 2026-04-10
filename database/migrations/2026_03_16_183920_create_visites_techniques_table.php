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
        Schema::create('visites_techniques', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vehicle_id')->nullable()->constrained('vehicles')->onDelete('cascade');
            $table->date('date_visite');
            $table->date('date_expiration');
            $table->string('centre_visite')->nullable();
            $table->string('numero_certificat')->nullable();
            $table->decimal('cout', 10, 2)->nullable();
            $table->string('statut')->default('valide');
            $table->text('commentaires')->nullable();
            $table->string('piece_jointe')->nullable();
            $table->timestamps();

            $table->index(['date_expiration', 'statut']);
            $table->index('vehicle_id');
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

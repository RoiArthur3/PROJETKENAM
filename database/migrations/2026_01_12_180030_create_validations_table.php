

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
        if (!Schema::hasTable('validations')) {
            Schema::create('validations', function (Blueprint $table) {
                $table->id();
                $table->string('titre');
                $table->text('description')->nullable();
                $table->enum('statut', ['en_attente', 'en_cours', 'approuve', 'rejete'])->default('en_attente');
                $table->foreignId('initiateur_id')->constrained('users');
                $table->foreignId('validateur_id')->nullable()->constrained('users');
                $table->text('commentaire')->nullable();
                $table->timestamp('date_validation')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('validations');
    }
};

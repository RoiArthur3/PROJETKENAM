<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_operations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('project_id');
            $table->unsignedBigInteger('operation_id');
            $table->decimal('montant_alloue', 15, 2)->nullable(); // Budget alloué pour cette opération
            $table->decimal('montant_consomme', 15, 2)->default(0); // Montant réellement dépensé
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
            $table->foreign('operation_id')->references('id')->on('operations')->onDelete('cascade');
            $table->unique(['project_id', 'operation_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_operations');
    }
};

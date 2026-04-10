<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_milestones', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('project_id');
            $table->string('titre');
            $table->text('description')->nullable();
            $table->date('date_planifiee');
            $table->date('date_reelle')->nullable();
            $table->integer('pourcentage_completion')->default(0);
            $table->enum('statut', ['planifiee', 'en_cours', 'completee', 'retardee'])->default('planifiee');
            $table->text('remarques')->nullable();
            $table->timestamps();

            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_milestones');
    }
};

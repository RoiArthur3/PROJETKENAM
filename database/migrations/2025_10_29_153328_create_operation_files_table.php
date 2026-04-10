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
        Schema::create('operation_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('operation_id')->constrained()->onDelete('cascade');
            $table->string('nom');
            $table->string('chemin');
            $table->string('type_mime');
            $table->unsignedBigInteger('taille');
            $table->string('extension');
            $table->text('description')->nullable();
            $table->foreignId('uploaded_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
            
            // Index pour les recherches fréquentes
            $table->index(['operation_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('operation_files', function (Blueprint $table) {
            $table->dropForeign(['operation_id']);
            $table->dropForeign(['uploaded_by']);
        });
        
        Schema::dropIfExists('operation_files');
    }
};

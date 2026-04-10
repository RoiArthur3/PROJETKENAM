<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        if (!Schema::hasTable('comptes_comptables')) {
            Schema::create('comptes_comptables', function (Blueprint $table) {
                $table->id();
                $table->string('numero_compte', 20)->unique();
                $table->string('intitule');
                $table->text('description')->nullable();
                $table->enum('type', ['actif', 'passif', 'charge', 'produit']);
                $table->boolean('actif')->default(true);
                $table->foreignId('parent_id')->nullable()->constrained('comptes_comptables')->onDelete('set null');
                $table->timestamps();
                $table->softDeletes();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('comptes_comptables');
    }
};

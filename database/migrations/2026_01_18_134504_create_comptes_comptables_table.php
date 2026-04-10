<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('comptes_comptables')) {
            Schema::create('comptes_comptables', function (Blueprint $table) {
                $table->id();
                $table->string('numero_compte')->unique();
                $table->string('libelle');
                $table->text('description')->nullable();
                $table->enum('type', ['actif', 'passif', 'charge', 'produit']);
                $table->timestamps();
                $table->softDeletes();
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('comptes_comptables');
    }
};
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCategorieFournisseursTable extends Migration
{
    public function up()
    {
        Schema::create('categorie_fournisseurs', function (Blueprint $table) {
            $table->id();
            $table->string('nom', 100);
            $table->string('description', 500)->nullable();
            $table->string('couleur', 20)->default('#6c757d');
            $table->boolean('est_actif')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('categorie_fournisseurs');
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('justificatifs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('depense_id')->constrained('depenses');
            $table->string('fichier');
            $table->string('type_fichier', 50);
            $table->string('nom_original');
            $table->foreignId('valide_par')->nullable()->constrained('users');
            $table->timestamp('date_validation')->nullable();
            $table->text('commentaires')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('justificatifs');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('depenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('approvisionnement_id')->nullable();
            $table->foreignId('caisse_id')->nullable();
            $table->decimal('montant', 15, 2);
            $table->string('libelle');
            $table->foreignId('compte_comptable_id')->nullable();
            $table->foreignId('created_by')->nullable();
            $table->date('date_depense');
            $table->boolean('est_justifie')->default(false);
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('depenses');
    }
};

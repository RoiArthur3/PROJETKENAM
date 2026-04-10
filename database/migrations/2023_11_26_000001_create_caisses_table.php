<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('caisses', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->enum('type', ['principale', 'secondaire']);
            $table->decimal('solde_initial', 15, 2)->default(0);
            $table->decimal('solde_actuel', 15, 2)->default(0);
            $table->string('devise', 10)->default('FCFA');
            $table->foreignId('responsable_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('description')->nullable();
            $table->boolean('est_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists('caisses');
    }
};

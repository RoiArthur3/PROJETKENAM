<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('types_operations', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique();
            $table->string('libelle', 100);
            $table->text('description')->nullable();
            $table->string('couleur', 20)->nullable()->comment('Ex: primary, success, danger, warning, info');
            $table->string('icone', 50)->nullable()->comment('Ex: fa-truck, fa-box, fa-file');
            $table->boolean('actif')->default(true);
            $table->timestamps();
            $table->index('actif');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('types_operations');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('operation_costs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('operation_id')->constrained('operations')->onDelete('cascade');
            $table->string('type'); // depense|facture
            $table->string('description');
            $table->decimal('montant', 15, 2)->default(0);
            $table->string('fournisseur')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('operation_costs');
    }
};

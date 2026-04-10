<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('operation_services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('operation_id')->constrained('operations')->onDelete('cascade');
            $table->string('service_name');
            $table->string('service_email');
            $table->unsignedInteger('ordre');
            $table->string('statut')->default('waiting'); // waiting|pending|approved|rejected
            $table->string('valide_par')->nullable();
            $table->timestamp('valide_le')->nullable();
            $table->text('commentaire')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('operation_services');
    }
};

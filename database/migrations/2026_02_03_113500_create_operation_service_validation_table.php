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
        if (!Schema::hasTable('operation_service_validation')) {
            Schema::create('operation_service_validation', function (Blueprint $table) {
                $table->id();
                $table->foreignId('operation_id')->constrained()->onDelete('cascade');
                $table->foreignId('service_operationnel_id')->constrained('services_operationnels')->onDelete('cascade');
                $table->integer('ordre_validation'); // 1, 2, 3...
                $table->enum('statut', ['EN_ATTENTE', 'EN_COURS', 'APPROUVE', 'REJETE'])->default('EN_ATTENTE');
                $table->text('commentaire')->nullable();
                $table->timestamp('date_validation')->nullable();

                $table->foreignId('validateur_id')->nullable()->constrained('users')->onDelete('set null');
                $table->string('signature_path')->nullable();
                $table->timestamps();

                $table->unique(['operation_id', 'service_operationnel_id'], 'op_srv_val_unique');
                $table->index(['operation_id', 'ordre_validation']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('operation_service_validation');
    }
};

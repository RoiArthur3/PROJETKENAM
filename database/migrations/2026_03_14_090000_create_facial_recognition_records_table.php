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
        Schema::create('facial_recognition_records', function (Blueprint $table) {
            $table->id();
            
            // Informations sur le device et l'événement
            $table->foreignId('device_id')->constrained('facial_devices')->onDelete('cascade');
            $table->string('device_serial', 100)->nullable();
            $table->string('event_type', 50)->default('facial_recognition');
            $table->string('direction', 20)->nullable(); // entry, exit, unknown
            $table->string('door_name', 100)->nullable();
            
            // Informations sur l'employé reconnu
            $table->foreignId('personnel_id')->nullable()->constrained('personnel')->onDelete('set null');
            $table->string('employee_code', 50)->nullable();
            $table->string('employee_name', 100)->nullable();
            $table->text('photo_base64')->nullable(); // Photo capturée par la caméra
            $table->string('face_id', 100)->nullable(); // ID du visage dans le système Hikvision
            
            // Scores et métadonnées
            $table->decimal('confidence_score', 5, 3)->nullable(); // Score de confiance 0.000-1.000
            $table->json('camera_metadata')->nullable(); // Métadonnées supplémentaires
            $table->string('status', 50)->default('pending'); // pending, recognized, unknown, error
            
            // Timestamps
            $table->timestamp('recognition_time')->nullable(); // Heure exacte de reconnaissance
            $table->timestamps();
            
            // Synchronisation avec la caméra
            $table->enum('sync_status', ['pending', 'syncing', 'synced', 'failed'])->default('pending');
            $table->text('sync_error')->nullable();
            $table->timestamp('synced_at')->nullable();
            
            // Indexes
            $table->index(['device_id', 'recognition_time']);
            $table->index(['personnel_id', 'recognition_time']);
            $table->index(['employee_code', 'recognition_time']);
            $table->index('sync_status');
            $table->index('recognition_time');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('facial_recognition_records');
    }
};

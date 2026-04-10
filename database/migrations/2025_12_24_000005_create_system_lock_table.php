<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('system_locks', function (Blueprint $table) {
            $table->id();
            $table->string('lock_type'); // 'full_access', 'module_access', 'user_access'
            $table->string('target')->nullable(); // module_code, user_id, or null for full system
            $table->boolean('is_locked')->default(false);
            $table->text('reason')->nullable();
            $table->foreignId('locked_by')->constrained('users')->onDelete('cascade');
            $table->timestamp('locked_at')->nullable();
            $table->timestamp('unlocked_at')->nullable();
            $table->foreignId('unlocked_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();

            $table->index(['lock_type', 'target']);
            $table->index('is_locked');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('system_locks');
    }
};

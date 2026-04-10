<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('warehouses')) {
            return; // already present in this environment
        }
        Schema::create('warehouses', function (Blueprint $table) {
            $table->id();
            $table->string('code', 30)->unique();
            $table->string('nom', 150);
            $table->string('localisation', 255)->nullable();
            $table->foreignId('manager_id')->nullable()->constrained('users')->nullOnDelete();
            $table->integer('capacite')->nullable();
            $table->string('type')->nullable();
            $table->boolean('actif')->default(true);
            $table->timestamps();
            $table->index(['actif']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('warehouses');
    }
};

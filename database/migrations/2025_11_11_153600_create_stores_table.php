<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('stores', function (Blueprint $table) {
            $table->id();
            $table->string('code', 30)->unique();
            $table->string('nom', 150);
            $table->foreignId('warehouse_id')->constrained('warehouses')->cascadeOnDelete();
            $table->foreignId('manager_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('type', 50)->nullable(); // magasin, atelier, depot
            $table->boolean('actif')->default(true);
            $table->timestamps();
            $table->index(['warehouse_id', 'actif']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stores');
    }
};

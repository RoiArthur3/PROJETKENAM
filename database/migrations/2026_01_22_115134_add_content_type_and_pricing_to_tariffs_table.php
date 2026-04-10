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
        Schema::table('tariffs', function (Blueprint $table) {
            $table->string('content_type')->nullable()->after('destination_country');
            $table->enum('pricing_type', ['per_kg', 'per_piece', 'per_cbm'])->default('per_kg')->after('price_per_kg');
            $table->decimal('min_volume', 8, 3)->nullable()->after('max_weight');
            $table->decimal('max_volume', 8, 3)->nullable()->after('min_volume');
            $table->index(['transport_mode', 'origin_country', 'destination_country'], 'tariffs_route_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tariffs', function (Blueprint $table) {
            $table->dropIndex('tariffs_route_index');
            $table->dropColumn(['content_type', 'pricing_type', 'min_volume', 'max_volume']);
        });
    }
};

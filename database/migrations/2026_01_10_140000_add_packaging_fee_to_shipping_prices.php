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
        Schema::table('shipping_prices', function (Blueprint $table) {
            $table->decimal('packaging_fee_per_carton', 10, 2)->default(0)->after('handling_fee');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shipping_prices', function (Blueprint $table) {
            $table->dropColumn('packaging_fee_per_carton');
        });
    }
};

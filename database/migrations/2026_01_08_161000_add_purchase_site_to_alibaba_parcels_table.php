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
        Schema::table('alibaba_parcels', function (Blueprint $table) {
            $table->string('purchase_site')->nullable()->after('alibaba_order_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('alibaba_parcels', function (Blueprint $table) {
            $table->dropColumn('purchase_site');
        });
    }
};

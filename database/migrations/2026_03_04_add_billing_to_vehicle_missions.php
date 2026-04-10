<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('vehicle_missions')) {
            Schema::table('vehicle_missions', function (Blueprint $table) {
                // Ajouter les colonnes de facturation si elles n'existent pas
                if (!Schema::hasColumn('vehicle_missions', 'invoice_reference')) {
                    $table->string('invoice_reference')->nullable()->after('total_client_amount');
                }
                if (!Schema::hasColumn('vehicle_missions', 'is_billed')) {
                    $table->boolean('is_billed')->default(false)->after('invoice_reference');
                }
                if (!Schema::hasColumn('vehicle_missions', 'billed_at')) {
                    $table->timestamp('billed_at')->nullable()->after('is_billed');
                }
                if (!Schema::hasColumn('vehicle_missions', 'is_revenue_recorded')) {
                    $table->boolean('is_revenue_recorded')->default(false)->after('billed_at');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('vehicle_missions')) {
            Schema::table('vehicle_missions', function (Blueprint $table) {
                $table->dropColumn(['invoice_reference', 'is_billed', 'billed_at', 'is_revenue_recorded']);
            });
        }
    }
};

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
        Schema::table('operations', function (Blueprint $table) {
            if (!Schema::hasColumn('operations', 'is_paid')) {
                $table->boolean('is_paid')->default(false)->after('statut_courant');
            }

            if (!Schema::hasColumn('operations', 'paid_by')) {
                $table->foreignId('paid_by')->nullable()->after('is_paid')->constrained('users')->nullOnDelete();
            }

            if (!Schema::hasColumn('operations', 'paid_at')) {
                $table->timestamp('paid_at')->nullable()->after('paid_by');
            }

            if (!Schema::hasColumn('operations', 'payment_reference')) {
                $table->string('payment_reference')->nullable()->after('paid_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('operations', function (Blueprint $table) {
            if (Schema::hasColumn('operations', 'payment_reference')) {
                $table->dropColumn('payment_reference');
            }

            if (Schema::hasColumn('operations', 'paid_at')) {
                $table->dropColumn('paid_at');
            }

            if (Schema::hasColumn('operations', 'paid_by')) {
                $table->dropConstrainedForeignId('paid_by');
            }

            if (Schema::hasColumn('operations', 'is_paid')) {
                $table->dropColumn('is_paid');
            }
        });
    }
};

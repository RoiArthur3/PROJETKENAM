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
        Schema::table('operation_service_validation', function (Blueprint $table) {
            // Ajouter les colonnes pour tracker qui a validé et quand
            if (!Schema::hasColumn('operation_service_validation', 'validated_by')) {
                $table->unsignedBigInteger('validated_by')->nullable()->after('statut');
            }
            if (!Schema::hasColumn('operation_service_validation', 'validated_at')) {
                $table->timestamp('validated_at')->nullable()->after('validated_by');
            }
            if (!Schema::hasColumn('operation_service_validation', 'commentaire')) {
                $table->text('commentaire')->nullable()->after('validated_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('operation_service_validation', function (Blueprint $table) {
            if (Schema::hasColumn('operation_service_validation', 'validated_by')) {
                $table->dropColumn('validated_by');
            }
            if (Schema::hasColumn('operation_service_validation', 'validated_at')) {
                $table->dropColumn('validated_at');
            }
            if (Schema::hasColumn('operation_service_validation', 'commentaire')) {
                $table->dropColumn('commentaire');
            }
        });
    }
};

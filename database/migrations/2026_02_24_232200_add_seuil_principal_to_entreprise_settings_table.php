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
        Schema::table('entreprise_settings', function (Blueprint $table) {
            $table->decimal('seuil_validation_principal', 15, 2)->default(500000)->after('seuil_validation_dg');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('entreprise_settings', function (Blueprint $table) {
            $table->dropColumn('seuil_validation_principal');
        });
    }
};

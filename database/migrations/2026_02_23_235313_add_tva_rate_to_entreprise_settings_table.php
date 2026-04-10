<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('entreprise_settings', function (Blueprint $table) {
            $table->decimal('tva_rate', 5, 2)->default(18.00)->after('compte_bancaire');
        });
    }

    public function down(): void
    {
        Schema::table('entreprise_settings', function (Blueprint $table) {
            $table->dropColumn('tva_rate');
        });
    }
};
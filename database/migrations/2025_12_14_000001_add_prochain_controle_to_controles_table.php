<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('controles', function (Blueprint $table) {
            if (!Schema::hasColumn('controles', 'prochain_controle')) {
                $table->date('prochain_controle')->nullable()->after('date_controle');
                $table->index('prochain_controle');
            }
        });
    }

    public function down(): void
    {
        Schema::table('controles', function (Blueprint $table) {
            if (Schema::hasColumn('controles', 'prochain_controle')) {
                $table->dropIndex(['prochain_controle']);
                $table->dropColumn('prochain_controle');
            }
        });
    }
};


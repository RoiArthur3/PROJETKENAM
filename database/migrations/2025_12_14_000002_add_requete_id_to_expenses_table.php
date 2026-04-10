<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            if (!Schema::hasColumn('expenses', 'requete_id')) {
                $table->foreignId('requete_id')->nullable()->after('operation_id')->constrained('requetes')->nullOnDelete();
                $table->index('requete_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            if (Schema::hasColumn('expenses', 'requete_id')) {
                $table->dropForeign(['requete_id']);
                $table->dropIndex(['requete_id']);
                $table->dropColumn('requete_id');
            }
        });
    }
};

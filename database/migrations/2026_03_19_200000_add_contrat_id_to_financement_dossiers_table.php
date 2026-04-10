<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('financement_dossiers')) {
            return;
        }

        Schema::table('financement_dossiers', function (Blueprint $table) {
            $table->unsignedBigInteger('contrat_id')->nullable()->after('created_by');
            $table->foreign('contrat_id')->references('id')->on('juridique_contrats')->onDelete('set null');
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('financement_dossiers')) {
            return;
        }

        Schema::table('financement_dossiers', function (Blueprint $table) {
            $table->dropForeign(['contrat_id']);
            $table->dropColumn('contrat_id');
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('depenses')) {
            return;
        }

        Schema::table('depenses', function (Blueprint $table) {
            if (!Schema::hasColumn('depenses', 'statut')) {
                $table->string('statut', 30)
                    ->default('impayee')
                    ->after('date_depense');
                $table->index(['statut', 'date_depense'], 'depenses_statut_date_depense_index');
            }
        });

        DB::table('depenses')
            ->where('est_justifie', true)
            ->update(['statut' => 'justifiee']);

        DB::table('depenses')
            ->where(function ($query) {
                $query->whereNull('est_justifie')
                    ->orWhere('est_justifie', false);
            })
            ->where(function ($query) {
                $query->whereNull('statut')
                    ->orWhere('statut', '')
                    ->orWhere('statut', 'impayee');
            })
            ->update(['statut' => 'impayee']);
    }

    public function down(): void
    {
        if (!Schema::hasTable('depenses') || !Schema::hasColumn('depenses', 'statut')) {
            return;
        }

        Schema::table('depenses', function (Blueprint $table) {
            $table->dropIndex('depenses_statut_date_depense_index');
            $table->dropColumn('statut');
        });
    }
};

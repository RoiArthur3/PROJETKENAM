<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class SkipValidationsTableCreation extends Migration
{
    public function up()
    {
        // Vérifiez si la table existe déjà
        if (Schema::hasTable('validations')) {
            // Marquez la migration comme exécutée
            DB::table('migrations')
                ->where('migration', '2026_01_12_180030_create_validations_table')
                ->update(['batch' => 1]);
        }
    }

    public function down()
    {
        // Ne rien faire
    }
}
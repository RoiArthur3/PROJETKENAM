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
        // Ne rien faire si les colonnes existent déjà
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Ne rien faire pour éviter de supprimer des colonnes qui pourraient être utilisées
    }
};

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
        // Migration neutralisée: la table services est créée et maintenue par une migration source unique.
        return;
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        return;
    }
};

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
        // Migration neutralisée: les FK sont déjà créées dans la migration source vehicle_assignments.
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

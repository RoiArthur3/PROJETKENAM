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
        Schema::table('operational_services', function (Blueprint $table) {
            $table->string('email')->nullable()->after('nom');
            $table->string('validateur_email')->nullable()->after('email');
            $table->text('emails_cc')->nullable()->after('validateur_email');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('operational_services', function (Blueprint $table) {
            $table->dropColumn(['email', 'validateur_email', 'emails_cc']);
        });
    }
};

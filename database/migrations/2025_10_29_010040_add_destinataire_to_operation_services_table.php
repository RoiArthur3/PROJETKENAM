<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('operation_services', function (Blueprint $table) {
            $table->string('destinataire_nom')->nullable()->after('service_email');
            $table->string('destinataire_email')->nullable()->after('destinataire_nom');
        });
    }

    public function down(): void
    {
        Schema::table('operation_services', function (Blueprint $table) {
            $table->dropColumn(['destinataire_nom', 'destinataire_email']);
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('commercial_commandes')) {
            return;
        }

        Schema::table('commercial_commandes', function (Blueprint $table) {
            if (!Schema::hasColumn('commercial_commandes', 'cc_emails')) {
                $table->text('cc_emails')->nullable()->after('email_service');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('commercial_commandes')) {
            return;
        }

        Schema::table('commercial_commandes', function (Blueprint $table) {
            if (Schema::hasColumn('commercial_commandes', 'cc_emails')) {
                $table->dropColumn('cc_emails');
            }
        });
    }
};

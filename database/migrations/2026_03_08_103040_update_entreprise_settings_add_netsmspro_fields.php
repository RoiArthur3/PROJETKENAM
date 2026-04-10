<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasColumn('entreprise_settings', 'sms_sender_id')) {
            DB::table('entreprise_settings')
                ->whereNull('sms_sender_id')
                ->orWhereRaw("TRIM(sms_sender_id) = ''")
                ->orWhereRaw('CHAR_LENGTH(sms_sender_id) > 50')
                ->update(['sms_sender_id' => 'KENAM']);
        }

        if (Schema::hasColumn('entreprise_settings', 'sms_api_url')) {
            DB::table('entreprise_settings')
                ->whereNull('sms_api_url')
                ->orWhereRaw("TRIM(sms_api_url) = ''")
                ->orWhereRaw('CHAR_LENGTH(sms_api_url) > 255')
                ->update(['sms_api_url' => 'https://www.netsmspro.net/api']);
        }

        Schema::table('entreprise_settings', function (Blueprint $table) {
            // Ajouter les champs SMS NetSMSPro s'ils n'existent pas
            if (!Schema::hasColumn('entreprise_settings', 'sms_username')) {
                $table->string('sms_username', 100)->nullable()->after('sms_provider');
            }

            if (!Schema::hasColumn('entreprise_settings', 'sms_password')) {
                $table->string('sms_password', 255)->nullable()->after('sms_username');
            }

            if (!Schema::hasColumn('entreprise_settings', 'sms_reseller_code')) {
                $table->string('sms_reseller_code', 50)->default('2656631451')->after('sms_sender_id');
            }

            if (!Schema::hasColumn('entreprise_settings', 'seuil_validation_dg_force')) {
                $table->decimal('seuil_validation_dg_force', 15, 2)->default(2500000)->after('seuil_validation_dg');
            }

            // Mettre à jour les champs existants
            $table->string('sms_sender_id', 50)->nullable()->default('KENAM')->change();
            $table->string('sms_api_url', 255)->nullable()->default('https://www.netsmspro.net/api')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('entreprise_settings', function (Blueprint $table) {
            $table->dropColumn(['sms_username', 'sms_password', 'sms_reseller_code', 'seuil_validation_dg_force']);
        });
    }
};

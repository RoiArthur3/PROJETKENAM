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
        if (!Schema::hasTable('users')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'client_type')) {
                $clientType = $table->enum('client_type', ['individual', 'business'])->default('individual');

                if (Schema::hasColumn('users', 'country')) {
                    $clientType->after('country');
                }
            }

            if (!Schema::hasColumn('users', 'account_status')) {
                $accountStatus = $table->enum('account_status', ['active', 'blocked', 'suspended'])->default('active');

                if (Schema::hasColumn('users', 'client_type')) {
                    $accountStatus->after('client_type');
                }
            }

            if (!Schema::hasColumn('users', 'notification_preference')) {
                $notificationPreference = $table->enum('notification_preference', ['email', 'sms'])->default('email');

                if (Schema::hasColumn('users', 'account_status')) {
                    $notificationPreference->after('account_status');
                }
            }

            if (!Schema::hasColumn('users', 'auto_billing')) {
                $autoBilling = $table->boolean('auto_billing')->default(false);

                if (Schema::hasColumn('users', 'notification_preference')) {
                    $autoBilling->after('notification_preference');
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('users')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            $columns = array_values(array_filter([
                Schema::hasColumn('users', 'client_type') ? 'client_type' : null,
                Schema::hasColumn('users', 'account_status') ? 'account_status' : null,
                Schema::hasColumn('users', 'notification_preference') ? 'notification_preference' : null,
                Schema::hasColumn('users', 'auto_billing') ? 'auto_billing' : null,
            ]));

            if (!empty($columns)) {
                $table->dropColumn($columns);
            }
        });
    }
};

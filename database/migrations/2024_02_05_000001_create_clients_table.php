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
        if (Schema::hasTable('clients')) {
            return;
        }

        Schema::create('clients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('client_code')->unique();
            $table->string('company_name')->nullable();
            $table->string('contact_person')->nullable();
            $table->string('phone')->nullable();
            $table->string('secondary_phone')->nullable();
            $table->string('email')->nullable();
            $table->string('secondary_email')->nullable();
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('country')->nullable();
            $table->string('postal_code')->nullable();
            $table->enum('client_type', ['individual', 'company', 'regular', 'vip'])->default('individual');
            $table->date('registration_date')->nullable();
            $table->decimal('credit_limit', 10, 2)->default(0);
            $table->decimal('current_balance', 10, 2)->default(0);
            $table->enum('payment_terms', ['cod', '7days', '14days', '30days'])->default('cod');
            $table->string('preferred_transport_mode')->nullable();
            $table->string('business_sector')->nullable();
            $table->string('tax_id')->nullable();
            $table->string('commercial_register')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_vip')->default(false);
            $table->date('last_order_date')->nullable();
            $table->integer('total_orders')->default(0);
            $table->decimal('total_revenue', 12, 2)->default(0);
            $table->decimal('average_order_value', 10, 2)->default(0);
            $table->integer('loyalty_points')->default(0);
            $table->string('referral_source')->nullable();
            $table->foreignId('assigned_agent_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('language_preference')->default('fr');
            $table->string('timezone')->default('UTC');
            $table->string('currency_preference')->default('EUR');
            $table->boolean('auto_billing_enabled')->default(false);
            $table->boolean('email_notifications_enabled')->default(true);
            $table->boolean('sms_notifications_enabled')->default(false);
            $table->timestamps();

            $table->index(['client_code']);
            $table->index(['email']);
            $table->index(['phone']);
            $table->index(['client_type']);
            $table->index(['is_active']);
            $table->index(['is_vip']);
            $table->index(['assigned_agent_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('parcels', function (Blueprint $table) {
            $table->string('content_type')->nullable()->after('transport_mode');
            $table->string('origin_city')->nullable()->after('origin_country');
            $table->string('destination_city')->nullable()->after('destination_country');
            $table->string('delivery_option')->nullable()->after('destination_city');
            $table->string('invoice_file')->nullable()->after('height');
            $table->decimal('estimated_weight', 10, 2)->nullable()->after('declared_value');
        });
    }

    public function down()
    {
        Schema::table('parcels', function (Blueprint $table) {
            $table->dropColumn(['content_type', 'origin_city', 'destination_city', 'delivery_option', 'invoice_file', 'estimated_weight']);
        });
    }
};

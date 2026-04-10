<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('personnel', function (Blueprint $table) {
            $table->text('photo_base64')->nullable();
        });
    }

    public function down(): void
    {
        if (Schema::hasColumn('personnel', 'photo_base64')) {
            Schema::table('personnel', function (Blueprint $table) {
                $table->dropIndex(['photo_base64']);
                $table->dropColumn('photo_base64');
            });
        }
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('operations', function (Blueprint $table) {
            if (!Schema::hasColumn('operations', 'type_operation_id')) {
                $table->foreignId('type_operation_id')
                    ->nullable()
                    ->after('echeance')
                    ->constrained('types_operations')
                    ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('operations', function (Blueprint $table) {
            if (Schema::hasColumn('operations', 'type_operation_id')) {
                $table->dropConstrainedForeignId('type_operation_id');
            }
        });
    }
};

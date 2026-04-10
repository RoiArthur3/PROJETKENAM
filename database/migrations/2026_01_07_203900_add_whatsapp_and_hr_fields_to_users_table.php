<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'whatsapp')) {
                $table->string('whatsapp', 30)->nullable()->after('phone');
            }
            if (!Schema::hasColumn('users', 'contrat')) {
                $table->string('contrat', 50)->nullable()->after('whatsapp');
            }
            if (!Schema::hasColumn('users', 'date_embauche')) {
                $table->date('date_embauche')->nullable()->after('contrat');
            }
            if (!Schema::hasColumn('users', 'salaire')) {
                $table->decimal('salaire', 12, 2)->nullable()->after('date_embauche');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'salaire')) {
                $table->dropColumn('salaire');
            }
            if (Schema::hasColumn('users', 'date_embauche')) {
                $table->dropColumn('date_embauche');
            }
            if (Schema::hasColumn('users', 'contrat')) {
                $table->dropColumn('contrat');
            }
            if (Schema::hasColumn('users', 'whatsapp')) {
                $table->dropColumn('whatsapp');
            }
        });
    }
};

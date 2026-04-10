<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('services', function (Blueprint $table) {
            // Ajouter des champs spécifiques pour le gestion des comptes responsables
            $table->string('responsable_email')->nullable()->after('responsable_id');
            $table->string('responsable_telephone')->nullable()->after('responsable_email');
            $table->boolean('responsable_compte_auto')->default(false)->after('responsable_telephone');
            $table->timestamp('responsable_compte_cree_le')->nullable()->after('responsable_compte_auto');
            $table->text('responsable_notes')->nullable()->after('responsable_compte_cree_le');
        });
    }

    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn([
                'responsable_email',
                'responsable_telephone',
                'responsable_compte_auto',
                'responsable_compte_cree_le',
                'responsable_notes'
            ]);
        });
    }
};

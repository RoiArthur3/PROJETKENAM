<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\ServiceOperationnel;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Ajouter la colonne responsable_id
        Schema::table('services_operationnels', function (Blueprint $table) {
            $table->unsignedBigInteger('responsable_id')->nullable()->after('responsable');
            $table->foreign('responsable_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('set null');
        });

        // 2. Mettre à jour les données existantes
        // Utilisation de DB::table pour éviter les problèmes de modèle
        $services = DB::table('services_operationnels')
            ->whereNotNull('responsable')
            ->get();

        foreach ($services as $service) {
            // Trouver l'utilisateur par nom
            $user = DB::table('users')
                ->where('name', $service->responsable)
                ->first();

            if ($user) {
                DB::table('services_operationnels')
                    ->where('id', $service->id)
                    ->update(['responsable_id' => $user->id]);
            }
        }

        // 3. Supprimer l'ancienne colonne (à décommenter après avoir vérifié que tout fonctionne)
        // Schema::table('services_operationnels', function (Blueprint $table) {
        //     $table->dropColumn('responsable');
        // });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // 1. Recréer la colonne responsable
        Schema::table('services_operationnels', function (Blueprint $table) {
            $table->string('responsable')->nullable()->after('telephone');
        });

        // 2. Mettre à jour les données
        $services = DB::table('services_operationnels')
            ->whereNotNull('responsable_id')
            ->get();

        foreach ($services as $service) {
            $user = DB::table('users')
                ->where('id', $service->responsable_id)
                ->first();

            if ($user) {
                DB::table('services_operationnels')
                    ->where('id', $service->id)
                    ->update(['responsable' => $user->name]);
            }
        }

        // 3. Supprimer la clé étrangère et la colonne responsable_id
        Schema::table('services_operationnels', function (Blueprint $table) {
            $table->dropForeign(['responsable_id']);
            $table->dropColumn('responsable_id');
        });
    }
};

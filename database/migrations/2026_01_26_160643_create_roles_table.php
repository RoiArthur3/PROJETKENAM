<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('display_name')->nullable();
            $table->string('description')->nullable();
            $table->timestamps();
        });

        // Ajouter les rôles par défaut
        DB::table('roles')->insert([
            ['name' => 'admin', 'display_name' => 'Administrateur', 'description' => 'Accès complet au système'],
            ['name' => 'manager', 'display_name' => 'Gestionnaire', 'description' => 'Accès à la gestion des opérations'],
            ['name' => 'user', 'display_name' => 'Utilisateur', 'description' => 'Accès de base']
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};

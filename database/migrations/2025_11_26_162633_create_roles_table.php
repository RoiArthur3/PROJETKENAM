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
        if (Schema::hasTable('roles')) {
            return;
        }

        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('guard_name')->default('web');
            $table->string('description')->nullable();
            $table->timestamps();
        });

        // Créer la table de liaison avec les utilisateurs
        Schema::create('role_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('role_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            $table->unique(['role_id', 'user_id']);
        });

        // Créer les rôles par défaut
        DB::table('roles')->insert([
            ['name' => 'admin', 'description' => 'Administrateur système'],
            ['name' => 'comptable', 'description' => 'Comptable'],
            ['name' => 'tresorier', 'description' => 'Trésorier'],
            ['name' => 'utilisateur', 'description' => 'Utilisateur standard']
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // On ne touche pas aux tables si elles existent déjà en prod, pour éviter de casser les droits.
        if (!Schema::hasTable('roles')) {
            return;
        }

        Schema::dropIfExists('role_user');
        Schema::dropIfExists('roles');
    }
};

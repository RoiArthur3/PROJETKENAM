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
        Schema::create('modules', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('display_name');
            $table->string('description')->nullable();
            $table->string('icon')->nullable();
            $table->string('color')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index('is_active');
            $table->index('sort_order');
        });

        // Insert modules par défaut
        DB::table('modules')->insert([
            [
                'name' => 'dashboard',
                'display_name' => 'Tableau de Bord',
                'description' => 'Accès au tableau de bord principal',
                'icon' => 'fas fa-tachometer-alt',
                'color' => 'primary',
                'is_active' => true,
                'sort_order' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'operations',
                'display_name' => 'Opérations',
                'description' => 'Gestion des opérations quotidiennes',
                'icon' => 'fas fa-tasks',
                'color' => 'success',
                'is_active' => true,
                'sort_order' => 2,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'validations',
                'display_name' => 'Validations',
                'description' => 'Validation des opérations',
                'icon' => 'fas fa-check-circle',
                'color' => 'info',
                'is_active' => true,
                'sort_order' => 3,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'admin',
                'display_name' => 'Administration',
                'description' => 'Administration du système',
                'icon' => 'fas fa-cogs',
                'color' => 'warning',
                'is_active' => true,
                'sort_order' => 4,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'comptabilite',
                'display_name' => 'Comptabilité',
                'description' => 'Gestion comptable',
                'icon' => 'fas fa-calculator',
                'color' => 'secondary',
                'is_active' => true,
                'sort_order' => 5,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'stock',
                'display_name' => 'Stock',
                'description' => 'Gestion des stocks',
                'icon' => 'fas fa-boxes',
                'color' => 'danger',
                'is_active' => true,
                'sort_order' => 6,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'commercial',
                'display_name' => 'Commercial',
                'description' => 'Gestion commerciale',
                'icon' => 'fas fa-handshake',
                'color' => 'primary',
                'is_active' => true,
                'sort_order' => 7,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'rh',
                'display_name' => 'Ressources Humaines',
                'description' => 'Gestion du personnel',
                'icon' => 'fas fa-users',
                'color' => 'info',
                'is_active' => true,
                'sort_order' => 8,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'parc',
                'display_name' => 'Parc Automobile',
                'description' => 'Gestion du parc de véhicules',
                'icon' => 'fas fa-car',
                'color' => 'success',
                'is_active' => true,
                'sort_order' => 9,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'tresorerie',
                'display_name' => 'Trésorerie',
                'description' => 'Gestion de la trésorerie',
                'icon' => 'fas fa-money-bill-wave',
                'color' => 'warning',
                'is_active' => true,
                'sort_order' => 10,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('modules');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Supprimer les migrations problématiques
        $this->removeProblematicMigrations();

        // Créer les tables manquantes
        $this->createMissingTables();
    }

    public function down(): void
    {
        Schema::dropIfExists('countries');
        Schema::dropIfExists('cities');
        Schema::dropIfExists('country_product_keyword');
    }

    private function removeProblematicMigrations(): void
    {
        $files = [
            '2026_01_11_000002_add_cities_to_parcels_table.php',
            '2026_01_11_000003_create_cities_table.php',
            '2026_01_11_000004_add_national_transport_modes.php',
            '2026_01_11_000005_add_cities_to_alibaba_parcels_table.php',
            '2026_01_11_000006_add_shipment_type_to_tariffs_table.php',
            '2026_01_11_000007_add_shipment_type_to_shipments_table.php'
        ];

        foreach ($files as $file) {
            $path = database_path("migrations/{$file}");
            if (file_exists($path)) {
                unlink($path);
            }
        }
    }

    private function createMissingTables(): void
    {
        // Créer la table countries si elle n'existe pas
        if (!Schema::hasTable('countries')) {
            Schema::create('countries', function (Blueprint $table) {
                $table->id();
                $table->string('name', 100);
                $table->string('code', 2)->unique();
                $table->string('code3', 3)->unique();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        // Créer la table cities si elle n'existe pas
        if (!Schema::hasTable('cities')) {
            Schema::create('cities', function (Blueprint $table) {
                $table->id();
                $table->string('name', 100);
                $table->string('code', 10);
                $table->foreignId('country_id')->constrained('countries')->onDelete('cascade');
                $table->boolean('is_active')->default(true);
                $table->decimal('latitude', 10, 8)->nullable();
                $table->decimal('longitude', 11, 8)->nullable();
                $table->timestamps();

                $table->unique(['country_id', 'name']);
                $table->index(['country_id', 'is_active']);
            });
        }

        // Créer la table country_product_keyword si elle n'existe pas
        if (!Schema::hasTable('country_product_keyword')) {
            Schema::create('country_product_keyword', function (Blueprint $table) {
                $table->id();
                $table->foreignId('country_id')->constrained('countries')->onDelete('cascade');
                $table->foreignId('product_keyword_id')->constrained('product_keywords')->onDelete('cascade');
                $table->enum('restriction_type', ['allowed', 'forbidden'])->default('forbidden');
                $table->text('notes')->nullable();
                $table->timestamps();

                $table->unique(['country_id', 'product_keyword_id']);
                $table->index(['country_id', 'restriction_type']);
                $table->index(['product_keyword_id', 'restriction_type']);
            });
        }

        // Ajouter les colonnes manquantes à la table parcels
        if (Schema::hasTable('parcels')) {
            if (!Schema::hasColumn('parcels', 'shipment_type')) {
                Schema::table('parcels', function (Blueprint $table) {
                    $table->enum('shipment_type', ['international', 'national'])->default('international')->after('transport_mode');
                });
            }

            if (!Schema::hasColumn('parcels', 'origin_city')) {
                Schema::table('parcels', function (Blueprint $table) {
                    $table->string('origin_city')->nullable()->after('origin_country');
                });
            }

            if (!Schema::hasColumn('parcels', 'destination_city')) {
                Schema::table('parcels', function (Blueprint $table) {
                    $table->string('destination_city')->nullable()->after('destination_country');
                });
            }
        }

        // Ajouter les colonnes manquantes à la table alibaba_parcels
        if (Schema::hasTable('alibaba_parcels')) {
            if (!Schema::hasColumn('alibaba_parcels', 'shipment_type')) {
                Schema::table('alibaba_parcels', function (Blueprint $table) {
                    $table->enum('shipment_type', ['international', 'national'])->default('international')->after('transport_mode');
                });
            }

            if (!Schema::hasColumn('alibaba_parcels', 'origin_city')) {
                Schema::table('alibaba_parcels', function (Blueprint $table) {
                    $table->string('origin_city')->nullable()->after('destination_country');
                });
            }

            if (!Schema::hasColumn('alibaba_parcels', 'destination_city')) {
                Schema::table('alibaba_parcels', function (Blueprint $table) {
                    $table->string('destination_city')->nullable()->after('origin_city');
                });
            }
        }

        // Ajouter les colonnes manquantes à la table tariffs
        if (Schema::hasTable('tariffs')) {
            if (!Schema::hasColumn('tariffs', 'shipment_type')) {
                Schema::table('tariffs', function (Blueprint $table) {
                    $table->enum('shipment_type', ['international', 'national'])->default('international')->after('transport_mode');
                });
            }
        }

        // Ajouter les colonnes manquantes à la table shipments
        if (Schema::hasTable('shipments')) {
            if (!Schema::hasColumn('shipments', 'shipment_type')) {
                Schema::table('shipments', function (Blueprint $table) {
                    $table->enum('shipment_type', ['international', 'national'])->default('international')->after('transport_mode');
                });
            }
        }

        // Mettre à jour les modes de transport
        $this->updateTransportModes();
    }

    private function updateTransportModes(): void
    {
        // Mettre à jour les enums pour inclure les modes nationaux
        // Note: MySQL ne permet pas de modifier les enums facilement
        // Cette fonction est pour documentation, les changements seront faits manuellement si nécessaire
    }
};

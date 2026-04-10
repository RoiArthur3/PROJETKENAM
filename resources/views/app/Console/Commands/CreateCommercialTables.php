<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Schema\Blueprint;

class CreateCommercialTables extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'commercial:create-tables';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Créer les tables commerciales (clients, contrats)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔧 Création des Tables Commerciales - KENAM SERVICES');
        $this->info('==================================================');

        // Créer la table clients
        if (!Schema::hasTable('clients')) {
            $this->info('Création de la table clients...');

            Schema::create('clients', function (Blueprint $table) {
                $table->id();
                $table->string('nom');
                $table->string('email')->unique();
                $table->string('telephone')->nullable();
                $table->text('adresse')->nullable();
                $table->enum('type', ['particulier', 'professionnel'])->default('particulier');
                $table->enum('statut', ['actif', 'inactif'])->default('actif');
                $table->text('notes')->nullable();
                $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
                $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
                $table->timestamps();
            });

            $this->info('✅ Table clients créée');
        } else {
            $this->info('ℹ️  Table clients existe déjà');
        }

        // Créer la table contrats
        if (!Schema::hasTable('contrats')) {
            $this->info('Création de la table contrats...');

            Schema::create('contrats', function (Blueprint $table) {
                $table->id();
                $table->string('numero')->unique();
                $table->foreignId('client_id')->constrained()->onDelete('cascade');
                $table->foreignId('service_id')->nullable()->constrained()->onDelete('set null');
                $table->date('date_debut');
                $table->date('date_fin');
                $table->decimal('montant_ht', 10, 2);
                $table->decimal('tva', 5, 2)->default(20.00);
                $table->decimal('montant_ttc', 10, 2);
                $table->enum('statut', ['en_attente', 'actif', 'termine', 'annule'])->default('en_attente');
                $table->text('description')->nullable();
                $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
                $table->foreignId('updated_by')->nullable()->constrained('users')->onDelete('set null');
                $table->timestamps();
            });

            $this->info('✅ Table contrats créée');
        } else {
            $this->info('ℹ️  Table contrats existe déjà');
        }

        // Ajouter quelques données de test si les tables sont vides
        if (Schema::hasTable('clients') && Schema::hasTable('contrats')) {
            $clientsCount = DB::table('clients')->count();
            $contratsCount = DB::table('contrats')->count();

            $this->info('');
            $this->info('📊 État actuel:');
            $this->info("- Clients: $clientsCount");
            $this->info("- Contrats: $contratsCount");

            if ($clientsCount == 0) {
                $this->info('');
                $this->info('Ajout de clients de test...');

                DB::table('clients')->insert([
                    [
                        'nom' => 'Client Test 1',
                        'email' => 'client1@test.com',
                        'telephone' => '0123456789',
                        'adresse' => 'Adresse test 1',
                        'type' => 'particulier',
                        'statut' => 'actif',
                        'created_at' => now(),
                        'updated_at' => now()
                    ],
                    [
                        'nom' => 'Client Test 2',
                        'email' => 'client2@test.com',
                        'telephone' => '0123456788',
                        'adresse' => 'Adresse test 2',
                        'type' => 'professionnel',
                        'statut' => 'actif',
                        'created_at' => now(),
                        'updated_at' => now()
                    ]
                ]);

                $this->info('✅ 2 clients de test ajoutés');
            }

            if ($contratsCount == 0 && $clientsCount > 0) {
                $this->info('');
                $this->info('Ajout de contrats de test...');

                $client1 = DB::table('clients')->first();

                if ($client1) {
                    DB::table('contrats')->insert([
                        [
                            'numero' => 'CONT-2025-001',
                            'client_id' => $client1->id,
                            'service_id' => null,
                            'date_debut' => now()->format('Y-m-d'),
                            'date_fin' => now()->addYear(1)->format('Y-m-d'),
                            'montant_ht' => 1000.00,
                            'tva' => 20.00,
                            'montant_ttc' => 1200.00,
                            'statut' => 'actif',
                            'description' => 'Contrat de test',
                            'created_at' => now(),
                            'updated_at' => now()
                        ]
                    ]);

                    $this->info('✅ 1 contrat de test ajouté');
                }
            }

            // Vérification finale
            $finalClients = DB::table('clients')->count();
            $finalContrats = DB::table('contrats')->count();

            $this->info('');
            $this->info('🎯 Résultat final:');
            $this->info("- Clients: $finalClients");
            $this->info("- Contrats: $finalContrats");

            if ($finalClients > 0 && $finalContrats > 0) {
                $this->info('');
                $this->info('🟢 TABLES PRÊTES POUR L\'APPLICATION !');
            } else {
                $this->info('');
                $this->error('🔴 PROBLÈME: Tables toujours vides');
            }
        }

        $this->info('');
        $this->info('✨ Opération terminée !');

        return Command::SUCCESS;
    }
}

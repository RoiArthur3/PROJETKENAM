<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RestoreAdmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:restore';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Rétablir l accès administrateur';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔧 Restauration de l\'Accès Admin - KENAM SERVICES');
        $this->info('==================================================');

        // Vérifier si l'admin existe
        $admin = User::where('email', 'admin@kenamservices.com')->first();

        if ($admin) {
            $this->info('✅ Utilisateur admin trouvé');
            $this->info("- Email: {$admin->email}");
            $this->info("- Nom: {$admin->name}");
            $this->info("- Rôle: {$admin->role}");
            $this->info("- Actif: " . ($admin->is_active ? 'Oui' : 'Non'));

            // Rétablir les permissions si nécessaire
            $needsUpdate = false;

            if (!$admin->can_access_dashboard) {
                $admin->can_access_dashboard = true;
                $needsUpdate = true;
                $this->info('🔄 Ajout de l\'accès dashboard');
            }

            if (!$admin->can_access_operations) {
                $admin->can_access_operations = true;
                $needsUpdate = true;
                $this->info('🔄 Ajout de l\'accès opérations');
            }

            if (!$admin->can_access_hr) {
                $admin->can_access_hr = true;
                $needsUpdate = true;
                $this->info('🔄 Ajout de l\'accès RH');
            }

            if (!$admin->can_access_fleet) {
                $admin->can_access_fleet = true;
                $needsUpdate = true;
                $this->info('🔄 Ajout de l\'accès parc auto');
            }

            if (!$admin->can_access_suppliers) {
                $admin->can_access_suppliers = true;
                $needsUpdate = true;
                $this->info('🔄 Ajout de l\'accès fournisseurs');
            }

            if (!$admin->can_access_warehouse) {
                $admin->can_access_warehouse = true;
                $needsUpdate = true;
                $this->info('🔄 Ajout de l\'accès entrepôt');
            }

            if (!$admin->can_access_accounting) {
                $admin->can_access_accounting = true;
                $needsUpdate = true;
                $this->info('🔄 Ajout de l\'accès comptabilité');
            }

            if (!$admin->can_access_invoicing) {
                $admin->can_access_invoicing = true;
                $needsUpdate = true;
                $this->info('🔄 Ajout de l\'accès facturation');
            }

            if (!$admin->is_active) {
                $admin->is_active = true;
                $needsUpdate = true;
                $this->info('🔄 Activation du compte');
            }

            if ($needsUpdate) {
                $admin->save();
                $this->info('✅ Permissions mises à jour');
            }

            // Vérifier le mot de passe
            if (!Hash::check('Admin@2025!', $admin->password)) {
                $admin->password = Hash::make('Admin@2025!');
                $admin->save();
                $this->info('🔄 Mot de passe réinitialisé');
            }

        } else {
            $this->error('❌ Utilisateur admin non trouvé, création en cours...');

            // Créer l'admin
            $admin = User::create([
                'name' => 'Administrateur KENAM',
                'email' => 'admin@kenamservices.com',
                'password' => Hash::make('Admin@2025!'),
                'email_verified_at' => now(),
                'is_active' => true,
                'can_access_dashboard' => true,
                'can_access_operations' => true,
                'can_access_hr' => true,
                'can_access_fleet' => true,
                'can_access_suppliers' => true,
                'can_access_warehouse' => true,
                'can_access_accounting' => true,
                'can_access_invoicing' => true,
                'role' => 'admin'
            ]);

            $this->info('✅ Utilisateur admin créé');
        }

        $this->info('');
        $this->info('🎯 Vérification finale:');
        $this->info('- Email: admin@kenamservices.com');
        $this->info('- Mot de passe: Admin@2025!');
        $this->info('- Rôle: admin');
        $this->info('- Accès: Tous les modules');

        $this->info('');
        $this->info('🟢 ACCÈS ADMIN RÉTABLIS !');
        $this->info('🌐 URL de connexion: http://127.0.0.1:8000/login');
        $this->info('');
        $this->info('✨ Opération terminée !');

        return Command::SUCCESS;
    }
}

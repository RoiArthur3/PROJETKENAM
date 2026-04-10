<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class EnableDashboardAccess extends Command
{
    protected $signature = 'dashboard:enable-access';
    protected $description = 'Enable dashboard access for all admin and superadmin users';

    public function handle()
    {
        $this->info('🔓 Activation de l\'accès au dashboard...');

        try {
            // Activer pour tous les superadmins et admins
            $updated = User::where(function ($query) {
                $query->where('role', 'superadmin')
                      ->orWhere('role', 'admin');
            })->update(['can_access_dashboard' => true]);

            $this->info("✅ {$updated} administrateur(s) ont maintenant accès au dashboard");

            // Afficher les utilisateurs mis à jour
            $admins = User::where(function ($query) {
                $query->where('role', 'superadmin')
                      ->orWhere('role', 'admin');
            })->get();

            $this->info("\n📋 Utilisateurs avec accès au dashboard :");
            foreach ($admins as $admin) {
                $status = $admin->can_access_dashboard ? '✅' : '❌';
                $this->line("   {$status} {$admin->name} ({$admin->email}) - Rôle: {$admin->role}");
            }

            $this->info("\n✨ L'accès au dashboard est maintenant activé !");
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error("❌ Erreur: " . $e->getMessage());
            return Command::FAILURE;
        }
    }
}

<?php

namespace App\Console\Commands;

use App\Mail\OperationCreatedNotification;
use App\Mail\OperationValidationRequest;
use App\Models\Operation;
use App\Models\ServiceOperationnel;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestOperationEmailCommand extends Command
{
    protected $signature = 'email:test-operation {email?} {--type=validation}';
    protected $description = 'Tester l\'envoi d\'emails d\'opérations (validation ou notification)';

    public function handle()
    {
        $email = $this->argument('email') ?: 'test@example.com';
        $type = $this->option('type');

        $this->info("Début du test d'email d'opération à : $email");
        $this->info("Type d'email : $type");
        $this->line('');

        try {
            // Récupérer un service opérationnel pour le test
            $serviceOperationnel = ServiceOperationnel::first();
            if (!$serviceOperationnel) {
                $this->error('Aucun service opérationnel trouvé. Veuillez d\'abord exécuter le seeder.');
                return 1;
            }

            // Créer une opération de test
            $operation = new Operation([
                'id' => 9999,
                'titre' => 'Opération de Test - ' . now()->format('d/m/Y H:i:s'),
                'description' => 'Ceci est une opération de test pour vérifier l\'envoi d\'emails.',
                'priorite' => 'moyenne',
                'date_echeance' => now()->addDays(3),
                'operational_service_id' => $serviceOperationnel->id,
                'statut_courant' => 'en_attente',
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // Créer un utilisateur de test
            $demandeur = new User([
                'name' => 'Utilisateur Test',
                'email' => 'test@kenamservices.net'
            ]);

            // Préparer les données pour l'email
            $emailData = [
                'operation' => $operation,
                'services' => [
                    [
                        'service' => $serviceOperationnel,
                        'ordre' => 1,
                        'statut' => 'en_attente'
                    ]
                ],
                'totalServices' => 1,
                'demandeur' => $demandeur,
                'serviceOperationnel' => $serviceOperationnel,
                'typeOperation' => 'Test'
            ];

            $this->info('Données de test préparées :');
            $this->info('- Opération : ' . $operation->titre);
            $this->info('- Service : ' . $serviceOperationnel->nom);
            $this->info('- Demandeur : ' . $demandeur->name);
            $this->line('');

            // Envoyer l'email selon le type
            if ($type === 'validation') {
                $this->info('Envoi d\'email de validation...');
                Mail::to($email)->send(new OperationValidationRequest($emailData, 'validation_1'));
                $this->info('✅ Email de validation envoyé !');
            } elseif ($type === 'notification') {
                $this->info('Envoi d\'email de notification...');
                Mail::to($email)->send(new OperationCreatedNotification($emailData));
                $this->info('✅ Email de notification envoyé !');
            } else {
                $this->error('Type d\'email invalide. Utilisez --type=validation ou --type=notification');
                return 1;
            }

            $this->line('');
            $this->info('Configuration SMTP utilisée :');
            $this->info('- Hôte : ' . config('mail.mailers.smtp.host'));
            $this->info('- Port : ' . config('mail.mailers.smtp.port'));
            $this->info('- Chiffrement : ' . (config('mail.mailers.smtp.encryption') ?? 'Aucun'));
            $this->info('- Expéditeur : ' . config('mail.from.address') . ' (' . config('mail.from.name') . ')');

            $this->line('');
            $this->info('Pour tester avec une vraie adresse email :');
            $this->info('php artisan email:test-operation votre@email.com --type=validation');
            $this->info('php artisan email:test-operation votre@email.com --type=notification');

            return 0;

        } catch (\Exception $e) {
            $this->error('❌ Erreur lors de l\'envoi : ' . $e->getMessage());
            $this->line('');
            $this->line('Détails de l\'erreur :');
            $this->line($e->getTraceAsString());
            
            $this->line('');
            $this->line('Vérifiez votre configuration SMTP dans le fichier .env :');
            $this->line('MAIL_MAILER=' . config('mail.default'));
            $this->line('MAIL_HOST=' . config('mail.mailers.smtp.host'));
            $this->line('MAIL_PORT=' . config('mail.mailers.smtp.port'));
            $this->line('MAIL_USERNAME=' . (config('mail.mailers.smtp.username') ? '***' : 'Non défini'));
            $this->line('MAIL_PASSWORD=' . (config('mail.mailers.smtp.password') ? '***' : 'Non défini'));
            $this->line('MAIL_ENCRYPTION=' . (config('mail.mailers.smtp.encryption') ?? 'Aucun'));
            $this->line('MAIL_FROM_ADDRESS=' . config('mail.from.address'));
            $this->line('MAIL_FROM_NAME=' . config('mail.from.name'));
            
            return 1;
        }
    }
}

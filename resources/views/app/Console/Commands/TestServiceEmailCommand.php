<?php

namespace App\Console\Commands;

use App\Models\ServiceOperationnel;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestServiceEmailCommand extends Command
{
    protected $signature = 'email:test-service {--service=} {--email=}';
    protected $description = 'Tester l\'envoi d\'email à un service opérationnel';

    public function handle()
    {
        $serviceId = $this->option('service');
        $customEmail = $this->option('email');

        $this->info('=== Test d\'envoi d\'email aux services ===');
        $this->line('');

        try {
            // Récupérer les services
            if ($serviceId) {
                $services = ServiceOperationnel::where('id', $serviceId)->get();
                $this->info("Test pour le service ID: $serviceId");
            } else {
                $services = ServiceOperationnel::where('actif', true)->get();
                $this->info("Test pour tous les services actifs (" . $services->count() . ")");
            }

            if ($services->isEmpty()) {
                $this->error('Aucun service trouvé !');
                return 1;
            }

            $this->line('');
            $this->info('Services trouvés :');
            foreach ($services as $service) {
                $this->line("- {$service->nom} ({$service->email})");
            }
            $this->line('');

            // Préparer le contenu de test
            $testData = [
                'subject' => 'Test Email - KENAM SERVICES',
                'operation_titre' => 'Opération de Test - ' . now()->format('d/m/Y H:i:s'),
                'operation_description' => 'Ceci est un test du système d\'envoi d\'emails.',
                'demandeur' => 'Système de Test',
                'date' => now()->format('d/m/Y H:i'),
                'priority' => 'moyenne',
                'deadline' => now()->addDays(3)->format('d/m/Y'),
            ];

            // Envoyer les emails
            $successCount = 0;
            $errorCount = 0;

            foreach ($services as $service) {
                $email = $customEmail ?: $service->email;
                
                $this->info("Envoi à : {$service->nom} -> $email");
                
                try {
                    // Créer un email de test simple
                    Mail::raw(
                        "Bonjour {$service->responsable},\n\n" .
                        "Ceci est un email de test du système KENAM SERVICES.\n\n" .
                        "Détails de l'opération :\n" .
                        "- Titre : {$testData['operation_titre']}\n" .
                        "- Description : {$testData['operation_description']}\n" .
                        "- Demandeur : {$testData['demandeur']}\n" .
                        "- Date : {$testData['date']}\n" .
                        "- Priorité : {$testData['priority']}\n" .
                        "- Échéance : {$testData['deadline']}\n\n" .
                        "Cordialement,\n" .
                        "L'équipe KENAM SERVICES",
                        function ($message) use ($email, $testData, $service) {
                            $message->to($email)
                                   ->subject($testData['subject'])
                                   ->from(config('mail.from.address'), config('mail.from.name'));
                        }
                    );

                    $this->info("✅ Email envoyé avec succès à {$service->nom}");
                    $successCount++;

                } catch (\Exception $e) {
                    $this->error("❌ Erreur pour {$service->nom}: " . $e->getMessage());
                    $errorCount++;
                }
            }

            $this->line('');
            $this->info('=== Résultat du test ===');
            $this->info("✅ Succès : $successCount email(s)");
            $this->info("❌ Erreurs : $errorCount email(s)");

            if ($successCount > 0) {
                $this->line('');
                $this->info('Configuration SMTP utilisée :');
                $this->info('- Hôte : ' . config('mail.mailers.smtp.host'));
                $this->info('- Port : ' . config('mail.mailers.smtp.port'));
                $this->info('- Chiffrement : ' . (config('mail.mailers.smtp.encryption') ?? 'Aucun'));
                $this->info('- Expéditeur : ' . config('mail.from.address') . ' (' . config('mail.from.name') . ')');
            }

            return $errorCount > 0 ? 1 : 0;

        } catch (\Exception $e) {
            $this->error('❌ Erreur générale : ' . $e->getMessage());
            return 1;
        }
    }
}

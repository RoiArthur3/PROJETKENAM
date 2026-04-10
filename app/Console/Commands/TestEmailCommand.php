<?php

namespace App\Console\Commands;

use App\Mail\DynamicMail;
use App\Models\EmailTemplate;
use App\Models\Operation;
use App\Models\ServiceOperationnel;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;

class TestEmailCommand extends Command
{
    protected $signature = 'email:test {email?} {--template=default_notification}';
    protected $description = 'Tester l\'envoi d\'emails avec le système de templates';

    public function handle()
    {
        $email = $this->argument('email') ?: 'test@example.com';
        $templateName = $this->option('template');

        $this->info("Début du test d'envoi d'email à : $email");
        $this->info("Template utilisé : $templateName");
        $this->line('');

        // Données de test pour le template
        $data = [
            'user_name' => 'Utilisateur Test',
            'subject' => 'Test du système d\'emails KENAM',
            'message' => 'Ceci est un test du système d\'envoi d\'emails KENAM SERVICES.',
            'details' => "Détails du test :\n" .
                       "- Template : $templateName\n" .
                       "- Date : " . now()->format('d/m/Y H:i:s') . "\n" .
                       "- Environnement : " . config('app.env') . "\n" .
                       "- URL : " . config('app.url'),
            'date' => now()->format('d/m/Y H:i'),
            'footer' => 'Cet email est un test, merci de ne pas y répondre.'
        ];

        try {
            // Vérifier si le template existe
            if (!EmailTemplate::where('name', $templateName)->exists()) {
                $this->warn("Le template '$templateName' n'existe pas. Utilisation du template par défaut.");

                // Créer un template par défaut s'il n'existe pas
                if (!EmailTemplate::where('name', 'default_notification')->exists()) {
                    EmailTemplate::create([
                        'name' => 'default_notification',
                        'subject' => 'Notification : {{ subject }}',
                        'description' => 'Template de notification par défaut',
                        'template' => "Bonjour {{ user_name }},\n\n{{ message }}\n\nDétails :\n{{ details }}\n\nDate : {{ date }}\n\nCordialement,\nL'équipe KENAM SERVICES",
                        'variables' => ['user_name', 'message', 'details', 'date', 'subject'],
                        'is_active' => true
                    ]);
                    $this->info('Template par défaut créé avec succès.');
                }

                $templateName = 'default_notification';
            }

            // Envoi de l'email
            Mail::to($email)->send(new DynamicMail($templateName, $data));

            $this->info('✅ Email envoyé avec succès !');
            $this->line('');
            $this->info('Configuration SMTP utilisée :');
            $this->info('- Hôte : ' . config('mail.mailers.smtp.host'));
            $this->info('- Port : ' . config('mail.mailers.smtp.port'));
            $this->info('- Chiffrement : ' . (config('mail.mailers.smtp.encryption') ?? 'Aucun'));
            $this->info('- Expéditeur : ' . config('mail.from.address') . ' (' . config('mail.from.name') . ')');

            return 0;

        } catch (\Exception $e) {
            $this->error('❌ Erreur lors de l\'envoi : ' . $e->getMessage());
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

        return 0;
    }
}

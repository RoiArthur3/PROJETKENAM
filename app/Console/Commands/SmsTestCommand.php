<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class SmsTestCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sms:test {numero} {--message=Test SMS KENAM}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Envoie un SMS de test à un numéro donné via la configuration SMS de production';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $numero = $this->argument('numero');
        $message = $this->option('message') ?? 'Test SMS KENAM';

        // Utilisez ici votre service d'envoi SMS habituel
        try {
            // Exemple générique :
            // app('sms')->send($numero, $message);
            // Remplacez par votre logique réelle
            if (method_exists(app(), 'sms')) {
                app('sms')->send($numero, $message);
            } else {
                // Si vous avez un service spécifique, adaptez ici
                // \App\Services\SmsService::send($numero, $message);
            }
            $this->info("SMS envoyé à $numero : $message");
            Log::info("SMS test envoyé à $numero : $message");
        } catch (\Throwable $e) {
            $this->error('Erreur lors de l\'envoi du SMS : ' . $e->getMessage());
            Log::error('Erreur SMS test', ['numero' => $numero, 'message' => $message, 'error' => $e->getMessage()]);
        }
    }
}

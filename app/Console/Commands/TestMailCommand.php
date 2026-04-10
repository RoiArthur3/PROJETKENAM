<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestMailCommand extends Command
{
    protected $signature = 'mail:test {to}';
    protected $description = 'Envoie un email de test à une adresse spécifiée';

    public function handle()
    {
        $to = $this->argument('to');
        try {
            Mail::raw('Ceci est un email de test depuis le serveur KENAM.', function ($message) use ($to) {
                $message->to($to)
                        ->subject('Test Email KENAM');
            });
            $this->info('Email de test envoyé à ' . $to);
        } catch (\Exception $e) {
            $this->error('Erreur lors de l\'envoi : ' . $e->getMessage());
        }
    }
}

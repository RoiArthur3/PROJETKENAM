<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendEmail;
use App\Models\Operation;

class EmailTestController extends Controller
{
    /**
     * Afficher le formulaire de test d'email
     */
    public function index()
    {
        return view('emails.test');
    }

    /**
     * Envoyer un email de test
     */
    public function sendTestEmail(Request $request)
    {
        $validated = $request->validate([
            'to_email' => 'required|email',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        try {
            // Créer une opération de test
            $testOperation = new \stdClass();
            $testOperation->id = 'TEST-' . time();
            $testOperation->titre = $validated['subject'];
            $testOperation->description = $validated['message'];
            $testOperation->montant = 150000;
            $testOperation->priorite = 'moyenne';
            $testOperation->demandeur_name = 'Test User';
            $testOperation->demandeur_email = 'test@kenamservices.net';
            $testOperation->created_at = now();
            $testOperation->echeance = now()->addDays(7);

            // URL de test
            $url = request()->getSchemeAndHttpHost() . "/operations/{$testOperation->id}/validate";

            // Envoyer l'email avec le nouveau design
            Mail::to($validated['to_email'])->send(new SendEmail($testOperation, $url));

            return redirect()->back()->with('success', 'Email de test envoyé avec succès à ' . $validated['to_email']);

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erreur lors de l\'envoi: ' . $e->getMessage());
        }
    }

    /**
     * Envoyer l'email de test aux deux adresses spécifiées
     */
    public function sendTestEmails()
    {
        $emails = [
            'technolabpro@gmail.com',
            'pmo.arthur@webpluriel.com'
        ];

        $results = [];

        foreach ($emails as $email) {
            try {
                // Créer une opération de test
                $testOperation = new \stdClass();
                $testOperation->id = 'KENAM-' . time() . '-' . uniqid();
                $testOperation->titre = 'Test Email Design KENAM SERVICES';
                $testOperation->description = 'Ceci est un test du nouveau design premium des emails KENAM SERVICES avec les couleurs orange, blanc et vert. Le logo est maintenant plus grand et le design est complètement refait avec des animations modernes.';
                $testOperation->montant = 250000;
                $testOperation->priorite = 'haute';
                $testOperation->demandeur_name = 'KENAM SERVICES';
                $testOperation->demandeur_email = 'info@kenamservices.net';
                $testOperation->created_at = now();
                $testOperation->echeance = now()->addDays(5);

                // URL de test
                $url = request()->getSchemeAndHttpHost() . "/operations/{$testOperation->id}/validate";

                // Envoyer l'email
                Mail::to($email)->send(new SendEmail($testOperation, $url));
                
                $results[$email] = '✅ Succès';
                
            } catch (\Exception $e) {
                $results[$email] = '❌ Erreur: ' . $e->getMessage();
            }
        }

        return response()->json([
            'message' => 'Test d\'envoi terminé',
            'results' => $results
        ]);
    }
}

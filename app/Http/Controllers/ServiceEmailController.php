<?php

namespace App\Http\Controllers;

use App\Models\ServiceOperationnel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class ServiceEmailController extends Controller
{
    /**
     * Afficher le formulaire d'envoi d'email aux services
     */
    public function create()
    {
        $services = ServiceOperationnel::where('actif', true)->whereNotNull('email')->get();
        return view('services.email.create', compact('services'));
    }

    /**
     * Envoyer un email aux services sélectionnés
     */
    public function send(Request $request)
    {
        $validated = $request->validate([
            'services' => 'required|array|min:1',
            'services.*' => 'exists:services_operationnels,id',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
            'attachment' => 'nullable|file|max:10240', // 10MB max
        ]);

        $selectedServices = ServiceOperationnel::whereIn('id', $validated['services'])->get();
        $successCount = 0;
        $errorCount = 0;

        foreach ($selectedServices as $service) {
            try {
                // Créer un email simple pour le service
                $emailData = [
                    'service' => $service,
                    'subject' => $validated['subject'],
                    'message' => $validated['message'],
                ];

                // Envoyer l'email
                Mail::raw($validated['message'], function ($message) use ($service, $validated) {
                    $message->to($service->email)
                           ->subject($validated['subject'])
                           ->from(config('mail.from.address'), config('mail.from.name'));
                });

                $successCount++;
                Log::info("Email envoyé avec succès au service: {$service->nom} ({$service->email})");

            } catch (\Exception $e) {
                $errorCount++;
                Log::error("Erreur lors de l'envoi au service {$service->nom}: " . $e->getMessage());
            }
        }

        return redirect()->back()->with('success', 
            "Emails envoyés: {$successCount} succès, {$errorCount} erreurs"
        );
    }

    /**
     * API pour récupérer les services pour les sélecteurs
     */
    public function apiServices(Request $request)
    {
        $query = ServiceOperationnel::where('actif', true);
        
        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function($q) use ($search) {
                $q->where('nom', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $services = $query->select('id', 'nom', 'email', 'responsable')
                         ->orderBy('nom')
                         ->limit(50)
                         ->get();

        return response()->json($services);
    }
}

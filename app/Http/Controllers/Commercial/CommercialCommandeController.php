<?php

namespace App\Http\Controllers\Commercial;

use App\Http\Controllers\Controller;
use App\Mail\DynamicMail;
use App\Models\CommercialCommande;
use App\Models\CommercialCommandeFournisseur;
use App\Models\CommercialCommandeReponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class CommercialCommandeController extends Controller
{
    public function index(Request $request)
    {
        $query = CommercialCommande::query()->with('client')->orderByDesc('created_at');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('reference', 'like', '%' . $search . '%')
                    ->orWhere('type_engin', 'like', '%' . $search . '%')
                    ->orWhere('email_service', 'like', '%' . $search . '%');
            });
        }

        if ($request->filled('statut')) {
            $query->where('statut', $request->input('statut'));
        }

        if ($request->filled('date_debut')) {
            $query->whereDate('created_at', '>=', $request->input('date_debut'));
        }

        if ($request->filled('date_fin')) {
            $query->whereDate('created_at', '<=', $request->input('date_fin'));
        }

        $commandes = $query->paginate(10);

        return view('commercial.commandes.index', compact('commandes'));
    }

    public function create()
    {
        return view('commercial.commandes.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'type_engin' => 'required|string|max:255',
            'description' => 'required|string',
            'delai' => 'required|date',
            'email_service' => 'required|email|max:255',
            'cc_emails' => 'nullable|string',
        ]);

        $payload = [
            'type_engin' => $validated['type_engin'],
            'commentaire' => $validated['description'],
            'date_debut' => now()->toDateString(),
            'date_fin' => $validated['delai'],
            'email_service' => $validated['email_service'],
            'cc_emails' => $validated['cc_emails'] ?? null,
            'created_by' => Auth::id(),
            'statut' => 'brouillon',
            'client_id' => null,
            'budget' => null,
            'quantite' => 1,
            'lieu' => null,
        ];

        $commande = CommercialCommande::create($payload);

        return redirect()->route('commercial.commandes.show', $commande->id)->with('success', 'Commande créée avec succès');
    }

    public function show($id)
    {
        $commande = CommercialCommande::with(['client', 'reponse.fournisseurs'])->findOrFail($id);

        return view('commercial.commandes.show', compact('commande'));
    }

    public function edit($id)
    {
        $commande = CommercialCommande::findOrFail($id);

        return view('commercial.commandes.edit', compact('commande'));
    }

    public function update(Request $request, $id)
    {
        $commande = CommercialCommande::findOrFail($id);

        $validated = $request->validate([
            'type_engin' => 'required|string|max:255',
            'description' => 'required|string',
            'delai' => 'required|date',
            'email_service' => 'required|email|max:255',
            'cc_emails' => 'nullable|string',
        ]);

        $commande->update([
            'type_engin' => $validated['type_engin'],
            'commentaire' => $validated['description'],
            'date_fin' => $validated['delai'],
            'email_service' => $validated['email_service'],
            'cc_emails' => $validated['cc_emails'] ?? null,
        ]);

        return redirect()->route('commercial.commandes.show', $commande->id)->with('success', 'Commande mise à jour avec succès');
    }

    public function envoyerLogistique($id)
    {
        $commande = CommercialCommande::with('client')->findOrFail($id);

        if (empty($commande->email_service)) {
            return redirect()->route('commercial.commandes.show', $commande->id)->with('error', 'Email service logistique manquant');
        }

        if (!in_array($commande->statut, ['brouillon', 'refuse'], true)) {
            return redirect()->route('commercial.commandes.show', $commande->id)->with('error', 'Commande déjà envoyée ou en cours de traitement');
        }

        $commande->update([
            'statut' => 'envoye_logistique',
            'sent_at' => now(),
        ]);

        $details = '';
        $details .= 'Référence: ' . $commande->reference . "\n";
        $details .= 'Type engin: ' . $commande->type_engin . "\n";
        $details .= 'Quantité: ' . $commande->quantite . "\n";

        if (!empty($commande->lieu)) {
            $details .= 'Lieu: ' . $commande->lieu . "\n";
        }

        if (!empty($commande->date_debut)) {
            $details .= 'Date début: ' . $commande->date_debut->format('d/m/Y') . "\n";
        }

        if (!empty($commande->date_fin)) {
            $details .= 'Date fin: ' . $commande->date_fin->format('d/m/Y') . "\n";
        }

        if (!empty($commande->budget)) {
            $details .= 'Budget: ' . number_format((float) $commande->budget, 2, ',', ' ') . "\n";
        }

        $actionUrl = route('commercial.commandes.reponse.create', $commande->id);

        $ccEmails = [];
        if (!empty($commande->cc_emails)) {
            $pieces = preg_split('/[;,\s]+/', (string) $commande->cc_emails, -1, PREG_SPLIT_NO_EMPTY);
            $ccEmails = array_values(array_unique(array_filter($pieces, function ($email) {
                return filter_var($email, FILTER_VALIDATE_EMAIL);
            })));
        }

        $mailer = Mail::to($commande->email_service);
        if (!empty($ccEmails)) {
            $mailer->cc($ccEmails);
        }

        $mailer->send(new DynamicMail('dynamic', [
            'subject' => 'Demande logistique - ' . $commande->reference,
            'message' => "Bonjour,\n\nUne demande de disponibilité d'engins vous a été soumise.\n\nMerci de répondre via le lien ci-dessous.",
            'details' => $details,
            'action_url' => $actionUrl,
            'action_text' => 'Répondre à la demande',
        ]));

        return redirect()->route('commercial.commandes.show', $commande->id)->with('success', 'Email envoyé à la logistique');
    }

    public function createReponse($id)
    {
        $commande = CommercialCommande::with('client')->findOrFail($id);

        return view('commercial.commandes.reponse', compact('commande'));
    }

    public function storeReponse(Request $request, $id)
    {
        $commande = CommercialCommande::findOrFail($id);

        $validated = $request->validate([
            'commentaire' => 'nullable|string',
            'fournisseurs' => 'required|array|min:1',
            'fournisseurs.*.fournisseur_nom' => 'required|string|max:255',
            'fournisseurs.*.engin_disponible' => 'nullable|string|max:255',
            'fournisseurs.*.prix' => 'nullable|numeric|min:0',
            'fournisseurs.*.devise' => 'nullable|string|max:10',
            'fournisseurs.*.details' => 'nullable|string',
        ]);

        DB::transaction(function () use ($validated, $commande) {
            $reponse = CommercialCommandeReponse::updateOrCreate(
                ['commande_id' => $commande->id],
                [
                    'commentaire' => $validated['commentaire'] ?? null,
                    'created_by' => Auth::id(),
                ]
            );

            CommercialCommandeFournisseur::where('reponse_id', $reponse->id)->delete();

            foreach ($validated['fournisseurs'] as $row) {
                $row['reponse_id'] = $reponse->id;
                CommercialCommandeFournisseur::create($row);
            }

            $commande->update([
                'statut' => 'repondu',
                'responded_at' => now(),
            ]);
        });

        return redirect()->route('commercial.commandes.show', $commande->id)->with('success', 'Réponse enregistrée');
    }

    public function valider($id)
    {
        $commande = CommercialCommande::findOrFail($id);

        if ($commande->statut !== 'repondu') {
            return redirect()->route('commercial.commandes.show', $commande->id)->with('error', 'La commande doit être au statut "Répondu"');
        }

        $commande->update([
            'statut' => 'valide',
            'validated_at' => now(),
            'validated_by' => Auth::id(),
        ]);

        return redirect()->route('commercial.commandes.show', $commande->id)->with('success', 'Commande validée');
    }

    public function refuser($id)
    {
        $commande = CommercialCommande::findOrFail($id);

        if (!in_array($commande->statut, ['envoye_logistique', 'repondu'], true)) {
            return redirect()->route('commercial.commandes.show', $commande->id)->with('error', 'Statut non compatible');
        }

        $commande->update([
            'statut' => 'refuse',
            'validated_at' => null,
            'validated_by' => null,
        ]);

        return redirect()->route('commercial.commandes.show', $commande->id)->with('success', 'Commande refusée');
    }

    public function destroy($id)
    {
        $commande = CommercialCommande::findOrFail($id);

        $commande->delete();

        return redirect()->route('commercial.commandes.index')->with('success', 'Commande supprimée avec succès');
    }
}

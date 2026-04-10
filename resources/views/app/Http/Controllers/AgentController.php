<?php

namespace App\Http\Controllers;

use App\Models\Requete;
use App\Models\Operation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AgentController extends Controller
{
    /**
     * Afficher le tableau de bord de l'agent
     */
    public function dashboard()
    {
        $user = Auth::user();

        // Statistiques des requêtes
        $baseQuery = Requete::where('demandeur_id', $user->id);
        $stats = [
            'total_requetes' => (clone $baseQuery)->count(),
            'en_attente' => (clone $baseQuery)->whereIn('statut', ['ENREGISTREE', 'EN_ATTENTE_ENVOI', 'ENVOYEE'])->count(),
            'en_cours' => (clone $baseQuery)->whereIn('statut', ['EN_COURS_DE_TRAITEMENT', 'TRANSFERE'])->count(),
            'cloturees' => (clone $baseQuery)->where('statut', 'CLOTUREE')->count(),
            'rejetees' => (clone $baseQuery)->where('statut', 'REJETEE')->count(),
        ];

        // Dernières requêtes
        $dernieresRequetes = (clone $baseQuery)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Informations sur l'agent
        $agentInfo = [
            'nom' => $user->name,
            'email' => $user->email,
            'telephone' => $user->telephone,
            'service' => $user->service ? $user->service->nom : 'Non assigné',
            'role' => $user->role,
            'date_creation' => $user->created_at->format('d/m/Y'),
        ];

        return view('agent.dashboard', compact('stats', 'dernieresRequetes', 'agentInfo'));
    }

    /**
     * Mini dashboard des requêtes (KPIs + dernières requêtes)
     */
    public function requetesDashboard()
    {
        $user = Auth::user();
        $baseQuery = Requete::where('demandeur_id', $user->id);

        $stats = [
            'total' => (clone $baseQuery)->count(),
            'en_attente' => (clone $baseQuery)->whereIn('statut', ['ENREGISTREE', 'EN_ATTENTE_ENVOI', 'ENVOYEE'])->count(),
            'en_cours' => (clone $baseQuery)->whereIn('statut', ['EN_COURS_DE_TRAITEMENT', 'TRANSFERE'])->count(),
            'cloturees' => (clone $baseQuery)->where('statut', 'CLOTUREE')->count(),
            'rejetees' => (clone $baseQuery)->where('statut', 'REJETEE')->count(),
        ];

        $dernieresRequetes = (clone $baseQuery)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('agent.requetes-dashboard', compact('stats', 'dernieresRequetes'));
    }

    /**
     * Afficher la liste des requêtes de l'agent
     */
    public function requetes()
    {
        $user = Auth::user();
        $baseQuery = Requete::where('demandeur_id', $user->id);

        $stats = [
            'total' => (clone $baseQuery)->count(),
            'en_attente' => (clone $baseQuery)->whereIn('statut', ['ENREGISTREE', 'EN_ATTENTE_ENVOI', 'ENVOYEE'])->count(),
            'en_cours' => (clone $baseQuery)->whereIn('statut', ['EN_COURS_DE_TRAITEMENT', 'TRANSFERE'])->count(),
            'cloturees' => (clone $baseQuery)->where('statut', 'CLOTUREE')->count(),
            'rejetees' => (clone $baseQuery)->where('statut', 'REJETEE')->count(),
        ];

        $requetes = (clone $baseQuery)
                          ->with(['operation', 'historiques.user'])
                          ->orderBy('created_at', 'desc')
                          ->paginate(15);

        return view('agent.requetes', compact('requetes', 'stats'));
    }

    /**
     * Afficher le formulaire de création de requête
     */
    public function createRequete()
    {
        // On utilise les types d'opérations (table types_operations) comme choix
        $operations = \App\Models\TypeOperation::where('actif', true)->orderBy('libelle')->get();
        return view('agent.create-requete', compact('operations'));
    }

    /**
     * Enregistrer une nouvelle requête
     */
    public function storeRequete(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'operation_id' => 'required|exists:types_operations,id',
            'titre' => 'required|string|max:255',
            'description' => 'required|string',
            'priorite' => 'required|in:basse,moyenne,haute,urgente',
            'fichiers.*' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png|max:10240'
        ]);

        // Déterminer le service émetteur (service de l'agent)
        $serviceEmetteurId = $user->service_id;
        if (!$serviceEmetteurId) {
            return back()->withErrors(['service_id' => 'Aucun service assigné à cet agent.'])->withInput();
        }

        // Pour l'instant: destinataire = même service (peut être ajusté plus tard)
        $serviceDestinataireId = $serviceEmetteurId;

        $requete = new Requete();
        $requete->reference = $requete->generateReference();
        $requete->objet = $request->titre;
        $requete->description = $request->description;
        $requete->statut = 'ENREGISTREE';
        $requete->service_emetteur_id = $serviceEmetteurId;
        $requete->service_destinataire_id = $serviceDestinataireId;
        $requete->demandeur_id = $user->id;
        $requete->module_source = 'operations';
        $requete->operation_id = $request->operation_id;
        $requete->save();

        $requete->historiques()->create([
            'user_id' => $user->id,
            'action' => 'CREATION',
            'nouveau_statut' => 'ENREGISTREE',
            'commentaire' => 'Requête créée',
        ]);

        // Gérer les fichiers joints
        if ($request->hasFile('fichiers')) {
            foreach ($request->file('fichiers') as $fichier) {
                $safeName = Str::random(8) . '_' . preg_replace('/[^A-Za-z0-9_.-]/', '_', $fichier->getClientOriginalName());
                $chemin = $fichier->storeAs('requetes/' . $requete->id, $safeName, 'public');
                $requete->piecesJointes()->create([
                    'nom_fichier' => $fichier->getClientOriginalName(),
                    'chemin_fichier' => $chemin,
                    'type_mime' => $fichier->getMimeType(),
                    'taille' => $fichier->getSize(),
                    'upload_par' => $user->id,
                ]);
            }
        }

        return redirect()->route('agent.requetes.index')
            ->with('success', 'Votre requête a été créée avec succès.');
    }

    /**
     * Afficher les détails d'une requête
     */
    public function showRequete(Requete $requete)
    {
        // Vérifier que la requête appartient à l'agent connecté
        if ($requete->demandeur_id !== Auth::id()) {
            abort(403, 'Accès non autorisé');
        }

        $requete->load(['operation', 'demandeur', 'historiques.user', 'piecesJointes']);

        return view('agent.show-requete', compact('requete'));
    }

    /**
     * Afficher le profil de l'agent
     */
    public function profile()
    {
        $user = Auth::user();
        return view('agent.profile', compact('user'));
    }

    /**
     * Mettre à jour le profil de l'agent
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'nom' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'telephone' => 'nullable|string|max:20',
            'current_password' => 'nullable|required_with:new_password|string',
            'new_password' => 'nullable|string|min:6|confirmed'
        ]);

        $user->nom = $request->nom;
        $user->email = $request->email;
        $user->telephone = $request->telephone;

        if ($request->filled('new_password')) {
            if (!Hash::check($request->current_password, $user->password)) {
                return back()->withErrors(['current_password' => 'Le mot de passe actuel est incorrect.']);
            }
            $user->password = Hash::make($request->new_password);
        }

        $user->save();

        return back()->with('success', 'Votre profil a été mis à jour avec succès.');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Fournisseur;
use App\Models\DocumentFournisseur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Carbon\Carbon;
use PDF;

class DocumentFournisseurController extends Controller
{
    /**
     * Afficher la liste des documents d'un fournisseur
     */
    public function index(Fournisseur $fournisseur, Request $request)
    {
        $query = $fournisseur->documents()
            ->with(['user'])
            ->latest('date_emission');
        
        // Filtres
        if ($request->filled('type')) {
            $query->where('type', $request->input('type'));
        }
        
        if ($request->filled('categorie')) {
            $query->where('categorie', $request->input('categorie'));
        }
        
        if ($request->filled('statut')) {
            $query->where('statut', $request->input('statut'));
        }
        
        if ($request->filled('debut') && $request->filled('fin')) {
            $query->whereBetween('date_emission', [
                $request->input('debut'),
                $request->input('fin')
            ]);
        }
        
        $documents = $query->paginate(15)->withQueryString();
        
        // Statistiques
        $stats = [
            'total' => $fournisseur->documents()->count(),
            'a_verifier' => $fournisseur->documents()->where('statut', 'a_verifier')->count(),
            'valide' => $fournisseur->documents()->where('statut', 'valide')->count(),
            'expire' => $fournisseur->documents()
                ->where('statut', 'valide')
                ->where('date_expiration', '<', now())
                ->count(),
            'expire_bientot' => $fournisseur->documents()
                ->where('statut', 'valide')
                ->where('date_expiration', '>=', now())
                ->where('date_expiration', '<=', now()->addDays(30))
                ->count(),
        ];
        
        // Types de documents disponibles
        $types = [
            'contrat' => 'Contrat',
            'certificat' => 'Certificat',
            'attestation' => 'Attestation',
            'facture' => 'Facture',
            'devis' => 'Devis',
            'bon_commande' => 'Bon de commande',
            'bon_livraison' => 'Bon de livraison',
            'rib' => 'RIB',
            'kbis' => 'Extrait Kbis',
            'assurance' => 'Assurance',
            'autre' => 'Autre document',
        ];
        
        // Catégories disponibles
        $categories = [
            'juridique' => 'Juridique',
            'financier' => 'Financier',
            'technique' => 'Technique',
            'commercial' => 'Commercial',
            'assurance' => 'Assurance',
            'autre' => 'Autre',
        ];
        
        return view('fournisseurs.documents.index', compact(
            'fournisseur', 
            'documents', 
            'stats',
            'types',
            'categories'
        ));
    }

    /**
     * Afficher le formulaire d'ajout d'un document
     */
    public function create(Fournisseur $fournisseur)
    {
        // Types de documents disponibles
        $types = [
            'contrat' => 'Contrat',
            'certificat' => 'Certificat',
            'attestation' => 'Attestation',
            'facture' => 'Facture',
            'devis' => 'Devis',
            'bon_commande' => 'Bon de commande',
            'bon_livraison' => 'Bon de livraison',
            'rib' => 'RIB',
            'kbis' => 'Extrait Kbis',
            'assurance' => 'Assurance',
            'autre' => 'Autre document',
        ];
        
        // Catégories disponibles
        $categories = [
            'juridique' => 'Juridique',
            'financier' => 'Financier',
            'technique' => 'Technique',
            'commercial' => 'Commercial',
            'assurance' => 'Assurance',
            'autre' => 'Autre',
        ];
        
        // Période de validité par défaut
        $dateEmission = now()->format('Y-m-d');
        $dateExpiration = now()->addYear()->format('Y-m-d');
        
        return view('fournisseurs.documents.create', compact(
            'fournisseur',
            'types',
            'categories',
            'dateEmission',
            'dateExpiration'
        ));
    }

    /**
     * Enregistrer un nouveau document
     */
    public function store(Request $request, Fournisseur $fournisseur)
    {
        $validated = $request->validate([
            'reference' => 'nullable|string|max:50|unique:document_fournisseurs,reference',
            'type' => 'required|string|max:50',
            'categorie' => 'required|string|max:50',
            'titre' => 'required|string|max:255',
            'description' => 'nullable|string',
            'fichier' => 'required|file|max:10240', // 10MB max
            'date_emission' => 'required|date',
            'date_expiration' => 'nullable|date|after_or_equal:date_emission',
            'est_obligatoire' => 'boolean',
            'a_renouveler' => 'boolean',
            'jours_alerte_avant_expiration' => 'nullable|integer|min:1',
            'statut' => 'required|in:brouillon,a_verifier,valide,rejete,expire',
            'motif_rejet' => 'nullable|required_if:statut,rejete|string|max:255',
            'notes' => 'nullable|string',
        ]);
        
        // Traitement du fichier
        $file = $request->file('fichier');
        $fileName = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '-' . time() . '.' . $file->getClientOriginalExtension();
        $filePath = 'fournisseurs/' . $fournisseur->id . '/documents/' . $fileName;
        
        // Vérification du type MIME
        $mimeType = $file->getClientMimeType();
        $fileSize = $file->getSize();
        
        // Enregistrement du fichier
        $path = Storage::disk('public')->putFileAs(
            'fournisseurs/' . $fournisseur->id . '/documents',
            $file,
            $fileName
        );
        
        if (!$path) {
            return back()
                ->withInput()
                ->with('error', 'Une erreur est survenue lors du téléversement du fichier.');
        }
        
        // Création du document
        $document = new DocumentFournisseur([
            'reference' => $validated['reference'] ?? 'DOC-' . date('Ymd') . '-' . strtoupper(Str::random(6)),
            'fournisseur_id' => $fournisseur->id,
            'type' => $validated['type'],
            'categorie' => $validated['categorie'],
            'titre' => $validated['titre'],
            'description' => $validated['description'] ?? null,
            'fichier_nom' => $file->getClientOriginalName(),
            'fichier_chemin' => $path,
            'fichier_taille' => $fileSize,
            'fichier_type' => $mimeType,
            'date_emission' => $validated['date_emission'],
            'date_expiration' => $validated['date_expiration'] ?? null,
            'est_obligatoire' => $validated['est_obligatoire'] ?? false,
            'a_renouveler' => $validated['a_renouveler'] ?? false,
            'jours_alerte_avant_expiration' => $validated['jours_alerte_avant_expiration'] ?? 30,
            'statut' => $validated['statut'],
            'motif_rejet' => $validated['motif_rejet'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'user_id' => auth()->id(),
        ]);
        
        try {
            $document->save();
            
            return redirect()
                ->route('fournisseurs.documents.show', ['fournisseur' => $fournisseur->id, 'document' => $document->id])
                ->with('success', 'Le document a été enregistré avec succès.');
            
        } catch (\Exception $e) {
            // Supprimer le fichier en cas d'erreur
            Storage::disk('public')->delete($path);
            
            return back()
                ->withInput()
                ->with('error', 'Une erreur est survenue lors de l\'enregistrement du document : ' . $e->getMessage());
        }
    }

    /**
     * Afficher les détails d'un document
     */
    public function show(Fournisseur $fournisseur, DocumentFournisseur $document)
    {
        $document->load(['user']);
        
        // Vérifier si l'utilisateur a le droit de voir ce document
        if ($document->fournisseur_id !== $fournisseur->id) {
            abort(404);
        }
        
        // Vérifier si le fichier existe
        $fileExists = Storage::disk('public')->exists($document->fichier_chemin);
        
        // Types de documents disponibles
        $types = [
            'contrat' => 'Contrat',
            'certificat' => 'Certificat',
            'attestation' => 'Attestation',
            'facture' => 'Facture',
            'devis' => 'Devis',
            'bon_commande' => 'Bon de commande',
            'bon_livraison' => 'Bon de livraison',
            'rib' => 'RIB',
            'kbis' => 'Extrait Kbis',
            'assurance' => 'Assurance',
            'autre' => 'Autre document',
        ];
        
        // Catégories disponibles
        $categories = [
            'juridique' => 'Juridique',
            'financier' => 'Financier',
            'technique' => 'Technique',
            'commercial' => 'Commercial',
            'assurance' => 'Assurance',
            'autre' => 'Autre',
        ];
        
        return view('fournisseurs.documents.show', compact(
            'fournisseur',
            'document',
            'fileExists',
            'types',
            'categories'
        ));
    }

    /**
     * Télécharger un document
     */
    public function download(Fournisseur $fournisseur, DocumentFournisseur $document)
    {
        // Vérifier si l'utilisateur a le droit de télécharger ce document
        if ($document->fournisseur_id !== $fournisseur->id) {
            abort(404);
        }
        
        $filePath = storage_path('app/public/' . $document->fichier_chemin);
        
        if (!file_exists($filePath)) {
            return back()->with('error', 'Le fichier demandé n\'existe plus.');
        }
        
        return response()->download($filePath, $document->fichier_nom);
    }

    /**
     * Afficher un document dans le navigateur
     */
    public function view(Fournisseur $fournisseur, DocumentFournisseur $document)
    {
        // Vérifier si l'utilisateur a le droit de voir ce document
        if ($document->fournisseur_id !== $fournisseur->id) {
            abort(404);
        }
        
        $filePath = storage_path('app/public/' . $document->fichier_chemin);
        
        if (!file_exists($filePath)) {
            return back()->with('error', 'Le fichier demandé n\'existe plus.');
        }
        
        $mimeType = mime_content_type($filePath);
        
        return response()->file($filePath, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="' . $document->fichier_nom . '"',
        ]);
    }

    /**
     * Afficher le formulaire de modification d'un document
     */
    public function edit(Fournisseur $fournisseur, DocumentFournisseur $document)
    {
        // Vérifier si l'utilisateur a le droit de modifier ce document
        if ($document->fournisseur_id !== $fournisseur->id) {
            abort(404);
        }
        
        // Types de documents disponibles
        $types = [
            'contrat' => 'Contrat',
            'certificat' => 'Certificat',
            'attestation' => 'Attestation',
            'facture' => 'Facture',
            'devis' => 'Devis',
            'bon_commande' => 'Bon de commande',
            'bon_livraison' => 'Bon de livraison',
            'rib' => 'RIB',
            'kbis' => 'Extrait Kbis',
            'assurance' => 'Assurance',
            'autre' => 'Autre document',
        ];
        
        // Catégories disponibles
        $categories = [
            'juridique' => 'Juridique',
            'financier' => 'Financier',
            'technique' => 'Technique',
            'commercial' => 'Commercial',
            'assurance' => 'Assurance',
            'autre' => 'Autre',
        ];
        
        return view('fournisseurs.documents.edit', compact(
            'fournisseur',
            'document',
            'types',
            'categories'
        ));
    }

    /**
     * Mettre à jour un document
     */
    public function update(Request $request, Fournisseur $fournisseur, DocumentFournisseur $document)
    {
        // Vérifier si l'utilisateur a le droit de modifier ce document
        if ($document->fournisseur_id !== $fournisseur->id) {
            abort(404);
        }
        
        $validated = $request->validate([
            'reference' => [
                'required',
                'string',
                'max:50',
                'unique:document_fournisseurs,reference,' . $document->id
            ],
            'type' => 'required|string|max:50',
            'categorie' => 'required|string|max:50',
            'titre' => 'required|string|max:255',
            'description' => 'nullable|string',
            'fichier' => 'nullable|file|max:10240', // 10MB max
            'date_emission' => 'required|date',
            'date_expiration' => 'nullable|date|after_or_equal:date_emission',
            'est_obligatoire' => 'boolean',
            'a_renouveler' => 'boolean',
            'jours_alerte_avant_expiration' => 'nullable|integer|min:1',
            'statut' => 'required|in:brouillon,a_verifier,valide,rejete,expire',
            'motif_rejet' => 'nullable|required_if:statut,rejete|string|max:255',
            'notes' => 'nullable|string',
        ]);
        
        // Mise à jour du document
        $document->fill([
            'reference' => $validated['reference'],
            'type' => $validated['type'],
            'categorie' => $validated['categorie'],
            'titre' => $validated['titre'],
            'description' => $validated['description'] ?? null,
            'date_emission' => $validated['date_emission'],
            'date_expiration' => $validated['date_expiration'] ?? null,
            'est_obligatoire' => $validated['est_obligatoire'] ?? false,
            'a_renouveler' => $validated['a_renouveler'] ?? false,
            'jours_alerte_avant_expiration' => $validated['jours_alerte_avant_expiration'] ?? 30,
            'statut' => $validated['statut'],
            'motif_rejet' => $validated['motif_rejet'] ?? null,
            'notes' => $validated['notes'] ?? null,
        ]);
        
        // Mise à jour du fichier si fourni
        if ($request->hasFile('fichier')) {
            $file = $request->file('fichier');
            $fileName = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME)) . '-' . time() . '.' . $file->getClientOriginalExtension();
            $filePath = 'fournisseurs/' . $fournisseur->id . '/documents/' . $fileName;
            
            // Supprimer l'ancien fichier
            Storage::disk('public')->delete($document->fichier_chemin);
            
            // Enregistrer le nouveau fichier
            $path = Storage::disk('public')->putFileAs(
                'fournisseurs/' . $fournisseur->id . '/documents',
                $file,
                $fileName
            );
            
            if (!$path) {
                return back()
                    ->withInput()
                    ->with('error', 'Une erreur est survenue lors du téléversement du nouveau fichier.');
            }
            
            // Mettre à jour les informations du fichier
            $document->fichier_nom = $file->getClientOriginalName();
            $document->fichier_chemin = $path;
            $document->fichier_taille = $file->getSize();
            $document->fichier_type = $file->getClientMimeType();
        }
        
        // Mettre à jour la date de vérification si le statut passe à "validé"
        if ($document->isDirty('statut') && $validated['statut'] === 'valide') {
            $document->date_verification = now();
            $document->verificateur_id = auth()->id();
        }
        
        try {
            $document->save();
            
            return redirect()
                ->route('fournisseurs.documents.show', ['fournisseur' => $fournisseur->id, 'document' => $document->id])
                ->with('success', 'Le document a été mis à jour avec succès.');
            
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Une erreur est survenue lors de la mise à jour du document : ' . $e->getMessage());
        }
    }

    /**
     * Supprimer un document
     */
    public function destroy(Fournisseur $fournisseur, DocumentFournisseur $document)
    {
        // Vérifier si l'utilisateur a le droit de supprimer ce document
        if ($document->fournisseur_id !== $fournisseur->id) {
            abort(404);
        }
        
        // Vérifier si le document peut être supprimé
        if ($document->est_obligatoire && $document->est_valide) {
            return back()
                ->with('error', 'Impossible de supprimer un document obligatoire et validé.');
        }
        
        try {
            // Supprimer le fichier physique
            Storage::disk('public')->delete($document->fichier_chemin);
            
            // Supprimer l'entrée en base de données
            $document->delete();
            
            return redirect()
                ->route('fournisseurs.documents.index', ['fournisseur' => $fournisseur->id])
                ->with('success', 'Le document a été supprimé avec succès.');
            
        } catch (\Exception $e) {
            return back()
                ->with('error', 'Une erreur est survenue lors de la suppression du document : ' . $e->getMessage());
        }
    }

    /**
     * Valider un document
     */
    public function valider(Request $request, Fournisseur $fournisseur, DocumentFournisseur $document)
    {
        // Vérifier si l'utilisateur a le droit de valider ce document
        if ($document->fournisseur_id !== $fournisseur->id) {
            abort(404);
        }
        
        if ($document->est_valide) {
            return back()->with('warning', 'Ce document a déjà été validé.');
        }
        
        $document->statut = 'valide';
        $document->date_verification = now();
        $document->verificateur_id = auth()->id();
        $document->save();
        
        return back()->with('success', 'Le document a été validé avec succès.');
    }

    /**
     * Rejeter un document
     */
    public function rejeter(Request $request, Fournisseur $fournisseur, DocumentFournisseur $document)
    {
        // Vérifier si l'utilisateur a le droit de rejeter ce document
        if ($document->fournisseur_id !== $fournisseur->id) {
            abort(404);
        }
        
        $validated = $request->validate([
            'motif_rejet' => 'required|string|max:255',
        ]);
        
        $document->statut = 'rejete';
        $document->motif_rejet = $validated['motif_rejet'];
        $document->save();
        
        return back()->with('success', 'Le document a été marqué comme rejeté.');
    }

    /**
     * Renouveler un document
     */
    public function renouveler(Fournisseur $fournisseur, DocumentFournisseur $document)
    {
        // Vérifier si l'utilisateur a le droit de renouveler ce document
        if ($document->fournisseur_id !== $fournisseur->id) {
            abort(404);
        }
        
        // Créer une copie du document avec une nouvelle date d'expiration
        $nouveauDocument = $document->replicate();
        $nouveauDocument->reference = 'DOC-' . date('Ymd') . '-' . strtoupper(Str::random(6));
        $nouveauDocument->date_emission = now();
        $nouveauDocument->date_expiration = now()->addYear();
        $nouveauDocument->statut = 'a_verifier';
        $nouveauDocument->date_verification = null;
        $nouveauDocument->verificateur_id = null;
        $nouveauDocument->motif_rejet = null;
        $nouveauDocument->user_id = auth()->id();
        
        // Créer un nouveau nom de fichier pour éviter les conflits
        $extension = pathinfo($document->fichier_chemin, PATHINFO_EXTENSION);
        $nouveauNomFichier = Str::slug(pathinfo($document->fichier_nom, PATHINFO_FILENAME)) . 
                            '-renouvelé-' . time() . '.' . $extension;
        
        $nouveauChemin = 'fournisseurs/' . $fournisseur->id . '/documents/' . $nouveauNomFichier;
        
        // Copier le fichier
        Storage::disk('public')->copy($document->fichier_chemin, $nouveauChemin);
        
        $nouveauDocument->fichier_nom = $document->fichier_nom;
        $nouveauDocument->fichier_chemin = $nouveauChemin;
        
        try {
            $nouveauDocument->save();
            
            // Mettre à jour l'ancien document
            $document->a_renouveler = false;
            $document->save();
            
            return redirect()
                ->route('fournisseurs.documents.show', ['fournisseur' => $fournisseur->id, 'document' => $nouveauDocument->id])
                ->with('success', 'Le document a été renouvelé avec succès.');
            
        } catch (\Exception $e) {
            // Supprimer le fichier en cas d'erreur
            Storage::disk('public')->delete($nouveauChemin);
            
            return back()
                ->with('error', 'Une erreur est survenue lors du renouvellement du document : ' . $e->getMessage());
        }
    }

    /**
     * Générer un PDF récapitulatif des documents
     */
    public function pdf(Fournisseur $fournisseur)
    {
        $documents = $fournisseur->documents()
            ->orderBy('type')
            ->orderBy('date_expiration', 'desc')
            ->get();
        
        $pdf = PDF::loadView('fournisseurs.documents.pdf', [
            'fournisseur' => $fournisseur,
            'documents' => $documents,
        ]);
        
        return $pdf->download('documents-fournisseur-' . $fournisseur->id . '.pdf');
    }
}

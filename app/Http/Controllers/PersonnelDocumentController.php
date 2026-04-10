<?php

namespace App\Http\Controllers;

use App\Models\Personnel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PersonnelDocumentController extends Controller
{
    /**
     * Afficher les documents d'un personnel
     */
    public function index(Request $request, Personnel $personnel)
    {
        $this->authorize('view', $personnel);

        // Récupérer les documents avec filtres
        $query = $personnel->documents();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('nom_fichier', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%");
        }

        if ($request->filled('type')) {
            $query->where('type_document', $request->type);
        }

        if ($request->filled('statut')) {
            $query->where('statut', $request->statut);
        }

        $documents = $query->orderBy('created_at', 'desc')->get();

        return view('rh.personnel.documents', compact('personnel', 'documents'));
    }

    /**
     * Télécharger un nouveau document
     */
    public function store(Request $request, Personnel $personnel)
    {
        $this->authorize('update', $personnel);

        $validated = $request->validate([
            'fichier' => 'required|file|max:10240', // 10MB max
            'type_document' => 'required|in:CV,CONTRAT,DIPLOME,ATTESTATION,CASIER_JUDICIAIRE,CERTIFICAT_MEDICAL,PHOTO,CNI,PASSEPORT,PERMIS,AUTRE',
            'date_expiration' => 'nullable|date|after:today',
            'description' => 'nullable|string|max:500',
        ]);

        // Validation du type de fichier
        $file = $request->file('fichier');
        $allowedTypes = [
            'application/pdf', 'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'image/jpeg', 'image/png', 'application/zip'
        ];

        if (!in_array($file->getMimeType(), $allowedTypes)) {
            return back()->with('error', 'Format de fichier non autorisé');
        }

        // Stockage du fichier
        $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('documents/personnel/' . $personnel->id, $filename, 'public');

        // Création de l'enregistrement du document
        $personnel->documents()->create([
            'nom_fichier' => $file->getClientOriginalName(),
            'chemin_fichier' => $path,
            'type_document' => $validated['type_document'],
            'taille_fichier' => $this->formatFileSize($file->getSize()),
            'date_expiration' => $validated['date_expiration'],
            'description' => $validated['description'],
            'statut' => 'EN_ATTENTE',
            'upload_par' => Auth::id(),
        ]);

        return back()->with('success', 'Document téléchargé avec succès');
    }

    /**
     * Valider un document
     */
    public function valider(Request $request, Personnel $personnel, $documentId)
    {
        $this->authorize('update', $personnel);

        $document = $personnel->documents()->findOrFail($documentId);

        $validated = $request->validate([
            'decision' => 'required|in:VALIDE,REJETE',
            'motif_decision' => 'required_if:decision,REJETE|string|max:500',
        ]);

        $document->update([
            'statut' => $validated['decision'],
            'date_validation' => now(),
            'valide_par' => Auth::id(),
            'motif_decision' => $validated['motif_decision'] ?? null,
        ]);

        $message = $validated['decision'] === 'VALIDE'
            ? 'Document validé avec succès'
            : 'Document rejeté';

        return back()->with('success', $message);
    }

    /**
     * Supprimer un document
     */
    public function destroy(Personnel $personnel, $documentId)
    {
        $this->authorize('update', $personnel);

        $document = $personnel->documents()->findOrFail($documentId);

        // Suppression du fichier physique
        if (Storage::disk('public')->exists($document->chemin_fichier)) {
            Storage::disk('public')->delete($document->chemin_fichier);
        }

        // Suppression de l'enregistrement
        $document->delete();

        return back()->with('success', 'Document supprimé avec succès');
    }

    /**
     * Télécharger un document
     */
    public function download(Personnel $personnel, $documentId)
    {
        $this->authorize('view', $personnel);

        $document = $personnel->documents()->findOrFail($documentId);

        if (!Storage::disk('public')->exists($document->chemin_fichier)) {
            abort(404, 'Fichier introuvable');
        }

        $filePath = Storage::disk('public')->path($document->chemin_fichier);

        return response()->download($filePath, $document->nom_fichier);
    }

    /**
     * Vérifier les documents expirés
     */
    public function verifierExpiration()
    {
        $documentsExpire = Personnel::whereHas('documents', function($query) {
            $query->where('date_expiration', '<=', now()->addDays(30))
                  ->where('statut', 'VALIDE');
        })->with(['documents' => function($query) {
            $query->where('date_expiration', '<=', now()->addDays(30))
                  ->where('statut', 'VALIDE');
        }])->get();

        return view('rh.documents.expiration', compact('documentsExpire'));
    }

    /**
     * Mettre à jour le statut des documents expirés
     */
    public function mettreAJourExpiration()
    {
        $documents = Personnel::whereHas('documents', function($query) {
            $query->where('date_expiration', '<', now())
                  ->where('statut', 'VALIDE');
        })->get();

        foreach ($documents as $personnel) {
            $personnel->documents()
                ->where('date_expiration', '<', now())
                ->where('statut', 'VALIDE')
                ->update(['statut' => 'EXPIRE']);
        }

        return back()->with('success', 'Statut des documents expirés mis à jour');
    }

    /**
     * Formater la taille du fichier
     */
    private function formatFileSize($bytes)
    {
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } else {
            return $bytes . ' bytes';
        }
    }
}

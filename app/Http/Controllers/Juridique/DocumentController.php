<?php

namespace App\Http\Controllers\Juridique;

use App\Http\Controllers\Controller;
use App\Models\JuridiqueContrat;
use App\Models\JuridiqueDocument;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    public function index()
    {
        $documents = Schema::hasTable('juridique_documents')
            ? JuridiqueDocument::with('contrat')->latest()->paginate(15)
            : new LengthAwarePaginator(collect(), 0, 15);

        return view('juridique.documents.index', compact('documents'));
    }

    public function create()
    {
        if (!Schema::hasTable('juridique_documents')) {
            return redirect()
                ->route('juridique.documents.index')
                ->with('error', 'La table des documents juridiques est absente sur cet environnement.');
        }

        $contrats = Schema::hasTable('juridique_contrats')
            ? JuridiqueContrat::orderBy('titre')->get()
            : collect();

        return view('juridique.documents.create', compact('contrats'));
    }

    public function store(Request $request)
    {
        if (!Schema::hasTable('juridique_documents')) {
            return back()
                ->withInput()
                ->with('error', 'Impossible d\'enregistrer le document: la table juridique_documents est absente sur cet environnement.');
        }

        $validated = $request->validate([
            'contrat_id' => Schema::hasTable('juridique_contrats') ? 'nullable|exists:juridique_contrats,id' : 'nullable',
            'reference' => 'nullable|string|max:100|unique:juridique_documents,reference',
            'titre' => 'required|string|max:255',
            'type_document' => 'nullable|string|max:100',
            'fichier' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,csv,txt,jpg,jpeg,png,webp,zip,rar|max:10240',
            'piece_jointe' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,csv,txt,jpg,jpeg,png,webp,zip,rar|max:10240',
            'date_document' => 'nullable|date',
            'date_expiration' => 'nullable|date|after_or_equal:date_document',
            'statut' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
        ]);

        $uploadedFile = $request->file('fichier') ?? $request->file('piece_jointe');
        $storedPath = null;

        try {
            if ($uploadedFile) {
                $storedPath = $uploadedFile->store('juridique/documents', 'public');

                if (!$storedPath) {
                    return back()
                        ->withInput()
                        ->with('error', 'Le fichier n\'a pas pu être téléversé. Vérifiez le stockage du serveur.');
                }

                $validated['chemin_fichier'] = $storedPath;
            }

            $validated['created_by'] = Auth::id();
            JuridiqueDocument::create($validated);

            return redirect()->route('juridique.documents.index')->with('success', 'Document enregistré avec succès.');
        } catch (\Throwable $exception) {
            if ($storedPath && Storage::disk('public')->exists($storedPath)) {
                Storage::disk('public')->delete($storedPath);
            }

            Log::error('Erreur enregistrement document juridique', [
                'message' => $exception->getMessage(),
                'user_id' => Auth::id(),
            ]);

            return back()
                ->withInput()
                ->with('error', 'Impossible d\'enregistrer le document sur cet environnement.');
        }
    }

    public function show(JuridiqueDocument $document)
    {
        if (!Schema::hasTable('juridique_documents')) {
            return redirect()
                ->route('juridique.documents.index')
                ->with('error', 'La table des documents juridiques est absente sur cet environnement.');
        }

        if (Schema::hasTable('juridique_contrats')) {
            $document->load('contrat');
        }

        return view('juridique.documents.show', compact('document'));
    }

    public function edit(JuridiqueDocument $document)
    {
        if (!Schema::hasTable('juridique_documents')) {
            return redirect()
                ->route('juridique.documents.index')
                ->with('error', 'La table des documents juridiques est absente sur cet environnement.');
        }

        $contrats = Schema::hasTable('juridique_contrats')
            ? JuridiqueContrat::orderBy('titre')->get()
            : collect();

        return view('juridique.documents.edit', compact('document', 'contrats'));
    }

    public function update(Request $request, JuridiqueDocument $document)
    {
        if (!Schema::hasTable('juridique_documents')) {
            return back()
                ->withInput()
                ->with('error', 'Impossible de mettre à jour le document: la table juridique_documents est absente sur cet environnement.');
        }

        $validated = $request->validate([
            'contrat_id' => Schema::hasTable('juridique_contrats') ? 'nullable|exists:juridique_contrats,id' : 'nullable',
            'reference' => 'nullable|string|max:100|unique:juridique_documents,reference,' . $document->id,
            'titre' => 'required|string|max:255',
            'type_document' => 'nullable|string|max:100',
            'fichier' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,csv,txt,jpg,jpeg,png,webp,zip,rar|max:10240',
            'piece_jointe' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx,csv,txt,jpg,jpeg,png,webp,zip,rar|max:10240',
            'date_document' => 'nullable|date',
            'date_expiration' => 'nullable|date|after_or_equal:date_document',
            'statut' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
        ]);

        $uploadedFile = $request->file('fichier') ?? $request->file('piece_jointe');
        $oldPath = $document->chemin_fichier;
        $newPath = null;

        try {
            if ($uploadedFile) {
                $newPath = $uploadedFile->store('juridique/documents', 'public');

                if (!$newPath) {
                    return back()
                        ->withInput()
                        ->with('error', 'Le nouveau fichier n\'a pas pu être téléversé. Vérifiez le stockage du serveur.');
                }

                $validated['chemin_fichier'] = $newPath;
            }

            $document->update($validated);

            if ($newPath && $oldPath && Storage::disk('public')->exists($oldPath)) {
                Storage::disk('public')->delete($oldPath);
            }

            return redirect()->route('juridique.documents.index')->with('success', 'Document mis à jour avec succès.');
        } catch (\Throwable $exception) {
            if ($newPath && Storage::disk('public')->exists($newPath)) {
                Storage::disk('public')->delete($newPath);
            }

            Log::error('Erreur mise à jour document juridique', [
                'document_id' => $document->id,
                'message' => $exception->getMessage(),
                'user_id' => Auth::id(),
            ]);

            return back()
                ->withInput()
                ->with('error', 'Impossible de mettre à jour le document sur cet environnement.');
        }
    }

    public function destroy(JuridiqueDocument $document)
    {
        if (!Schema::hasTable('juridique_documents')) {
            return redirect()
                ->route('juridique.documents.index')
                ->with('error', 'La table des documents juridiques est absente sur cet environnement.');
        }

        if ($document->chemin_fichier && Storage::disk('public')->exists($document->chemin_fichier)) {
            Storage::disk('public')->delete($document->chemin_fichier);
        }

        $document->delete();
        return redirect()->route('juridique.documents.index')->with('success', 'Document supprimé avec succès.');
    }
}

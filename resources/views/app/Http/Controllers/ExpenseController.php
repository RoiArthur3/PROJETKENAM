<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Operation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

/**
 * @OA\Info(
 *     title="API de Gestion des Dépenses",
 *     version="1.0.0",
 *     description="API pour la gestion des dépenses de l'entreprise",
 *     @OA\Contact(
 *         email="support@kenam.ci",
 *         name="Équipe Support"
 *     ),
 *     @OA\License(
 *         name="Licence MIT",
 *         url="https://opensource.org/licenses/MIT"
 *     )
 * )
 * 
 * @OA\Server(
 *     url=L5_SWAGGER_CONST_HOST,
 *     description="Serveur API principal"
 * )
 * 
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     in="header",
 *     name="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT"
 * )
 * 
 * @OA\Tag(
 *     name="Dépenses",
 *     description="Opérations liées aux dépenses"
 * )
 * @OA\Tag(
 *     name="Rapports",
 *     description="Rapports et statistiques sur les dépenses"
 * )
 */
class ExpenseController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/expenses",
     *     summary="Lister toutes les dépenses",
     *     tags={"Dépenses"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="statut",
     *         in="query",
     *         description="Filtrer par statut (en_attente, approuvee, rejetee)",
     *         required=false,
     *         @OA\Schema(type="string", enum={"en_attente", "approuvee", "rejetee"})
     *     ),
     *     @OA\Parameter(
     *         name="service",
     *         in="query",
     *         description="Filtrer par service concerné",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="Numéro de page pour la pagination",
     *         required=false,
     *         @OA\Schema(type="integer", default=1)
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Nombre d'éléments par page",
     *         required=false,
     *         @OA\Schema(type="integer", default=15)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Liste des dépenses récupérée avec succès",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="data",
     *                 type="array",
     *                 @OA\Items(ref="#/components/schemas/Expense")
     *             ),
     *             @OA\Property(
     *                 property="links",
     *                 type="object",
     *                 @OA\Property(property="first", type="string", example="http://kenam.ci/api/expenses?page=1"),
     *                 @OA\Property(property="last", type="string", example="http://kenam.ci/api/expenses?page=5"),
     *                 @OA\Property(property="prev", type="string", example="http://kenam.ci/api/expenses?page=1"),
     *                 @OA\Property(property="next", type="string", example="http://kenam.ci/api/expenses?page=3")
     *             ),
     *             @OA\Property(
     *                 property="meta",
     *                 type="object",
     *                 @OA\Property(property="current_page", type="integer", example=2),
     *                 @OA\Property(property="from", type="integer", example=16),
     *                 @OA\Property(property="last_page", type="integer", example=5),
     *                 @OA\Property(property="path", type="string", example="http://kenam.ci/api/expenses"),
     *                 @OA\Property(property="per_page", type="integer", example=15),
     *                 @OA\Property(property="to", type="integer", example=30),
     *                 @OA\Property(property="total", type="integer", example=75)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Non authentifié"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Accès non autorisé"
     *     )
     * )
     */
    public function index()
    {
        $this->authorize('viewAny', Expense::class);
        
        $user = Auth::user();
        
        // Si l'utilisateur est admin, voir toutes les dépenses, sinon seulement les siennes
        $query = $user->isAdmin() 
            ? Expense::with(['user', 'operation', 'approver'])
            : $user->expenses()->with(['operation', 'approver']);
            
        // Filtrage par statut si spécifié
        if (request()->has('statut')) {
            $query->where('statut', request('statut'));
        }
        
        // Filtrage par service si spécifié
        if (request()->has('service')) {
            $query->where('service_concerne', request('service'));
        }
        
        $expenses = $query->latest('date_depense')->paginate(15);
        
        return response()->json($expenses);
    }

    /**
     * @OA\Post(
     *     path="/api/expenses",
     *     summary="Créer une nouvelle dépense",
     *     tags={"Dépenses"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Données de la dépense à créer",
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"categorie", "montant", "date_depense", "mode_paiement", "service_concerne"},
     *                 @OA\Property(
     *                     property="categorie",
     *                     type="string",
     *                     maxLength=100,
     *                     example="Fournitures de bureau"
     *                 ),
     *                 @OA\Property(
     *                     property="description",
     *                     type="string",
     *                     nullable=true,
     *                     example="Achat de fournitures pour le service comptabilité"
     *                 ),
     *                 @OA\Property(
     *                     property="montant",
     *                     type="number",
     *                     format="float",
     *                     minimum=0,
     *                     example=125.50
     *                 ),
     *                 @OA\Property(
     *                     property="date_depense",
     *                     type="string",
     *                     format="date",
     *                     example="2025-10-18"
     *                 ),
     *                 @OA\Property(
     *                     property="operation_id",
     *                     type="integer",
     *                     format="int64",
     *                     nullable=true,
     *                     example=1
     *                 ),
     *                 @OA\Property(
     *                     property="fournisseur",
     *                     type="string",
     *                     maxLength=100,
     *                     nullable=true,
     *                     example="PAPETERIE CENTRALE"
     *                 ),
     *                 @OA\Property(
     *                     property="mode_paiement",
     *                     type="string",
     *                     enum={"espece", "cheque", "virement", "carte_bancaire"},
     *                     example="carte_bancaire"
     *                 ),
     *                 @OA\Property(
     *                     property="service_concerne",
     *                     type="string",
     *                     maxLength=100,
     *                     example="Comptabilité"
     *                 ),
     *                 @OA\Property(
     *                     property="budget_prevu",
     *                     type="number",
     *                     format="float",
     *                     minimum=0,
     *                     nullable=true,
     *                     example=1000.00
     *                 ),
     *                 @OA\Property(
     *                     property="justificatif",
     *                     type="string",
     *                     format="binary",
     *                     description="Fichier justificatif (PDF, JPG, PNG, max 2MB)",
     *                     nullable=true
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Dépense créée avec succès",
     *         @OA\JsonContent(ref="#/components/schemas/Expense")
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Données invalides",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Les données fournies sont invalides."),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 @OA\Property(
     *                     property="champ",
     *                     type="array",
     *                     @OA\Items(type="string", example="Le champ est obligatoire.")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Non authentifié"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Accès non autorisé"
     *     )
     * )
     */
    public function store(Request $request)
    {
        $this->authorize('create', Expense::class);
        
        $validated = $request->validate([
            'categorie' => 'required|string|max:100',
            'description' => 'nullable|string',
            'montant' => 'required|numeric|min:0',
            'date_depense' => 'required|date',
            'operation_id' => 'nullable|exists:operations,id',
            'fournisseur' => 'nullable|string|max:100',
            'mode_paiement' => 'required|string|in:espece,cheque,virement,carte_bancaire',
            'service_concerne' => 'required|string|max:100',
            'budget_prevu' => 'nullable|numeric|min:0',
            'justificatif' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);
        
        // Gestion du fichier justificatif
        if ($request->hasFile('justificatif')) {
            $path = $request->file('justificatif')->store('justificatifs', 'public');
            $validated['justificatif'] = $path;
        }
        
        // Génération d'une référence unique
        $validated['reference'] = 'DEP-' . now()->format('Ymd') . '-' . strtoupper(Str::random(6));
        $validated['user_id'] = Auth::id();
        $validated['statut'] = 'en_attente';
        
        $expense = Expense::create($validated);
        
        return response()->json($expense->load(['user', 'operation']), 201);
    }

    /**
     * @OA\Get(
     *     path="/api/expenses/{expense}",
     *     summary="Afficher les détails d'une dépense",
     *     tags={"Dépenses"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="expense",
     *         in="path",
     *         required=true,
     *         description="ID de la dépense",
     *         @OA\Schema(type="integer", format="int64")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Détails de la dépense récupérés avec succès",
     *         @OA\JsonContent(ref="#/components/schemas/Expense")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Non authentifié"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Accès non autorisé"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Dépense non trouvée"
     *     )
     * )
     */
    public function show(Expense $expense)
    {
        $this->authorize('view', $expense);
        
        return response()->json($expense->load(['user', 'operation', 'approver']));
    }

    /**
     * @OA\Put(
     *     path="/api/expenses/{expense}",
     *     summary="Mettre à jour une dépense existante",
     *     tags={"Dépenses"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="expense",
     *         in="path",
     *         required=true,
     *         description="ID de la dépense à mettre à jour",
     *         @OA\Schema(type="integer", format="int64")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Données à mettre à jour",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="categorie",
     *                     type="string",
     *                     maxLength=100,
     *                     example="Fournitures de bureau"
     *                 ),
     *                 @OA\Property(
     *                     property="description",
     *                     type="string",
     *                     nullable=true,
     *                     example="Achat de fournitures mises à jour"
     *                 ),
     *                 @OA\Property(
     *                     property="montant",
     *                     type="number",
     *                     format="float",
     *                     minimum=0,
     *                     example=150.75
     *                 ),
     *                 @OA\Property(
     *                     property="date_depense",
     *                     type="string",
     *                     format="date",
     *                     example="2025-10-18"
     *                 ),
     *                 @OA\Property(
     *                     property="operation_id",
     *                     type="integer",
     *                     format="int64",
     *                     nullable=true,
     *                     example=1
     *                 ),
     *                 @OA\Property(
     *                     property="fournisseur",
     *                     type="string",
     *                     maxLength=100,
     *                     nullable=true,
     *                     example="PAPETERIE CENTRALE"
     *                 ),
     *                 @OA\Property(
     *                     property="mode_paiement",
     *                     type="string",
     *                     enum={"espece", "cheque", "virement", "carte_bancaire"},
     *                     example="carte_bancaire"
     *                 ),
     *                 @OA\Property(
     *                     property="service_concerne",
     *                     type="string",
     *                     maxLength=100,
     *                     example="Comptabilité"
     *                 ),
     *                 @OA\Property(
     *                     property="budget_prevu",
     *                     type="number",
     *                     format="float",
     *                     minimum=0,
     *                     nullable=true,
     *                     example=1200.00
     *                 ),
     *                 @OA\Property(
     *                     property="justificatif",
     *                     type="string",
     *                     format="binary",
     *                     description="Nouveau fichier justificatif (PDF, JPG, PNG, max 2MB)",
     *                     nullable=true
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Dépense mise à jour avec succès",
     *         @OA\JsonContent(ref="#/components/schemas/Expense")
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Données invalides"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Non authentifié"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Accès non autorisé"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Dépense non trouvée"
     *     )
     * )
     */
    public function update(Request $request, Expense $expense)
    {
        $this->authorize('update', $expense);
        
        $validated = $request->validate([
            'categorie' => 'sometimes|required|string|max:100',
            'description' => 'nullable|string',
            'montant' => 'sometimes|required|numeric|min:0',
            'date_depense' => 'sometimes|required|date',
            'operation_id' => 'nullable|exists:operations,id',
            'fournisseur' => 'nullable|string|max:100',
            'mode_paiement' => 'sometimes|required|string|in:espece,cheque,virement,carte_bancaire',
            'service_concerne' => 'sometimes|required|string|max:100',
            'budget_prevu' => 'nullable|numeric|min:0',
            'justificatif' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'rejet_raison' => 'nullable|required_if:statut,rejete|string|max:255',
        ]);
        
        // Gestion du fichier justificatif
        if ($request->hasFile('justificatif')) {
            // Supprimer l'ancien fichier s'il existe
            if ($expense->justificatif) {
                Storage::disk('public')->delete($expense->justificatif);
            }
            $path = $request->file('justificatif')->store('justificatifs', 'public');
            $validated['justificatif'] = $path;
        }
        
        $expense->update($validated);
        
        return response()->json($expense->refresh()->load(['user', 'operation', 'approver']));
    }
    
    /**
     * @OA\Post(
     *     path="/api/expenses/{expense}/approve",
     *     summary="Approuver une dépense",
     *     tags={"Dépenses"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="expense",
     *         in="path",
     *         required=true,
     *         description="ID de la dépense à approuver",
     *         @OA\Schema(type="integer", format="int64")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Dépense approuvée avec succès",
     *         @OA\JsonContent(ref="#/components/schemas/Expense")
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Action non autorisée sur cette dépense"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Non authentifié"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Accès non autorisé"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Dépense non trouvée"
     *     )
     * )
     */
    public function approve(Expense $expense)
    {
        $this->authorize('approve', $expense);
        
        $expense->update([
            'statut' => 'approuvee',
            'approuve_par' => Auth::id(),
            'date_approbation' => now(),
            'rejet_raison' => null,
        ]);
        
        return response()->json($expense->refresh()->load('approver'));
    }
    
    /**
     * @OA\Post(
     *     path="/api/expenses/{expense}/reject",
     *     summary="Rejeter une dépense",
     *     tags={"Dépenses"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="expense",
     *         in="path",
     *         required=true,
     *         description="ID de la dépense à rejeter",
     *         @OA\Schema(type="integer", format="int64")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Raison du rejet",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 required={"raison"},
     *                 @OA\Property(
     *                     property="raison",
     *                     type="string",
     *                     maxLength=255,
     *                     example="Justificatif manquant ou incomplet"
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Dépense rejetée avec succès",
     *         @OA\JsonContent(ref="#/components/schemas/Expense")
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Données invalides ou action non autorisée"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Non authentifié"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Accès non autorisé"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Dépense non trouvée"
     *     )
     * )
     */
    public function reject(Request $request, Expense $expense)
    {
        $this->authorize('reject', $expense);
        
        $validated = $request->validate([
            'raison' => 'required|string|max:255',
        ]);
        
        $expense->update([
            'statut' => 'rejetee',
            'rejet_raison' => $validated['raison'],
            'approuve_par' => Auth::id(),
            'date_approbation' => now(),
        ]);
        
        return response()->json($expense->refresh()->load('approver'));
    }

    /**
     * @OA\Delete(
     *     path="/api/expenses/{expense}",
     *     summary="Supprimer une dépense (soft delete)",
     *     tags={"Dépenses"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="expense",
     *         in="path",
     *         required=true,
     *         description="ID de la dépense à supprimer",
     *         @OA\Schema(type="integer", format="int64")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Dépense supprimée avec succès"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Non authentifié"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Accès non autorisé"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Dépense non trouvée"
     *     )
     * )
     */
    public function destroy(Expense $expense)
    {
        $this->authorize('delete', $expense);
        
        // Supprimer le fichier justificatif s'il existe
        if ($expense->justificatif) {
            Storage::disk('public')->delete($expense->justificatif);
        }
        
        $expense->delete();
        
        return response()->json(null, 204);
    }
    
    /**
     * @OA\Get(
     *     path="/api/expenses/{expense}/download",
     *     summary="Télécharger le justificatif d'une dépense",
     *     tags={"Dépenses"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="expense",
     *         in="path",
     *         required=true,
     *         description="ID de la dépense",
     *         @OA\Schema(type="integer", format="int64")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Fichier du justificatif",
     *         @OA\MediaType(
     *             mediaType="application/octet-stream",
     *             @OA\Schema(
     *                 type="string",
     *                 format="binary"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Non authentifié"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Accès non autorisé"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Justificatif non trouvé"
     *     )
     * )
     */
    public function downloadJustificatif(Expense $expense)
    {
        $this->authorize('view', $expense);
        
        if (!$expense->justificatif || !Storage::disk('public')->exists($expense->justificatif)) {
            return response()->json(['message' => 'Aucun justificatif trouvé pour cette dépense.'], 404);
        }
        
        return Storage::disk('public')->download(
            $expense->justificatif,
            'justificatif_' . $expense->reference . '.' . pathinfo($expense->justificatif, PATHINFO_EXTENSION)
        );
    }
    
    /**
     * @OA\Get(
     *     path="/api/reports/expenses-by-category",
     *     summary="Rapport des dépenses par catégorie",
     *     tags={"Rapports"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Rapport des dépenses par catégorie",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 type="object",
     *                 @OA\Property(property="categorie", type="string", example="Fournitures de bureau"),
     *                 @OA\Property(property="total", type="number", format="float", example=1250.50),
     *                 @OA\Property(property="count", type="integer", example=15)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Non authentifié"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Accès non autorisé"
     *     )
     * )
     */
    public function expensesByCategory()
    {
        $this->authorize('viewAny', Expense::class);
        
        $expenses = Expense::selectRaw('categorie, SUM(montant) as total, COUNT(*) as count')
            ->groupBy('categorie')
            ->get();
            
        return response()->json($expenses);
    }
    
    /**
     * @OA\Get(
     *     path="/api/reports/expenses-by-status",
     *     summary="Rapport des dépenses par statut",
     *     tags={"Rapports"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="Rapport des dépenses par statut",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 type="object",
     *                 @OA\Property(property="statut", type="string", example="approuvee"),
     *                 @OA\Property(property="total", type="number", format="float", example=3250.75),
     *                 @OA\Property(property="count", type="integer", example=42)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Non authentifié"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Accès non autorisé"
     *     )
     * )
     */
    public function expensesByStatus()
    {
        $this->authorize('viewAny', Expense::class);
        
        $expenses = Expense::selectRaw('statut, SUM(montant) as total, COUNT(*) as count')
            ->groupBy('statut')
            ->get();
            
        return response()->json($expenses);
    }
    
    /**
     * @OA\Get(
     *     path="/api/reports/expenses-by-period",
     *     summary="Rapport des dépenses par période",
     *     tags={"Rapports"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="period",
     *         in="query",
     *         description="Période de regroupement (day, month, year)",
     *         required=false,
     *         @OA\Schema(
     *             type="string",
     *             enum={"day", "month", "year"},
     *             default="month"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Rapport des dépenses par période",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 type="object",
     *                 @OA\Property(property="period", type="string", example="2025-10"),
     *                 @OA\Property(property="total", type="number", format="float", example=1250.50),
     *                 @OA\Property(property="count", type="integer", example=8)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Période invalide"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Non authentifié"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Accès non autorisé"
     *     )
     * )
     */
    public function expensesByPeriod(Request $request)
    {
        $this->authorize('viewAny', Expense::class);
        
        $period = $request->input('period', 'month'); // day, month, year
        $format = [
            'day' => '%Y-%m-%d',
            'month' => '%Y-%m',
            'year' => '%Y'
        ][$period] ?? '%Y-%m';
        
        $expenses = Expense::selectRaw(
                "DATE_FORMAT(date_depense, '{$format}') as period, " .
                "SUM(montant) as total, COUNT(*) as count"
            )
            ->groupBy('period')
            ->orderBy('period')
            ->get();
            
        return response()->json($expenses);
    }

    /**
     * Affiche la page des paiements liés aux dépenses et opérations
     *
     * @return \Illuminate\View\View
     */
    public function paiements()
    {
        $this->authorize('viewAny', Expense::class);
        
        $user = Auth::user();
        
        // Récupérer les dépenses avec leurs paiements
        $query = Expense::query();
        
        // Filtrer selon les permissions de l'utilisateur
        if (!$user->hasRole('admin')) {
            $query->where('user_id', $user->id);
        }
        
        $expenses = $query->with(['user', 'operation'])
            ->whereIn('mode_paiement', ['virement', 'cheque', 'espèces', 'carte_bancaire'])
            ->latest('date_depense')
            ->paginate(15);
        
        // Calculer les totaux par mode de paiement
        $paiementsParMode = Expense::selectRaw('mode_paiement, COALESCE(SUM(montant), 0) as total, COUNT(*) as count')
            ->groupBy('mode_paiement')
            ->get();
        
        // Calculer les totaux par statut
        $paiementsParStatut = Expense::selectRaw('statut, COALESCE(SUM(montant), 0) as total, COUNT(*) as count')
            ->groupBy('statut')
            ->get();
        
        // Total des paiements en attente
        $totalEnAttente = Expense::where('statut', 'en_attente')->sum('montant') ?? 0;
        
        // Total des paiements approuvés
        $totalApprouves = Expense::where('statut', 'approuvee')->sum('montant') ?? 0;
        
        return view('comptabilite.paiements', compact(
            'expenses',
            'paiementsParMode',
            'paiementsParStatut',
            'totalEnAttente',
            'totalApprouves'
        ));
    }
}

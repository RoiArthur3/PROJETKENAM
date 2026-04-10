<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Vehicule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AjaxFournisseurEnginsController extends Controller
{
    /**
     * Retourner la liste des engins selon le type de fournisseur
     */
    public function getEngins(Request $request)
    {
        try {
            $type = $request->input('type');

            // Validation du type
            if (!in_array($type, ['kenam', 'autres', 'tous'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Type de fournisseur invalide',
                    'data' => []
                ], 400);
            }

            $engins = [];

            if ($type === 'kenam') {
                // Engins internes (Kenam)
                $engins = Vehicule::where('provenance', 'kenam')
                    ->where('statut', 'disponible')
                    ->orderBy('immatriculation')
                    ->select('id', 'immatriculation', 'marque', 'modele', 'type_vehicule', 'statut')
                    ->get();

            } elseif ($type === 'autres') {
                // Engins externes (autres fournisseurs)
                $engins = Vehicule::where('provenance', 'fournisseur')
                    ->where('statut', 'disponible')
                    ->orderBy('immatriculation')
                    ->select('id', 'immatriculation', 'marque', 'modele', 'type_vehicule', 'statut')
                    ->get();

            } elseif ($type === 'tous') {
                // Tous les engins disponibles
                $engins = Vehicule::where('statut', 'disponible')
                    ->orderBy('provenance')
                    ->orderBy('immatriculation')
                    ->select('id', 'immatriculation', 'marque', 'modele', 'type_vehicule', 'statut', 'provenance')
                    ->get();
            }

            // Formater les données pour l'affichage
            $formattedEngins = $engins->map(function($engin) {
                $label = $engin->immatriculation;
                if ($engin->marque || $engin->modele) {
                    $label .= ' - ' . trim($engin->marque . ' ' . $engin->modele);
                }
                if ($engin->type_vehicule) {
                    $label .= ' (' . $engin->type_vehicule . ')';
                }
                if (isset($engin->provenance)) {
                    $label .= ' [' . ucfirst($engin->provenance) . ']';
                }

                return [
                    'id' => $engin->id,
                    'immatriculation' => $engin->immatriculation,
                    'label' => $label,
                    'marque' => $engin->marque,
                    'modele' => $engin->modele,
                    'type_vehicule' => $engin->type_vehicule,
                    'statut' => $engin->statut,
                    'provenance' => $engin->provenance ?? null
                ];
            });

            return response()->json([
                'success' => true,
                'message' => sprintf('%d engin(s) trouvé(s)', $formattedEngins->count()),
                'data' => $formattedEngins,
                'count' => $formattedEngins->count(),
                'type' => $type
            ]);

        } catch (\Exception $e) {
            Log::error('Erreur lors de la récupération des engins', [
                'error' => $e->getMessage(),
                'type' => $request->input('type'),
                'request' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des engins: ' . $e->getMessage(),
                'data' => []
            ], 500);
        }
    }

    /**
     * Alternative: Retourner les engins au format simple pour compatibilité
     */
    public function getEnginsSimple(Request $request)
    {
        try {
            $type = $request->input('type');
            $engins = [];

            if ($type === 'kenam') {
                $engins = Vehicule::where('provenance', 'kenam')
                    ->where('statut', 'disponible')
                    ->orderBy('immatriculation')
                    ->get();
            } elseif ($type === 'autres') {
                $engins = Vehicule::where('provenance', 'fournisseur')
                    ->where('statut', 'disponible')
                    ->orderBy('immatriculation')
                    ->get();
            }

            return response()->json($engins);

        } catch (\Exception $e) {
            Log::error('Erreur AJAX engins simple', ['error' => $e->getMessage()]);
            return response()->json([]);
        }
    }
}

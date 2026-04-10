<?php

namespace App\Http\Controllers\RH;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Personnel;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class CameraSyncController extends Controller
{
    public function index(Request $request)
    {
        $query = Personnel::query();

        // Filtres
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('nom', 'like', "%{$search}%")
                  ->orWhere('prenoms', 'like', "%{$search}%")
                  ->orWhere('matricule', 'like', "%{$search}%");
            });
        }

        if ($request->filled('sync_status')) {
            if ($request->sync_status === 'synced') {
                $query->whereNotNull('camera_person_id');
            } elseif ($request->sync_status === 'not_synced') {
                $query->whereNull('camera_person_id');
            }
        }

        $personnels = $query->orderBy('created_at', 'desc')->paginate(20);

        // Statistiques
        $stats = [
            'total' => Personnel::count(),
            'synced' => Personnel::whereNotNull('camera_person_id')->count(),
            'not_synced' => Personnel::whereNull('camera_person_id')->count(),
            'with_photo' => Personnel::whereNotNull('photo_profil')->count(),
        ];

        return view('rh.camera-sync.index', compact('personnels', 'stats'));
    }

    public function create($id)
    {
        $personnel = Personnel::findOrFail($id);
        return view('rh.camera-sync.create', compact('personnel'));
    }

    public function store(Request $request, $id)
    {
        $personnel = Personnel::findOrFail($id);

        $validated = $request->validate([
            'camera_person_id' => 'required|string|max:100',
            'photo_profil' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        try {
            // Upload de la photo
            if ($request->hasFile('photo_profil')) {
                $photoPath = $request->file('photo_profil')->store('personnel/photos', 'public');
                $personnel->photo_profil = $photoPath;
            }

            // Mise à jour du camera_person_id
            $personnel->camera_person_id = $validated['camera_person_id'];

            // Mise à jour du statut de synchronisation si la colonne existe
            if (Schema::hasColumn('personnel', 'facial_sync_status')) {
                $personnel->facial_sync_status = 'pending';
            }

            $personnel->save();

            // Tentative de synchronisation avec la caméra
            $syncResult = $this->syncToCamera($personnel);

            if ($syncResult['success']) {
                $message = 'Personnel synchronisé avec succès avec la caméra !';
                if (Schema::hasColumn('personnel', 'facial_sync_status')) {
                    $personnel->facial_sync_status = 'synced';
                    $personnel->save();
                }
            } else {
                $message = 'ID caméra enregistré mais la synchronisation a échoué: ' . $syncResult['message'];
                if (Schema::hasColumn('personnel', 'facial_sync_status')) {
                    $personnel->facial_sync_status = 'failed';
                    $personnel->save();
                }
            }

            return redirect()->route('rh.camera-sync.index')
                ->with('success', $message);

        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors de la synchronisation: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $personnel = Personnel::findOrFail($id);
        return view('rh.camera-sync.edit', compact('personnel'));
    }

    public function update(Request $request, $id)
    {
        $personnel = Personnel::findOrFail($id);

        $validated = $request->validate([
            'camera_person_id' => 'nullable|string|max:100',
            'photo_profil' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        try {
            // Upload de la nouvelle photo si fournie
            if ($request->hasFile('photo_profil')) {
                // Supprimer l'ancienne photo si elle existe
                if ($personnel->photo_profil) {
                    Storage::disk('public')->delete($personnel->photo_profil);
                }
                $photoPath = $request->file('photo_profil')->store('personnel/photos', 'public');
                $personnel->photo_profil = $photoPath;
            }

            // Mise à jour du camera_person_id si fourni
            if (isset($validated['camera_person_id'])) {
                $personnel->camera_person_id = $validated['camera_person_id'];
            }

            // Si modification des données essentielles, resynchroniser
            $essentialDataChanged = false;
            if ($request->hasFile('photo_profil') ||
                (isset($validated['camera_person_id']) && $validated['camera_person_id'] !== $personnel->getOriginal('camera_person_id'))) {
                $essentialDataChanged = true;
            }

            if ($essentialDataChanged && !empty($personnel->camera_person_id)) {
                $syncResult = $this->syncToCamera($personnel);

                if ($syncResult['success']) {
                    $personnel->facial_sync_status = 'synced';
                    $message = 'Synchronisation mise à jour avec succès !';
                } else {
                    $personnel->facial_sync_status = 'failed';
                    $message = 'Données mises à jour mais synchronisation échouée: ' . $syncResult['message'];
                }
            } else {
                $message = 'Informations mises à jour avec succès';
            }

            $personnel->save();

            return redirect()->route('rh.camera-sync.index')
                ->with('success', $message);

        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors de la mise à jour: ' . $e->getMessage());
        }
    }

    public function syncSingle($id)
    {
        $personnel = Personnel::findOrFail($id);

        if (empty($personnel->camera_person_id) || empty($personnel->photo_profil)) {
            return back()->with('error', 'ID caméra et photo requis pour la synchronisation');
        }

        try {
            $syncResult = $this->syncToCamera($personnel);

            if ($syncResult['success']) {
                if (Schema::hasColumn('personnel', 'facial_sync_status')) {
                    $personnel->facial_sync_status = 'synced';
                    $personnel->save();
                }
                return back()->with('success', 'Synchronisation réussie avec la caméra !');
            } else {
                if (Schema::hasColumn('personnel', 'facial_sync_status')) {
                    $personnel->facial_sync_status = 'failed';
                    $personnel->save();
                }
                return back()->with('error', 'Échec de synchronisation: ' . $syncResult['message']);
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors de la synchronisation: ' . $e->getMessage());
        }
    }

    public function bulkSync(Request $request)
    {
        $personnelIds = $request->input('personnel_ids', []);

        if (empty($personnelIds)) {
            return back()->with('error', 'Veuillez sélectionner au moins un personnel');
        }

        $personnels = Personnel::whereIn('id', $personnelIds)
            ->whereNotNull('camera_person_id')
            ->whereNotNull('photo_profil')
            ->get();

        $successCount = 0;
        $failedCount = 0;

        foreach ($personnels as $personnel) {
            try {
                $syncResult = $this->syncToCamera($personnel);

                if ($syncResult['success']) {
                    $successCount++;
                    if (Schema::hasColumn('personnel', 'facial_sync_status')) {
                        $personnel->facial_sync_status = 'synced';
                        $personnel->save();
                    }
                } else {
                    $failedCount++;
                    if (Schema::hasColumn('personnel', 'facial_sync_status')) {
                        $personnel->facial_sync_status = 'failed';
                        $personnel->save();
                    }
                }
            } catch (\Exception $e) {
                $failedCount++;
            }
        }

        $message = "Synchronisation terminée: {$successCount} réussie(s), {$failedCount} échouée(s)";

        if ($failedCount > 0) {
            return back()->with('warning', $message);
        } else {
            return back()->with('success', $message);
        }
    }

    private function syncToCamera($personnel)
    {
        try {
            // Simulation de synchronisation avec la caméra
            // Remplacer par la vraie logique de synchronisation Hikvision

            // Pour l'instant, simuler une réussite
            return [
                'success' => true,
                'message' => 'Synchronisation réussie',
                'camera_person_id' => $personnel->camera_person_id
            ];

            // Exemple de logique réelle:
            // $hikvisionService = app(HikvisionFaceSyncService::class);
            // return $hikvisionService->syncEmployeeToCamera($personnel);

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    public function destroy($id)
    {
        $personnel = Personnel::findOrFail($id);

        try {
            // Supprimer la photo si elle existe
            if ($personnel->photo_profil) {
                Storage::disk('public')->delete($personnel->photo_profil);
            }

            // Réinitialiser les informations de synchronisation
            $personnel->camera_person_id = null;
            $personnel->photo_profil = null;

            if (Schema::hasColumn('personnel', 'facial_sync_status')) {
                $personnel->facial_sync_status = null;
            }

            $personnel->save();

            return redirect()->route('rh.camera-sync.index')
                ->with('success', 'Synchronisation supprimée avec succès');

        } catch (\Exception $e) {
            return back()->with('error', 'Erreur lors de la suppression: ' . $e->getMessage());
        }
    }
}

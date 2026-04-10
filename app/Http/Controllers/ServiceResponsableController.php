<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ServiceResponsableController extends Controller
{
    /**
     * Afficher la liste des responsables de services
     */
    public function index()
    {
        $services = Service::with('responsable')
            ->orderBy('nom')
            ->paginate(15);

        return view('admin.services.responsables.index', compact('services'));
    }

    /**
     * Créer un compte responsable pour un service
     */
    public function create(Service $service)
    {
        return view('admin.services.responsables.create', compact('service'));
    }

    /**
     * Enregistrer un nouveau compte responsable
     */
    public function store(Request $request, Service $service)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'telephone' => 'nullable|string|max:20',
            'password' => 'required|string|min:6',
            'auto_generate_password' => 'boolean'
        ]);

        DB::beginTransaction();

        try {
            // Générer le mot de passe automatiquement si demandé
            $password = $request->auto_generate_password ? '12345678' : $request->password;

            // Créer l'utilisateur responsable
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->telephone,
                'password' => Hash::make($password),
                'email_verified_at' => now(),
                'is_active' => true,
            ]);

            // Assigner le rôle de responsable
            $user->assignRole('responsable');

            // Donner les permissions de base (requêtes est obligatoire pour tout le monde)
            $baseModules = ['dashboard', 'operations']; // operations = requêtes

            // Ajouter les modules spécifiques au service
            $serviceModules = \App\Models\ServiceModule::where('service_id', $service->id)
                ->where('can_access', true)
                ->pluck('module_code')
                ->toArray();

            // Fusionner les permissions
            $allModules = array_unique(array_merge($baseModules, $serviceModules));

            \App\Services\UserPermissionService::updateUserPermissions($user, $allModules);

            // Lier l'utilisateur au service
            $service->update([
                'responsable_id' => $user->id,
                'responsable_email' => $request->email,
                'responsable_telephone' => $request->telephone,
                'responsable_compte_auto' => $request->auto_generate_password,
                'responsable_compte_cree_le' => now(),
                'responsable_notes' => $request->notes ?? 'Compte créé automatiquement'
            ]);

            DB::commit();

            return redirect()
                ->route('admin.services.responsables.index')
                ->with('success', "Compte responsable créé pour {$service->nom}. Email: {$request->email}, Mot de passe: {$password}");

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur création compte responsable: ' . $e->getMessage());

            return back()
                ->withInput()
                ->with('error', 'Erreur lors de la création du compte responsable');
        }
    }

    /**
     * Modifier le mot de passe d'un responsable
     */
    public function editPassword(Service $service)
    {
        if (!$service->responsable) {
            return back()->with('error', 'Aucun responsable associé à ce service');
        }

        return view('admin.services.responsables.edit-password', compact('service'));
    }

    /**
     * Mettre à jour le mot de passe d'un responsable
     */
    public function updatePassword(Request $request, Service $service)
    {
        $request->validate([
            'password' => 'required|string|min:6|confirmed',
            'auto_generate_password' => 'boolean'
        ]);

        if (!$service->responsable) {
            return back()->with('error', 'Aucun responsable associé à ce service');
        }

        try {
            $password = $request->auto_generate_password ? '12345678' : $request->password;

            $service->responsable->update([
                'password' => Hash::make($password)
            ]);

            $service->responsable_notes = "Mot de passe mis à jour le " . now()->format('d/m/Y H:i');
            $service->save();

            return redirect()
                ->route('admin.services.responsables.index')
                ->with('success', "Mot de passe mis à jour pour {$service->responsable->name}. Nouveau mot de passe: {$password}");

        } catch (\Exception $e) {
            Log::error('Erreur mise à jour mot de passe: ' . $e->getMessage());

            return back()
                ->withInput()
                ->with('error', 'Erreur lors de la mise à jour du mot de passe');
        }
    }

    /**
     * Désactiver/Activer le compte d'un responsable
     */
    public function toggleStatus(Service $service)
    {
        if (!$service->responsable) {
            return back()->with('error', 'Aucun responsable associé à ce service');
        }

        try {
            $service->responsable->update([
                'is_active' => !$service->responsable->is_active
            ]);

            $status = $service->responsable->is_active ? 'activé' : 'désactivé';

            return back()->with('success', "Compte de {$service->responsable->name} {$status}");

        } catch (\Exception $e) {
            Log::error('Erreur changement statut: ' . $e->getMessage());

            return back()->with('error', 'Erreur lors du changement de statut');
        }
    }

    /**
     * Supprimer le compte responsable d'un service
     */
    public function destroy(Service $service)
    {
        if (!$service->responsable) {
            return back()->with('error', 'Aucun responsable associé à ce service');
        }

        DB::beginTransaction();

        try {
            // Détacher le responsable du service
            $service->update([
                'responsable_id' => null,
                'responsable_email' => null,
                'responsable_telephone' => null,
                'responsable_compte_auto' => false,
                'responsable_compte_cree_le' => null,
                'responsable_notes' => 'Compte responsable supprimé le ' . now()->format('d/m/Y H:i')
            ]);

            // Supprimer le rôle responsable de l'utilisateur
            $service->responsable->removeRole('responsable');

            DB::commit();

            return back()->with('success', "Compte responsable supprimé pour {$service->nom}");

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur suppression compte responsable: ' . $e->getMessage());

            return back()->with('error', 'Erreur lors de la suppression du compte responsable');
        }
    }

    /**
     * Afficher les modules d'un service
     */
    public function modules(Service $service)
    {
        $allModules = \App\Services\UserPermissionService::getAllModules();
        $activeModules = \App\Models\ServiceModule::where('service_id', $service->id)
            ->where('can_access', true)
            ->pluck('module_code')
            ->toArray();

        return view('admin.services.responsables.modules', compact('service', 'allModules', 'activeModules'));
    }

    /**
     * Mettre à jour les modules d'un service
     */
    public function updateModules(Request $request, Service $service)
    {
        $selectedModules = $request->input('modules', []);

        // S'assurer que les modules obligatoires sont toujours inclus
        $mandatoryModules = ['dashboard', 'operations']; // operations = requêtes
        $finalModules = array_unique(array_merge($selectedModules, $mandatoryModules));

        DB::beginTransaction();

        try {
            // Supprimer tous les modules existants pour ce service
            \App\Models\ServiceModule::where('service_id', $service->id)->delete();

            // Ajouter les nouveaux modules
            foreach ($finalModules as $moduleCode) {
                \App\Models\ServiceModule::create([
                    'service_id' => $service->id,
                    'module_code' => $moduleCode,
                    'can_access' => true
                ]);
            }

            // Mettre à jour les permissions du responsable si existant
            if ($service->responsable) {
                \App\Services\UserPermissionService::updateUserPermissions($service->responsable, $finalModules);
            }

            DB::commit();

            return back()->with('success', 'Modules du service mis à jour avec succès');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Erreur mise à jour modules service: ' . $e->getMessage());

            return back()->with('error', 'Erreur lors de la mise à jour des modules');
        }
    }

    /**
     * Générer des comptes responsables pour tous les services
     */
    public function generateAll()
    {
        $services = Service::whereNull('responsable_id')->get();
        $created = 0;
        $errors = [];

        foreach ($services as $service) {
            try {
                // Générer un email automatiquement
                $email = strtolower(str_replace(' ', '.', $service->nom)) . '@kenam.ci';

                // Vérifier si l'email existe déjà
                if (User::where('email', $email)->exists()) {
                    $email = strtolower(str_replace(' ', '.', $service->nom)) . '.' . time() . '@kenam.ci';
                }

                // Créer l'utilisateur
                $user = User::create([
                    'name' => 'Responsable ' . $service->nom,
                    'email' => $email,
                    'password' => Hash::make('12345678'),
                    'email_verified_at' => now(),
                    'is_active' => true,
                ]);

                $user->assignRole('responsable');

                // Donner les permissions de base (requêtes est obligatoire pour tout le monde)
                $baseModules = ['dashboard', 'operations']; // operations = requêtes

                // Ajouter les modules spécifiques au service
                $serviceModules = \App\Models\ServiceModule::where('service_id', $service->id)
                    ->where('can_access', true)
                    ->pluck('module_code')
                    ->toArray();

                // Fusionner les permissions
                $allModules = array_unique(array_merge($baseModules, $serviceModules));

                \App\Services\UserPermissionService::updateUserPermissions($user, $allModules);

                // Lier au service
                $service->update([
                    'responsable_id' => $user->id,
                    'responsable_email' => $email,
                    'responsable_compte_auto' => true,
                    'responsable_compte_cree_le' => now(),
                    'responsable_notes' => 'Compte généré automatiquement'
                ]);

                $created++;

            } catch (\Exception $e) {
                $errors[] = "Service {$service->nom}: " . $e->getMessage();
            }
        }

        return back()->with('success', " {$created} comptes responsables créés avec mot de passe: 12345678")
                    ->with('errors', $errors);
    }
}

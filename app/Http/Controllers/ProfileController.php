<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\Personnel;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Espace profil complet de l'utilisateur connecte.
     */
    public function dashboard(Request $request): View
    {
        $user = $request->user();

        $personnel = Personnel::query()
            ->where('user_id', $user->id)
            ->first();

        $lastPaie = null;
        if ($personnel) {
            $lastPaie = DB::table('personnel_paies')
                ->where('personnel_id', $personnel->id)
                ->orderByDesc('periode')
                ->orderByDesc('id')
                ->first();
        }

        return view('profile.dashboard', [
            'user' => $user,
            'personnel' => $personnel,
            'lastPaie' => $lastPaie,
            'shortcuts' => $this->buildAccessShortcuts($user),
        ]);
    }

    /**
     * Mettre a jour la photo du profil employe lie.
     */
    public function updatePhoto(Request $request): RedirectResponse
    {
        $request->validate([
            'photo_profil' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $personnel = Personnel::query()->where('user_id', $request->user()->id)->first();
        if (!$personnel) {
            return Redirect::route('profile.dashboard')
                ->with('error', 'Aucun dossier employe lie a ce compte.');
        }

        if ($personnel->photo_profil) {
            Storage::disk('public')->delete($personnel->photo_profil);
        }

        $photoPath = $request->file('photo_profil')->store('personnel/photos', 'public');
        $personnel->update(['photo_profil' => $photoPath]);

        return Redirect::route('profile.dashboard')->with('success', 'Photo de profil mise a jour.');
    }

    private function buildAccessShortcuts($user): array
    {
        $candidates = [
            ['module' => 'operations', 'label' => 'Operations', 'icon' => 'fas fa-tasks', 'url' => '/operations'],
            ['module' => 'validations', 'label' => 'Validations', 'icon' => 'fas fa-check-circle', 'url' => '/validations/pending'],
            ['module' => 'tresorerie', 'label' => 'Tresorerie', 'icon' => 'fas fa-wallet', 'url' => '/tresorerie/dashboard'],
            ['module' => 'accounting', 'label' => 'Comptabilite', 'icon' => 'fas fa-calculator', 'url' => '/comptabilite/dashboard'],
            ['module' => 'hr', 'label' => 'Ressources Humaines', 'icon' => 'fas fa-users', 'url' => '/rh/dashboard'],
            ['module' => 'materiel', 'label' => 'Materiel', 'icon' => 'fas fa-truck', 'url' => '/materiel/cost-control'],
            ['module' => 'warehouse', 'label' => 'Stock', 'icon' => 'fas fa-boxes', 'url' => '/warehouse/dashboard'],
            ['module' => 'commercial', 'label' => 'Commercial', 'icon' => 'fas fa-chart-line', 'url' => '/commercial/dashboard'],
            ['module' => 'fournisseurs', 'label' => 'Fournisseurs', 'icon' => 'fas fa-handshake', 'url' => '/fournisseurs/dashboard'],
            ['module' => 'projects', 'label' => 'Projets', 'icon' => 'fas fa-project-diagram', 'url' => '/projets/dashboard'],
        ];

        $shortcuts = [];
        foreach ($candidates as $candidate) {
            if ($user->canAccessModule($candidate['module'])) {
                $shortcuts[] = $candidate;
            }
        }

        return $shortcuts;
    }

    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}

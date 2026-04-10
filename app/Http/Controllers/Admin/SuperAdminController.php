<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class SuperAdminController extends Controller
{
    /**
     * Afficher le formulaire de création d'un superadmin
     */
    public function create()
    {
        return view('admin.superadmin.create');
    }

    /**
     * Enregistrer un nouveau superadmin
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        // Créer l'utilisateur avec le rôle superadmin
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'superadmin',
            'is_active' => true,
            'can_access_dashboard' => true,
            'can_access_operations' => true,
            'can_access_hr' => true,
            'can_access_fleet' => true,
            'can_access_suppliers' => true,
            'can_access_warehouse' => true,
            'can_access_accounting' => true,
            'can_access_invoicing' => true,
            'can_access_reporting' => true,
            'can_access_commercial' => true,
            'can_access_prospection' => true,
            'can_access_ateliers' => true,
            'can_access_projects' => true,
            'can_access_audit' => true,
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', 'Superadmin créé avec succès !');
    }
}

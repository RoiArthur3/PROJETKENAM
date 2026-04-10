<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class TempAdminController extends Controller
{
    public function createAdmin()
    {
        // Vérifier si l'utilisateur existe déjà
        $user = User::where('email', 'admin@kenamservices.com')->first();

        if (!$user) {
            // Créer l'utilisateur
            $user = new User();
            $user->name = 'Admin';
            $user->email = 'admin@kenamservices.com';
            $user->password = Hash::make('admin123');
            $user->is_active = true;
            $user->save();

            // Créer les rôles s'ils n'existent pas
            $adminRole = Role::firstOrCreate(
                ['name' => 'admin'],
                ['description' => 'Administrateur système', 'guard_name' => 'web']
            );

            // Attacher le rôle admin à l'utilisateur
            $user->roles()->attach($adminRole);

            return "Utilisateur admin créé avec succès. Email: admin@kenamservices.com, Mot de passe: admin123";
        }

        return "L'utilisateur admin existe déjà.";
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Personnel;
use App\Models\User;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    /**
     * Search employees by name or other attributes
     * GET /api/employees/search?q=searchtext
     */
    public function search(Request $request)
    {
        $query = $request->input('q', '');

        if (strlen($query) < 2) {
            return response()->json([]);
        }

        // Search in Personnel table (RH)
        $personnel = Personnel::query()
            ->where(function ($q) use ($query) {
                $q->where('nom', 'LIKE', "%{$query}%")
                  ->orWhere('prenoms', 'LIKE', "%{$query}%")
                  ->orWhere('prenom', 'LIKE', "%{$query}%")
                  ->orWhere('email', 'LIKE', "%{$query}%");
            })
            ->select('id', 'nom', 'prenoms', 'prenom', 'email', 'fonction', 'departement')
            ->limit(10)
            ->get()
            ->map(fn ($p) => [
                'id' => $p->id,
                'name' => trim($p->prenoms . ' ' . $p->nom) ?: trim($p->prenom . ' ' . $p->nom),
                'nom' => $p->nom,
                'email' => $p->email,
                'role' => $p->fonction ?? 'RH',
                'type' => 'personnel',
            ]);

        // Also search in Users table (if they have roles)
        $users = User::query()
            ->where(function ($q) use ($query) {
                $q->where('name', 'LIKE', "%{$query}%")
                  ->orWhere('nom', 'LIKE', "%{$query}%")
                  ->orWhere('email', 'LIKE', "%{$query}%");
            })
            ->select('id', 'name', 'nom', 'email')
            ->limit(5)
            ->get()
            ->map(fn ($u) => [
                'id' => $u->id,
                'name' => $u->name ?? $u->nom,
                'nom' => $u->nom ?? $u->name,
                'email' => $u->email,
                'role' => 'Utilisateur',
                'type' => 'user',
            ]);

        // Merge and deduplicate by ID
        $results = collect($personnel)->merge($users)
            ->unique('id')
            ->values()
            ->toArray();

        return response()->json($results);
    }

    /**
     * Get single employee details
     * GET /api/employees/{id}
     */
    public function show($id)
    {
        // Try Personnel first
        $personnel = Personnel::find($id);
        if ($personnel) {
            return response()->json([
                'id' => $personnel->id,
                'name' => trim($personnel->prenoms . ' ' . $personnel->nom) ?: trim($personnel->prenom . ' ' . $personnel->nom),
                'email' => $personnel->email,
                'role' => $personnel->fonction ?? 'RH',
                'type' => 'personnel',
            ]);
        }

        // Try User
        $user = User::find($id);
        if ($user) {
            return response()->json([
                'id' => $user->id,
                'name' => $user->name ?? $user->nom,
                'email' => $user->email,
                'role' => 'Utilisateur',
                'type' => 'user',
            ]);
        }

        return response()->json(['error' => 'Employee not found'], 404);
    }
}

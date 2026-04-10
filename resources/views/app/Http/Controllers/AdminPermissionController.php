<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class AdminPermissionController extends Controller
{
    /**
     * Afficher la page de gestion des permissions admin
     */
    public function index()
    {
        $this->authorize('manage-admin-permissions');
        
        $config = config('admin_modules');
        $allModules = $this->getAllAvailableModules();
        
        return view('admin.permissions.index', compact('config', 'allModules'));
    }
    
    /**
     * Mettre à jour les permissions admin
     */
    public function update(Request $request)
    {
        $this->authorize('manage-admin-permissions');
        
        $validated = $request->validate([
            'admin_has_full_access' => 'required|boolean',
            'restricted_modules' => 'nullable|array',
            'restricted_modules.*' => 'string',
        ]);
        
        $configPath = config_path('admin_modules.php');
        
        $configContent = "<?php\n\nreturn [\n";
        $configContent .= "    /*\n    |\n    | Modules restreints pour l'admin\n    |\n    */\n";
        $configContent .= "    'restricted_modules' => [\n";
        
        if (!empty($validated['restricted_modules'])) {
            foreach ($validated['restricted_modules'] as $module) {
                $configContent .= "        '{$module}',\n";
            }
        }
        
        $configContent .= "    ],\n\n";
        $configContent .= "    /*\n    |\n    | Permissions de l'admin\n    |\n    */\n";
        $configContent .= "    'admin_has_full_access' => " . ($validated['admin_has_full_access'] ? 'true' : 'false') . ",\n";
        $configContent .= "];\n";
        
        File::put($configPath, $configContent);
        
        // Vider le cache de configuration
        app()->make('config')->set('admin_modules', include $configPath);
        
        return redirect()->route('admin.permissions.index')
            ->with('success', 'Permissions mises à jour avec succès');
    }
    
    /**
     * Obtenir la liste de tous les modules disponibles
     */
    private function getAllAvailableModules()
    {
        $modules = config('modules.modules', []);
        $allModules = [];
        
        foreach ($modules as $role => $roleModules) {
            if (is_array($roleModules)) {
                foreach ($roleModules as $module) {
                    if ($module !== '*' && !in_array($module, $allModules)) {
                        $allModules[] = $module;
                    }
                }
            }
        }
        
        sort($allModules);
        return $allModules;
    }
}

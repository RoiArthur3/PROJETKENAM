<?php

echo "=== Liste des employés dans la base de données ===\n\n";

try {
    $employees = \App\Models\Personnel::take(10)->get(['nom', 'prenom', 'matricule', 'code_badge']);
    
    if ($employees->isEmpty()) {
        echo "❌ Aucun employé trouvé dans la base de données\n";
        echo "   Vous devez d'abord ajouter des employés dans le système\n";
    } else {
        echo "✅ Employés trouvés:\n";
        foreach ($employees as $employee) {
            echo "- {$employee->nom} {$employee->prenom}";
            echo " (Matricule: " . ($employee->matricule ?? 'N/A') . ")";
            echo " (Badge: " . ($employee->code_badge ?? 'N/A') . ")\n";
        }
    }
    
    echo "\n=== Recommandations ===\n";
    echo "1. Utilisez ces noms réels dans vos tests\n";
    echo "2. Ajoutez des photos pour chaque employé\n";
    echo "3. Synchronisez avec le terminal Hikvision\n";
    
} catch (Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
}

echo "\n";

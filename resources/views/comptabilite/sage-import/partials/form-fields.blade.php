{{-- Partial: champs communs à tous les formulaires d'import --}}

<div class="mb-3">
    <label class="form-label fw-semibold">Fichier CSV exporté depuis SAGE <span class="text-danger">*</span></label>
    <input type="file" name="fichier_csv" class="form-control" accept=".csv,.txt" required>
    <div class="form-text">Format accepté: .csv ou .txt — Taille max: 10 Mo</div>
</div>

<div class="row g-3 mb-3">
    <div class="col-md-6">
        <label class="form-label fw-semibold">Séparateur de colonnes</label>
        <select name="separateur" class="form-select">
            <option value="semicolon" selected>Point-virgule ; (SAGE par défaut)</option>
            <option value="comma">Virgule ,</option>
            <option value="pipe">Pipe |</option>
            <option value="tab">Tabulation</option>
        </select>
    </div>
    <div class="col-md-6">
        <label class="form-label fw-semibold">Mode d'import</label>
        <select name="mode_import" class="form-select">
            <option value="update" selected>Ajouter + Mettre à jour si existant</option>
            <option value="ignore">Ajouter seulement (ignorer les doublons)</option>
        </select>
        <div class="form-text">L'option "Mettre à jour" modifie les données existantes. "Ignorer" ne touche jamais l'existant.</div>
    </div>
</div>

@extends('layouts.app')

@section('title', 'Nouvelle Entrée/Sortie - KENAM SERVICES')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <h1 class="h3 mb-0 text-gray-800">Enregistrer une Entrée ou Sortie</h1>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header py-3 bg-gradient-primary text-white">
                    <h6 class="m-0 font-weight-bold">Détails du Mouvement</h6>
                </div>
                <div class="card-body">
                    <form id="warehouseForm">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="type_mouvement" class="form-label">Type de Mouvement *</label>
                                <select class="form-select" id="type_mouvement" required onchange="toggleFields()">
                                    <option value="">Sélectionner le type</option>
                                    <option value="entree">Entrée</option>
                                    <option value="sortie">Sortie</option>
                                </select>
                                <div class="form-text">Choisissez si c'est une entrée ou une sortie de stock.</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="article" class="form-label">Article *</label>
                                <select class="form-select" id="article" required>
                                    <option value="">Sélectionner un article</option>
                                    <option value="1">Huile Moteur 10W40</option>
                                    <option value="2">Papier A4</option>
                                    <option value="3">Toner Imprimante</option>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="quantite" class="form-label">Quantité *</label>
                                <input type="number" class="form-control" id="quantite" required min="1" placeholder="Ex: 10">
                                <div class="form-text">Quantité à entrer ou sortir.</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="date_mouvement" class="form-label">Date du Mouvement *</label>
                                <input type="date" class="form-control" id="date_mouvement" value="{{ date('Y-m-d') }}" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="raison" class="form-label">Raison/Motif *</label>
                            <textarea class="form-control" id="raison" rows="2" required placeholder="Ex: Réapprovisionnement, Utilisation pour opération..."></textarea>
                        </div>

                        <div class="row" id="entreeFields" style="display: none;">
                            <div class="col-md-6 mb-3">
                                <label for="fournisseur" class="form-label">Fournisseur</label>
                                <select class="form-select" id="fournisseur">
                                    <option value="">Sélectionner un fournisseur</option>
                                    <option value="1">Total Energies</option>
                                    <option value="2">Office Depot</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="prix_unitaire" class="form-label">Prix Unitaire (FCFA)</label>
                                <input type="number" class="form-control" id="prix_unitaire" step="0.01" placeholder="Ex: 1500">
                                <div class="form-text">Prix d'achat par unité.</div>
                            </div>
                        </div>

                        <div class="row" id="sortieFields" style="display: none;">
                            <div class="col-md-6 mb-3">
                                <label for="destination" class="form-label">Destination/Utilisation</label>
                                <input type="text" class="form-control" id="destination" placeholder="Ex: Opération OP-2025-001">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="responsable" class="form-label">Responsable</label>
                                <input type="text" class="form-control" id="responsable" placeholder="Ex: Jean Dupont">
                            </div>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="{{ route('warehouse.index') }}" class="btn btn-secondary me-md-2">Annuler</a>
                            <button type="submit" class="btn btn-primary" id="submitBtn">
                                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true" id="spinner" style="display:none;"></span>
                                Enregistrer le Mouvement
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Toggle fields based on movement type
function toggleFields() {
    const type = document.getElementById('type_mouvement').value;
    document.getElementById('entreeFields').style.display = type === 'entree' ? 'block' : 'none';
    document.getElementById('sortieFields').style.display = type === 'sortie' ? 'block' : 'none';
}

// Validation et soumission
document.getElementById('warehouseForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const type = document.getElementById('type_mouvement').value;
    const article = document.getElementById('article').value;
    const quantite = document.getElementById('quantite').value;
    const raison = document.getElementById('raison').value;

    if (!type || !article || !quantite || !raison) {
        alert('Veuillez remplir tous les champs obligatoires.');
        return;
    }

    // Simulation de soumission
    document.getElementById('spinner').style.display = 'inline-block';
    document.getElementById('submitBtn').disabled = true;

    setTimeout(() => {
        alert('Mouvement enregistré avec succès !');
        window.location.href = '{{ route("warehouse.index") }}';
    }, 2000);
});
</script>
@endsection

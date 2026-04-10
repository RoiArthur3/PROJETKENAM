@extends('layouts.app')

@section('title', 'Ajouter un Client - KENAM SERVICES')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <h1 class="h3 mb-0 text-gray-800">Ajouter un Nouveau Client</h1>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header py-3 bg-gradient-primary text-white">
                    <h6 class="m-0 font-weight-bold">Informations du Client</h6>
                </div>
                <div class="card-body">
                    <form id="clientForm">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="nom" class="form-label">Nom du Client *</label>
                                <input type="text" class="form-control" id="nom" required placeholder="Ex: Jean Dupont">
                                <div class="form-text">Entrez le nom complet du client.</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="type" class="form-label">Type *</label>
                                <select class="form-select" id="type" required>
                                    <option value="">Sélectionner le type</option>
                                    <option value="Particulier">Particulier</option>
                                    <option value="Entreprise">Entreprise</option>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" placeholder="Ex: jean.dupont@email.com">
                                <div class="form-text">Adresse email pour les factures.</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="telephone" class="form-label">Téléphone</label>
                                <input type="tel" class="form-control" id="telephone" placeholder="Ex: +225 01 02 03 04 05">
                                <div class="form-text">Numéro de téléphone principal.</div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="entreprise" class="form-label">Entreprise (si applicable)</label>
                            <input type="text" class="form-control" id="entreprise" placeholder="Ex: ABC Logistics">
                        </div>

                        <div class="mb-3">
                            <label for="adresse" class="form-label">Adresse</label>
                            <textarea class="form-control" id="adresse" rows="2" placeholder="Adresse complète du client..."></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="credit_limit" class="form-label">Limite de Crédit (FCFA)</label>
                                <input type="number" class="form-control" id="credit_limit" value="0" min="0" step="0.01">
                                <div class="form-text">Limite de crédit autorisée pour ce client.</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="actif" class="form-label">Statut</label>
                                <select class="form-select" id="actif">
                                    <option value="1">Actif</option>
                                    <option value="0">Inactif</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" rows="3" placeholder="Informations supplémentaires sur le client..."></textarea>
                        </div>

                        <!-- Bouton avec Spinner -->
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="{{ route('clients.index') }}" class="btn btn-secondary me-md-2">Annuler</a>
                            <button type="submit" class="btn btn-primary" id="submitBtn">
                                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true" id="spinner" style="display:none;"></span>
                                Ajouter le Client
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Validation et soumission
document.getElementById('clientForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const nom = document.getElementById('nom').value;
    const type = document.getElementById('type').value;

    if (!nom || !type) {
        alert('Veuillez remplir tous les champs obligatoires.');
        return;
    }

    // Simulation de soumission
    document.getElementById('spinner').style.display = 'inline-block';
    document.getElementById('submitBtn').disabled = true;

    setTimeout(() => {
        alert('Client ajouté avec succès !');
        window.location.href = '{{ route("clients.index") }}';
    }, 2000);
});
</script>
@endsection

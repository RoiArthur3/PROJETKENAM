@extends('layouts.app')

@section('title', 'Nouvelle Recette - KENAM SERVICES')

@section('content')
<div class="content-wrapper">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Nouvelle Recette</h1>
        <div class="d-flex gap-2">
            <a href="{{ route('comptabilite.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-chart-line me-2"></i>Comptabilité
            </a>
            <a href="{{ route('comptabilite.depenses') }}" class="btn btn-outline-danger">
                <i class="fas fa-arrow-down me-2"></i>Dépenses
            </a>
            <a href="{{ route('comptabilite.recettes') }}" class="btn btn-outline-success">
                <i class="fas fa-arrow-up me-2"></i>Recettes
            </a>
            <a href="{{ route('comptabilite.bilan') }}" class="btn btn-outline-primary">
                <i class="fas fa-balance-scale me-2"></i>Bilan
            </a>
        </div>
    </div>

    <!-- Formulaire de création -->
    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('comptabilite.recettes.store') }}">
                @csrf
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="reference" class="form-label">Référence</label>
                            <input type="text" class="form-control" id="reference" name="reference" placeholder="Ex: REC-2026-001" required>
                        </div>
                        <div class="mb-3">
                            <label for="date" class="form-label">Date</label>
                            <input type="date" class="form-control" id="date" name="date" value="{{ now()->format('Y-m-d') }}" required>
                        </div>
                        <div class="mb-3">
                            <label for="montant" class="form-label">Montant (FCFA)</label>
                            <input type="number" class="form-control" id="montant" name="montant" placeholder="0" min="0" required>
                        </div>
                        <div class="mb-3">
                            <label for="categorie" class="form-label">Catégorie</label>
                            <select class="form-select" id="categorie" name="categorie" required>
                                <option value="">Sélectionner une catégorie</option>
                                <option value="Ventes">Ventes</option>
                                <option value="Services">Services</option>
                                <option value="Formations">Formations</option>
                                <option value="Consulting">Consulting</option>
                                <option value="Autres">Autres</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="client" class="form-label">Client</label>
                            <input type="text" class="form-control" id="client" name="client" placeholder="Nom du client" required>
                        </div>
                        <div class="mb-3">
                            <label for="statut" class="form-label">Statut</label>
                            <select class="form-select" id="statut" name="statut" required>
                                <option value="">Sélectionner un statut</option>
                                <option value="en_attente">En attente</option>
                                <option value="encaissée">Encaissée</option>
                                <option value="annulée">Annulée</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="mode_paiement" class="form-label">Mode de Paiement</label>
                            <select class="form-select" id="mode_paiement" name="mode_paiement">
                                <option value="">Sélectionner un mode</option>
                                <option value="espece">Espèces</option>
                                <option value="virement">Virement bancaire</option>
                                <option value="carte">Carte bancaire</option>
                                <option value=" cheque">Chèque</option>
                                <option value="mobile_money">Mobile Money</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="reference_paiement" class="form-label">Référence Paiement</label>
                            <input type="text" class="form-control" id="reference_paiement" name="reference_paiement" placeholder="Numéro de transaction">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="3" placeholder="Description de la recette..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="facture" class="form-label">Numéro Facture</label>
                            <input type="text" class="form-control" id="facture" name="facture" placeholder="Numéro de facture">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="date_echeance" class="form-label">Date d'Échéance</label>
                            <input type="date" class="form-control" id="date_echeance" name="date_echeance">
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center">
                    <a href="{{ route('comptabilite.recettes') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Annuler
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Enregistrer la Recette
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Informations additionnelles -->
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card bg-light">
                <div class="card-body">
                    <h5 class="card-title mb-3">
                        <i class="fas fa-info-circle me-2"></i>Informations
                    </h5>
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-muted">Champs obligatoires</h6>
                            <ul class="mb-0">
                                <li>Référence unique pour identifier la recette</li>
                                <li>Montant et date pour le suivi comptable</li>
                                <li>Catégorie pour les rapports</li>
                                <li>Client pour la traçabilité</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted">Conseils</h6>
                            <ul class="mb-0">
                                <li>Utilisez des références cohérentes (REC-AAAA-MM-JJ)</li>
                                <li>Vérifiez les informations avant de valider</li>
                                <li>Joignez les factures si disponible</li>
                                <li>Le statut "Encaissée" impacte la trésorerie</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistiques Rapides -->
    <div class="row mt-4">
        <div class="col-md-4">
            <div class="card bg-success bg-opacity-10">
                <div class="card-body">
                    <h6 class="text-success">Total Recettes du Mois</h6>
                    <h4 class="mb-0">12,500,000 FCFA</h4>
                    <small>En progression de +15%</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-primary bg-opacity-10">
                <div class="card-body">
                    <h6 class="text-primary">Recettes En Attente</h6>
                    <h4 class="mb-0">2,300,000 FCFA</h4>
                    <small>À encaisser</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card bg-warning bg-opacity-10">
                <div class="card-body">
                    <h6 class="text-warning">Moyenne par Recette</h6>
                    <h4 class="mb-0">416,667 FCFA</h4>
                    <small>Basé sur 30 recettes</small>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-génération de référence
    const referenceInput = document.getElementById('reference');
    const dateInput = document.getElementById('date');

    if (referenceInput && dateInput) {
        dateInput.addEventListener('change', function() {
            const date = new Date(this.value);
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const day = String(date.getDate()).padStart(2, '0');

            // Compter le nombre de recettes existantes pour le jour (simulation)
            const count = Math.floor(Math.random() * 10) + 1;
            referenceInput.value = `REC-${year}-${month}-${day}-${String(count).padStart(3, '0')}`;
        });
    }

    // Validation du formulaire
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function(e) {
            const montant = document.getElementById('montant').value;
            const statut = document.getElementById('statut').value;

            if (montant <= 0) {
                e.preventDefault();
                alert('Le montant doit être supérieur à 0');
                return;
            }

            if (statut === 'encaissée') {
                const modePaiement = document.getElementById('mode_paiement').value;
                if (!modePaiement) {
                    e.preventDefault();
                    alert('Le mode de paiement est requis pour une recette encaissée');
                    return;
                }
            }
        });
    }
});
</script>
@endsection

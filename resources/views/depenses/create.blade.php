@extends('layouts.app')

@section('title', 'Créer une Dépense - KENAM SERVICES')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <h1 class="h3 mb-0 text-gray-800">Créer une Nouvelle Dépense</h1>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header py-3 bg-gradient-primary text-white">
                    <h6 class="m-0 font-weight-bold">Détails de la Dépense</h6>
                </div>
                <div class="card-body">
                    <form id="expenseForm">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="reference" class="form-label">Référence *</label>
                                <input type="text" class="form-control" id="reference" required placeholder="Ex: EXP-003">
                                <div class="form-text">Référence unique automatique.</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="categorie" class="form-label">Catégorie *</label>
                                <select class="form-select" id="categorie" required>
                                    <option value="">Sélectionner une catégorie</option>
                                    <option value="Carburant">Carburant</option>
                                    <option value="Maintenance">Maintenance</option>
                                    <option value="Achat">Achat</option>
                                    <option value="Prime">Prime</option>
                                    <option value="Fournitures">Fournitures</option>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="montant" class="form-label">Montant *</label>
                                <input type="number" class="form-control" id="montant" required placeholder="Ex: 75000" min="0" step="0.01">
                                <div class="form-text">Montant en FCFA.</div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="date_depense" class="form-label">Date de Dépense *</label>
                                <input type="date" class="form-control" id="date_depense" value="{{ date('Y-m-d') }}" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="service_concerne" class="form-label">Service Concerné *</label>
                                <select class="form-select" id="service_concerne" required>
                                    <option value="">Sélectionner un service</option>
                                    <option value="Logistique">Logistique</option>
                                    <option value="RH">RH</option>
                                    <option value="Maintenance">Maintenance</option>
                                    <option value="Commercial">Commercial</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="mode_paiement" class="form-label">Mode de Paiement</label>
                                <select class="form-select" id="mode_paiement">
                                    <option value="especes">Espèces</option>
                                    <option value="cheque">Chèque</option>
                                    <option value="virement">Virement</option>
                                    <option value="mobile_money">Mobile Money</option>
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="operation_id" class="form-label">Opération Liée</label>
                                <select class="form-select" id="operation_id">
                                    <option value="">Sélectionner une opération</option>
                                    <option value="1">Transport Abidjan–Bouaké</option>
                                    <option value="2">Livraison Dakar</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="fournisseur" class="form-label">Fournisseur</label>
                                <input type="text" class="form-control" id="fournisseur" placeholder="Ex: Total Energies">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description *</label>
                            <textarea class="form-control" id="description" rows="3" required placeholder="Détaillez la dépense..."></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="budget_prevu" class="form-label">Budget Prévu (FCFA)</label>
                            <input type="number" class="form-control" id="budget_prevu" placeholder="Ex: 80000" min="0" step="0.01">
                            <div class="form-text">Budget alloué pour cette catégorie.</div>
                        </div>

                        <div class="mb-3">
                            <label for="justificatif" class="form-label">Pièce Justificative</label>
                            <input type="file" class="form-control" id="justificatif" accept=".jpg,.jpeg,.png,.pdf">
                            <div class="form-text">Téléversez la facture ou le reçu.</div>
                        </div>

                        <!-- Bouton avec Spinner -->
                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                            <a href="{{ route('depenses.index') }}" class="btn btn-secondary me-md-2">Annuler</a>
                            <button type="submit" class="btn btn-primary" id="submitBtn">
                                <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true" id="spinner" style="display:none;"></span>
                                Enregistrer la Dépense
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
document.getElementById('expenseForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const reference = document.getElementById('reference').value;
    const categorie = document.getElementById('categorie').value;
    const montant = document.getElementById('montant').value;
    const description = document.getElementById('description').value;

    if (!reference || !categorie || !montant || !description) {
        alert('Veuillez remplir tous les champs obligatoires.');
        return;
    }

    // Simulation de soumission
    document.getElementById('spinner').style.display = 'inline-block';
    document.getElementById('submitBtn').disabled = true;

    setTimeout(() => {
        alert('Dépense créée avec succès et soumise à validation !');
        window.location.href = '{{ route("depenses.index") }}';
    }, 2000);
});
</script>
@endsection

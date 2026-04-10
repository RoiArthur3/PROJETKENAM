@extends('layouts.app')

@section('title', 'Nouveau Décaissement - KENAM SERVICES')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-wallet me-2 text-primary"></i>Nouveau Décaissement
            </h1>
            <p class="text-muted mb-0">{{ now()->locale('fr')->isoFormat('dddd D MMMM YYYY') }}</p>
        </div>
        <div>
            <a href="{{ route('tresorerie.decaissements') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i>Retour
            </a>
        </div>
    </div>

    @if(session('error'))
    <div class="alert alert-danger border-0 shadow-sm" role="alert">
        <i class="fas fa-exclamation-triangle me-2"></i>{{ session('error') }}
    </div>
    @endif

    @if($errors->any())
    <div class="alert alert-danger border-0 shadow-sm" role="alert">
        <div class="fw-bold mb-1"><i class="fas fa-times-circle me-2"></i>Veuillez corriger les erreurs suivantes :</div>
        <ul class="mb-0 ps-3">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <!-- Bande d'explication du workflow -->
    <div class="alert alert-warning border-0 shadow-sm mb-4" role="alert">
        <div class="d-flex align-items-start gap-3">
            <div class="pt-1">
                <i class="fas fa-sitemap fa-lg"></i>
            </div>
            <div class="w-100">
                <h6 class="mb-2 fw-bold">Workflow de création du décaissement</h6>
                <div class="d-flex flex-wrap align-items-center gap-2">
                    <span class="badge bg-warning text-dark">1. Sélectionner la source</span>
                    <i class="fas fa-arrow-right text-muted"></i>
                    <span class="badge bg-primary">2. Lier à opération/facture</span>
                    <i class="fas fa-arrow-right text-muted"></i>
                    <span class="badge bg-info text-dark">3. Saisir montant et bénéficiaire</span>
                    <i class="fas fa-arrow-right text-muted"></i>
                    <span class="badge bg-success">4. Confirmer</span>
                </div>
                <p class="mb-0 mt-2 small text-muted">
                    Astuce: sélectionnez une opération ou facture pour remplir automatiquement les champs.
                </p>
            </div>
        </div>
    </div>

    <form method="POST" action="{{ route('tresorerie.decaissements.store') }}" id="decaissementForm">
        @csrf

        <!-- Sélection de la source (Caisse) -->
        <div class="card shadow mb-4 border-0">
            <div class="card-header py-3 bg-warning text-dark">
                <h6 class="m-0 font-weight-bold">
                    <i class="fas fa-piggy-bank me-2"></i>Sélection de la Source
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="caisse_id" class="form-label fw-bold">
                            <i class="fas fa-safe me-1"></i>Caisse Source *
                        </label>
                        <select class="form-select form-select-lg" id="caisse_id" name="caisse_id" required>
                            <option value="">Choisir une caisse</option>
                            @foreach($caisses as $caisse)
                                <option value="{{ $caisse->id }}">{{ $caisse->nom }} (Solde: {{ number_format($caisse->solde_actuel, 0, ',', ' ') }} FCFA)</option>
                            @endforeach
                        </select>
                        <div class="form-text">La source de fonds pour ce décaissement</div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="date_decaissement" class="form-label fw-bold">
                            <i class="fas fa-calendar me-1"></i>Date de Décaissement *
                        </label>
                        <input type="date" class="form-control form-control-lg" id="date_decaissement" name="date_decaissement" required value="{{ now()->format('Y-m-d') }}">
                        <div class="form-text">Date effective du décaissement</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Liaison avec opération ou facture -->
        <div class="card shadow mb-4 border-0">
            <div class="card-header py-3 bg-primary text-white">
                <h6 class="m-0 font-weight-bold">
                    <i class="fas fa-link me-2"></i>Lier à une Source Documentaire
                </h6>
            </div>
            <div class="card-body">
                <div class="alert alert-info border-0 mb-3">
                    <i class="fas fa-info-circle me-2"></i>
                    Sélectionnez une opération ou facture pour remplir automatiquement les champs ci-dessous.
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="operation_id" class="form-label fw-bold">
                            <i class="fas fa-file-alt me-1"></i>Opération Approuvée (Optionnel)
                        </label>
                        <select class="form-select" id="operation_id" name="operation_id">
                            <option value="">-- Aucune opération --</option>
                            @foreach($operations as $op)
                                <option value="{{ $op->id }}"
                                        data-montant="{{ $op->montant }}"
                                        data-titre="{{ $op->titre }}"
                                        data-beneficiaire="{{ $op->demandeur_name }}"
                                        data-motif="{{ $op->typeOperation?->nom ?? 'Opération' }}"
                                        data-ref="{{ $op->numero_ordre ?? '#OP-'.$op->id }}">
                                    [{{ $op->numero_ordre ?? '#OP-'.$op->id }}] {{ $op->titre }} - {{ number_format($op->montant, 0, ',', ' ') }} FCFA
                                </option>
                            @endforeach
                        </select>
                        <div class="form-text">Sélectionnez une opération validée à payer</div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="facture_id" class="form-label fw-bold">
                            <i class="fas fa-file-invoice-dollar me-1"></i>Facture à Payer (Optionnel)
                        </label>
                        <select class="form-select" id="facture_id" name="facture_id">
                            <option value="">-- Aucune facture --</option>
                            @foreach($factures as $facture)
                                <option value="{{ $facture->id }}"
                                        data-montant="{{ $facture->montant_restant }}"
                                        data-titre="{{ $facture->numero_facture }}"
                                        data-beneficiaire="{{ $facture->client_nom }}"
                                        data-motif="Paiement facture {{ $facture->numero_facture }}"
                                        data-ref="{{ $facture->numero_facture }}">
                                    [{{ $facture->numero_facture }}] {{ $facture->client_nom }} - {{ number_format($facture->montant_restant, 0, ',', ' ') }} FCFA
                                </option>
                            @endforeach
                        </select>
                        <div class="form-text">Sélectionnez une facture pour paiement partiel ou total</div>
                    </div>
                </div>

                <!-- Boîte d'information pour la source sélectionnée -->
                <div id="sourceInfo" class="alert alert-success d-none border-0 mt-3">
                    <div class="row">
                        <div class="col-md-4">
                            <small><strong><i class="fas fa-bookmark me-1"></i>Référence:</strong></small>
                            <div id="infoRef" class="fw-bold text-primary">-</div>
                        </div>
                        <div class="col-md-4">
                            <small><strong><i class="fas fa-paragraph me-1"></i>Titre:</strong></small>
                            <div id="infoTitre">-</div>
                        </div>
                        <div class="col-md-4">
                            <small><strong><i class="fas fa-money-bill-wave me-1"></i>Montant:</strong></small>
                            <div id="infoMontant" class="text-success fw-bold">-</div>
                        </div>
                    </div>
                    <div class="row mt-2">
                        <div class="col-md-6">
                            <small><strong><i class="fas fa-user me-1"></i>Bénéficiaire:</strong></small>
                            <div id="infoBeneficiaire">-</div>
                        </div>
                        <div class="col-md-6">
                            <small><strong><i class="fas fa-tag me-1"></i>Motif:</strong></small>
                            <div id="infoMotif">-</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Montant et détails du bénéficiaire -->
        <div class="card shadow mb-4 border-0">
            <div class="card-header py-3 bg-info text-white">
                <h6 class="m-0 font-weight-bold">
                    <i class="fas fa-users-cog me-2"></i>Montant et Bénéficiaire
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="montant" class="form-label fw-bold">
                            <i class="fas fa-money-bill-wave me-1"></i>Montant (FCFA) *
                        </label>
                        <input type="number" class="form-control form-control-lg" id="montant" name="montant" required placeholder="0" step="1">
                        <div class="form-text"><small>Utilisez un montant négatif pour corriger un décaissement erroné.</small></div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="beneficiaire" class="form-label fw-bold">
                            <i class="fas fa-user me-1"></i>Bénéficiaire *
                        </label>
                        <input type="text" class="form-control form-control-lg" id="beneficiaire" name="beneficiaire" required placeholder="Nom du bénéficiaire">
                        <div class="form-text">Personne ou entité recevant le paiement</div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="fournisseur_id" class="form-label fw-bold">
                            <i class="fas fa-building me-1"></i>Fournisseur (Optionnel)
                        </label>
                        <select class="form-select" id="fournisseur_id" name="fournisseur_id">
                            <option value="">-- Aucun fournisseur --</option>
                            @foreach($fournisseurs as $fournisseur)
                                <option value="{{ $fournisseur->id }}">
                                    {{ $fournisseur->nom ?? $fournisseur->raison_sociale ?? $fournisseur->name }}
                                </option>
                            @endforeach
                        </select>
                        <div class="form-text">Lier ce décaissement à un fournisseur</div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="libelle" class="form-label fw-bold">
                            <i class="fas fa-heading me-1"></i>Libellé / Objet *
                        </label>
                        <input type="text" class="form-control" id="libelle" name="libelle" required placeholder="Description du décaissement">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="motif" class="form-label fw-bold">
                            <i class="fas fa-comment me-1"></i>Motif / Catégorie
                        </label>
                        <input type="text" class="form-control" id="motif" name="motif" placeholder="Ex: Achat fournitures, Carburant, etc.">
                    </div>
                </div>
            </div>
        </div>

        <!-- Informations complémentaires -->
        <div class="card shadow mb-4 border-0">
            <div class="card-header py-3 bg-secondary">
                <h6 class="m-0 font-weight-bold text-white">
                    <i class="fas fa-clipboard-list me-2"></i>Informations Complémentaires
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="responsable" class="form-label fw-bold">
                            <i class="fas fa-id-card me-1"></i>Responsable (Caissier)
                        </label>
                        <input type="text" class="form-control" id="responsable" name="responsable" required value="{{ Auth::user()->name }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="justification" class="form-label fw-bold">
                            <i class="fas fa-file-archive me-1"></i>Justification (N° Pièce)
                        </label>
                        <input type="text" class="form-control" id="justification" name="justification" placeholder="Numéro de facture ou ticket">
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 mb-3">
                        <label for="notes" class="form-label fw-bold">
                            <i class="fas fa-sticky-note me-1"></i>Notes et Observations
                        </label>
                        <textarea class="form-control" id="notes" name="notes" rows="3" placeholder="Notes additionnelles pour ce décaissement"></textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- Statut automatique -->
        <div class="alert alert-success border-0 mb-4">
            <i class="fas fa-check-circle me-2"></i>
            <strong>Statut du décaissement :</strong> Validé / Décaissé (Automatique)
            <p class="small mb-0 mt-2">Les décaissements sont immédiatement validés et le solde de la caisse est automatiquement débité.</p>
            <input type="hidden" name="statut" value="validé">
        </div>

        <!-- Boutons d'action -->
        <div class="row mt-3">
            <div class="col-12">
                <div class="d-flex justify-content-between gap-2">
                    <a href="{{ route('tresorerie.decaissements') }}" class="btn btn-outline-secondary btn-lg">
                        <i class="fas fa-times me-1"></i>Annuler
                    </a>
                    <button type="submit" class="btn btn-success btn-lg px-5">
                        <i class="fas fa-check-circle me-1"></i>Confirmer le Décaissement
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const operationSelect = document.getElementById('operation_id');
    const factureSelect = document.getElementById('facture_id');
    const montantInput = document.getElementById('montant');
    const beneficiaireInput = document.getElementById('beneficiaire');
    const libelleInput = document.getElementById('libelle');
    const motifInput = document.getElementById('motif');
    const sourceInfoBox = document.getElementById('sourceInfo');

    // Fonction pour afficher les informations de la source sélectionnée
    function updateSourceInfo() {
        const selectedOption = operationSelect.value 
            ? operationSelect.options[operationSelect.selectedIndex]
            : factureSelect.options[factureSelect.selectedIndex];

        if (!selectedOption || selectedOption.value === '') {
            sourceInfoBox.classList.add('d-none');
            return;
        }

        const montant = selectedOption.dataset.montant;
        const titre = selectedOption.dataset.titre;
        const beneficiaire = selectedOption.dataset.beneficiaire;
        const motif = selectedOption.dataset.motif;
        const ref = selectedOption.dataset.ref;

        document.getElementById('infoRef').textContent = ref;
        document.getElementById('infoTitre').textContent = titre;
        document.getElementById('infoMontant').textContent = new Intl.NumberFormat('fr-FR').format(montant) + ' FCFA';
        document.getElementById('infoBeneficiaire').textContent = beneficiaire;
        document.getElementById('infoMotif').textContent = motif;

        sourceInfoBox.classList.remove('d-none');

        // Remplir automatiquement les champs
        if (!montantInput.value) montantInput.value = montant;
        if (!beneficiaireInput.value) beneficiaireInput.value = beneficiaire;
        if (!libelleInput.value) libelleInput.value = titre;
        if (!motifInput.value) motifInput.value = motif;
    }

    // Événements de changement
    operationSelect.addEventListener('change', function() {
        if (this.value) {
            factureSelect.value = ''; // Déselectionner la facture
        }
        updateSourceInfo();
    });

    factureSelect.addEventListener('change', function() {
        if (this.value) {
            operationSelect.value = ''; // Déselectionner l'opération
        }
        updateSourceInfo();
    });
});
</script>
@endpush
@endsection

@extends('layouts.app')

@section('title', 'Nouvel Encaissement')

@section('content')
<div class="container-fluid">
    <!-- En-tête -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="h3 mb-0">
                        <i class="fas fa-plus-circle text-success me-2"></i>
                        Nouvel Encaissement
                    </h2>
                    <p class="text-muted mb-0">Enregistrer une nouvelle entrée de fonds</p>
                </div>
                <div>
                    <a href="{{ route('tresorerie.encaissements') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-1"></i>
                        Retour à la liste
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Formulaire -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('tresorerie.encaissements.store') }}" enctype="multipart/form-data">
                        @csrf
                        
                        <!-- ORIGINE DE L'ENCAISSEMENT -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="card-title text-primary border-bottom pb-2 mb-3">
                                    <i class="fas fa-link me-2"></i>1. Origine des fonds
                                </h5>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Type d'origine *</label>
                                <select class="form-select form-select-lg border-primary" name="type_encaissement" id="type_encaissement" required>
                                    <option value="">-- Choisir l'origine --</option>
                                    <option value="facture">Règlement de Facture</option>
                                    <option value="operation">Liaison à une Opération</option>
                                    <option value="projet">Liaison à un Projet</option>
                                    <option value="avance">Acompte client (valeur conservee pour compatibilite BDD)</option>
                                    <option value="remboursement">Remboursement Fournisseur</option>
                                    <option value="autre">Autres Recettes / Divers</option>
                                </select>
                            </div>

                            <!-- Sélecteur dynamique selon le type -->
                            <div class="col-md-6 mb-3 dynamic-field" id="field_facture" style="display:none;">
                                <label class="form-label fw-bold text-success">Choisir la Facture *</label>
                                <select class="form-select border-success" name="invoice_id" id="invoice_id">
                                    <option value="">-- Sélectionner une facture --</option>
                                    @foreach($invoices as $inv)
                                        <option value="{{ $inv->id }}" 
                                                data-montant="{{ $inv->net_amount }}" 
                                                data-reste="{{ $inv->remaining_amount }}"
                                                data-client="{{ $inv->client->name ?? '' }}"
                                                data-description="Paiement facture {{ $inv->invoice_number }}">
                                            {{ $inv->invoice_number }} - {{ $inv->client->name ?? 'Client' }} (Reste: {{ number_format($inv->remaining_amount, 0, ',', ' ') }} FCFA)
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6 mb-3 dynamic-field" id="field_operation" style="display:none;">
                                <label class="form-label fw-bold text-info">Choisir l'Opération *</label>
                                <select class="form-select border-info" name="operation_id" id="operation_id">
                                    <option value="">-- Sélectionner une opération --</option>
                                    @foreach($operations as $op)
                                        <option value="{{ $op->id }}" 
                                                data-montant="{{ $op->montant }}"
                                                data-client="{{ $op->demandeur_name }}"
                                                data-description="Encaissement lié à l'opération {{ $op->titre }}">
                                            [{{ $op->numero_ordre ?? '#OP-'.$op->id }}] {{ $op->titre }} - {{ number_format($op->montant, 0, ',', ' ') }} FCFA
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6 mb-3 dynamic-field" id="field_projet" style="display:none;">
                                <label class="form-label fw-bold text-warning">Choisir le Projet *</label>
                                <select class="form-select border-warning" name="project_id" id="project_id">
                                    <option value="">-- Sélectionner un projet --</option>
                                    @foreach($projects as $pr)
                                        <option value="{{ $pr->id }}"
                                                data-client="{{ $pr->client->nom ?? '' }}"
                                                data-description="Encaissement lié au projet : {{ $pr->nom }}">
                                            {{ $pr->nom }} ({{ $pr->client->nom ?? 'Client' }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6 mb-3 dynamic-field" id="field_avance" style="display:none;">
                                <label class="form-label fw-bold">Client (Acompte) *</label>
                                <select class="form-select" name="client_id">
                                    <option value="">-- Sélectionner le client --</option>
                                    @foreach($clients as $cl)
                                        <option value="{{ $cl->id }}">{{ $cl->nom }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6 mb-3 dynamic-field" id="field_remboursement" style="display:none;">
                                <label class="form-label fw-bold">Fournisseur (Remboursement) *</label>
                                <select class="form-select" name="fournisseur_id">
                                    <option value="">-- Sélectionner le fournisseur --</option>
                                    @foreach($fournisseurs as $fr)
                                        <option value="{{ $fr->id }}">{{ $fr->nom }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- DETAILS FINANCIERS -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="card-title text-success border-bottom pb-2 mb-3">
                                    <i class="fas fa-coins me-2"></i>2. Détails Financiers
                                </h5>
                            </div>

                            <div class="col-md-4 mb-3 text-center bg-light p-3 rounded shadow-sm border">
                                <label class="form-label fw-bold">Date *</label>
                                <input type="date" class="form-control text-center fs-5" name="date_encaissement" value="{{ date('Y-m-d') }}" required>
                            </div>

                            <div class="col-md-4 mb-3 text-center bg-light p-3 rounded shadow-sm border">
                                <label class="form-label fw-bold">Montant (FCFA) *</label>
                                <input type="number" class="form-control text-center fs-4 fw-bold text-success" name="montant" id="montant" step="1" min="0" placeholder="0" required>
                                <div id="montant_info" class="small text-muted mt-1"></div>
                            </div>

                            <div class="col-md-4 mb-3 text-center bg-light p-3 rounded shadow-sm border">
                                <label class="form-label fw-bold">Destination (Caisse) *</label>
                                <select class="form-select text-center fs-5" name="caisse_id" required>
                                    <option value="">-- Choisir la caisse --</option>
                                    @foreach($caisses as $caisse)
                                        <option value="{{ $caisse->id }}">{{ $caisse->nom }} (Solde: {{ number_format($caisse->solde_actuel, 0, ',', ' ') }} )</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- JUSTIFICATION ET TRACE -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="card-title text-muted border-bottom pb-2 mb-3">
                                    <i class="fas fa-file-invoice me-2"></i>3. Justification et Trace
                                </h5>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Libellé / Objet *</label>
                                <input type="text" class="form-control" name="description" id="description" required placeholder="Ex: Règlement partiel facture X...">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Client / Nom du déposant</label>
                                <input type="text" class="form-control" name="beneficiaire" id="beneficiaire" placeholder="Nom pour la trace physique">
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label">Mode de règlement *</label>
                                <select class="form-select" name="mode_paiement" required>
                                    <option value="espece">Espèces</option>
                                    <option value="cheque">Chèque</option>
                                    <option value="virement">Virement Bancaire</option>
                                    <option value="mobile">Mobile Money</option>
                                    <option value="autre">Autre</option>
                                </select>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label">Référence Externe (N° Chèque/Virement)</label>
                                <input type="text" class="form-control" name="reference_externe" placeholder="Ex: CK-2024-001">
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label">Justificatif (Scan/PDF)</label>
                                <input type="file" class="form-control" name="piece_jointe">
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-12">
                                <label class="form-label">Notes et Observations</label>
                                <textarea class="form-control" name="notes" rows="2" placeholder="Informations complémentaires..."></textarea>
                            </div>
                        </div>

                        <!-- BOUTONS -->
                        <div class="row">
                            <div class="col-12">
                                <hr>
                                <div class="d-flex justify-content-between">
                                    <a href="{{ route('tresorerie.encaissements') }}" class="btn btn-outline-secondary btn-lg">
                                        Annuler
                                    </a>
                                    <button type="submit" class="btn btn-success btn-lg px-5 shadow">
                                        <i class="fas fa-check-circle me-2"></i>CONFIRMER L'ENCAISSEMENT
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.getElementById('type_encaissement').addEventListener('change', function() {
        const val = this.value;
        // Masquer tous les champs dynamiques
        document.querySelectorAll('.dynamic-field').forEach(el => el.style.display = 'none');
        
        // Afficher celui correspondant
        const target = document.getElementById('field_' + val);
        if (target) target.style.display = 'block';
        
        // Reset inputs
        document.getElementById('montant').value = '';
        document.getElementById('description').value = '';
        document.getElementById('beneficiaire').value = '';
    });

    // Auto-fill logic for Invoices
    document.getElementById('invoice_id').addEventListener('change', function() {
        const opt = this.options[this.selectedIndex];
        if (opt.value) {
            document.getElementById('montant').value = opt.dataset.reste;
            document.getElementById('description').value = opt.dataset.description;
            document.getElementById('beneficiaire').value = opt.dataset.client;
            document.getElementById('montant_info').innerHTML = "<span class='text-danger'>Reste à payer total : " + opt.dataset.reste + " FCFA</span>";
        }
    });

    // Auto-fill logic for Operations
    document.getElementById('operation_id').addEventListener('change', function() {
        const opt = this.options[this.selectedIndex];
        if (opt.value) {
            document.getElementById('montant').value = opt.dataset.montant;
            document.getElementById('description').value = opt.dataset.description;
            document.getElementById('beneficiaire').value = opt.dataset.client;
        }
    });

    // Auto-fill logic for Projects
    document.getElementById('project_id').addEventListener('change', function() {
        const opt = this.options[this.selectedIndex];
        if (opt.value) {
            document.getElementById('description').value = opt.dataset.description;
            document.getElementById('beneficiaire').value = opt.dataset.client;
        }
    });
</script>
@endpush

@endsection

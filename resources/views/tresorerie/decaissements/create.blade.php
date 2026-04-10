@extends('layouts.app')

@section('title', 'Nouveau Décaissement - KENAM SERVICES')

@section('content')
<div class="container-fluid mt-4">
    <!-- En-tête -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2>
                <i class="fas fa-money-bill-wave me-2"></i>Nouveau Décaissement
            </h2>
            <p class="text-muted mb-0">Enregistrer une nouvelle sortie de fonds</p>
        </div>
        <div>
            <a href="{{ route('tresorerie.decaissements.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i>Retour à la liste
            </a>
        </div>
    </div>

    <!-- Formulaire -->
    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('tresorerie.decaissements.store') }}">
                @csrf

                <!-- Informations générales -->
                <div class="row mb-4">
                    <div class="col-12">
                        <h5 class="border-bottom pb-2 mb-3">
                            <i class="fas fa-info-circle me-2"></i>Informations générales
                        </h5>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Type de décaissement *</label>
                        <select name="type_decaissement" class="form-select @error('type_decaissement') is-invalid @enderror" required>
                            <option value="">Sélectionner</option>
                            <option value="PAIE_SALAIRE" {{ old('type_decaissement') == 'PAIE_SALAIRE' ? 'selected' : '' }}>Paiement des salaires</option>
                            <option value="ACHAT_FOURNISSEUR" {{ old('type_decaissement') == 'ACHAT_FOURNISSEUR' ? 'selected' : '' }}>Achat fournisseur</option>
                            <option value="REMBOURSEMENT" {{ old('type_decaissement') == 'REMBOURSEMENT' ? 'selected' : '' }}>Remboursement</option>
                            <option value="CHARGE_FONCTIONNEMENT" {{ old('type_decaissement') == 'CHARGE_FONCTIONNEMENT' ? 'selected' : '' }}>Charges de fonctionnement</option>
                            <option value="INVESTISSEMENT" {{ old('type_decaissement') == 'INVESTISSEMENT' ? 'selected' : '' }}>Investissement</option>
                            <option value="AUTRE" {{ old('type_decaissement') == 'AUTRE' ? 'selected' : '' }}>Autre</option>
                        </select>
                        @error('type_decaissement')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Date du décaissement *</label>
                        <input type="date" name="date_decaissement" class="form-control @error('date_decaissement') is-invalid @enderror"
                               value="{{ old('date_decaissement') ?? date('Y-m-d') }}" required>
                        @error('date_decaissement')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">Description *</label>
                        <textarea name="description" class="form-control @error('description') is-invalid @enderror"
                                  rows="3" placeholder="Description détaillée du décaissement..." required>{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Montant et devise -->
                <div class="row mb-4">
                    <div class="col-12">
                        <h5 class="border-bottom pb-2 mb-3">
                            <i class="fas fa-coins me-2"></i>Montant et devise
                        </h5>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Montant *</label>
                        <input type="number" name="montant" class="form-control @error('montant') is-invalid @enderror"
                               value="{{ old('montant') }}" min="0" step="0.01" placeholder="0.00" required>
                        @error('montant')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Devise *</label>
                        <select name="devise" class="form-select @error('devise') is-invalid @enderror" required>
                            <option value="">Sélectionner</option>
                            <option value="XOF" {{ old('devise') == 'XOF' ? 'selected' : '' }}>XOF - Franc CFA</option>
                            <option value="EUR" {{ old('devise') == 'EUR' ? 'selected' : '' }}>EUR - Euro</option>
                            <option value="USD" {{ old('devise') == 'USD' ? 'selected' : '' }}>USD - Dollar Américain</option>
                        </select>
                        @error('devise')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Taux de change</label>
                        <input type="number" name="taux_change" class="form-control @error('taux_change') is-invalid @enderror"
                               value="{{ old('taux_change') ?? 1 }}" min="0" step="0.01" placeholder="1.00">
                        @error('taux_change')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Source et destination -->
                <div class="row mb-4">
                    <div class="col-12">
                        <h5 class="border-bottom pb-2 mb-3">
                            <i class="fas fa-exchange-alt me-2"></i>Source et destination
                        </h5>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Source de fonds *</label>
                        <select name="source_fonds" class="form-select @error('source_fonds') is-invalid @enderror" required>
                            <option value="">Sélectionner</option>
                            <option value="CAISSE_PRINCIPALE" {{ old('source_fonds') == 'CAISSE_PRINCIPALE' ? 'selected' : '' }}>Caisse principale</option>
                            <option value="CAISSE_SECONDAIRE" {{ old('source_fonds') == 'CAISSE_SECONDAIRE' ? 'selected' : '' }}>Caisse secondaire</option>
                            <option value="BANQUE_ECOBANK" {{ old('source_fonds') == 'BANQUE_ECOBANK' ? 'selected' : '' }}>Banque - Ecobank</option>
                            <option value="BANQUE_SGBCI" {{ old('source_fonds') == 'BANQUE_SGBCI' ? 'selected' : '' }}>Banque - SGBCI</option>
                            <option value="BANQUE_BIAO" {{ old('source_fonds') == 'BANQUE_BIAO' ? 'selected' : '' }}>Banque - BIAO</option>
                            <option value="AUTRE" {{ old('source_fonds') == 'AUTRE' ? 'selected' : '' }}>Autre</option>
                        </select>
                        @error('source_fonds')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Bénéficiaire *</label>
                        <input type="text" name="beneficiaire" class="form-control @error('beneficiaire') is-invalid @enderror"
                               value="{{ old('beneficiaire') }}" placeholder="Nom du bénéficiaire" required>
                        @error('beneficiaire')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Informations complémentaires -->
                <div class="row mb-4">
                    <div class="col-12">
                        <h5 class="border-bottom pb-2 mb-3">
                            <i class="fas fa-file-invoice me-2"></i>Informations complémentaires
                        </h5>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Référence</label>
                        <input type="text" name="reference" class="form-control @error('reference') is-invalid @enderror"
                               value="{{ old('reference') }}" placeholder="Référence du document">
                        @error('reference')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Catégorie</label>
                        <select name="categorie" class="form-select @error('categorie') is-invalid @enderror">
                            <option value="">Sélectionner</option>
                            <option value="OPERATIONNEL" {{ old('categorie') == 'OPERATIONNEL' ? 'selected' : '' }}>Opérationnel</option>
                            <option value="ADMINISTRATIF" {{ old('categorie') == 'ADMINISTRATIF' ? 'selected' : '' }}>Administratif</option>
                            <option value="FINANCIER" {{ old('categorie') == 'FINANCIER' ? 'selected' : '' }}>Financier</option>
                            <option value="COMMERCIAL" {{ old('categorie') == 'COMMERCIAL' ? 'selected' : '' }}>Commercial</option>
                        </select>
                        @error('categorie')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-12">
                        <label class="form-label">Notes</label>
                        <textarea name="notes" class="form-control @error('notes') is-invalid @enderror"
                                  rows="2" placeholder="Notes supplémentaires...">{{ old('notes') }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Validation -->
                <div class="row mb-4">
                    <div class="col-12">
                        <h5 class="border-bottom pb-2 mb-3">
                            <i class="fas fa-check-circle me-2"></i>Validation
                        </h5>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Validé par</label>
                        <input type="text" name="valide_par" class="form-control @error('valide_par') is-invalid @enderror"
                               value="{{ old('valide_par') ?? Auth::user()->name }}" readonly>
                        @error('valide_par')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Statut</label>
                        <select name="statut" class="form-select @error('statut') is-invalid @enderror">
                            <option value="EN_ATTENTE" {{ old('statut') == 'EN_ATTENTE' ? 'selected' : '' }}>En attente</option>
                            <option value="VALIDE" {{ old('statut') == 'VALIDE' ? 'selected' : '' }}>Validé</option>
                            <option value="REJETE" {{ old('statut') == 'REJETE' ? 'selected' : '' }}>Rejeté</option>
                        </select>
                        @error('statut')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Boutons -->
                <div class="row">
                    <div class="col-12">
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('tresorerie.decaissements.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-1"></i>Annuler
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i>Enregistrer le décaissement
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

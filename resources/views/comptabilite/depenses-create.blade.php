@extends('layouts.app')

@section('title', 'Nouvelle Dépense | KENAM SERVICES')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-gradient-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-0">
                                <i class="fas fa-plus-circle me-2"></i>Nouvelle Dépense
                            </h5>
                            <small class="mb-0 opacity-75">Enregistrer une nouvelle dépense</small>
                        </div>
                        <div>
                            <a href="{{ route('comptabilite.depenses.index') }}" class="btn btn-light btn-sm">
                                <i class="fas fa-arrow-left me-1"></i>Retour
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('comptabilite.depenses.store') }}" enctype="multipart/form-data">
                        @csrf
                        
                        <!-- Informations générales -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-primary fw-bold mb-3">
                                    <i class="fas fa-info-circle me-2"></i>Informations générales
                                </h6>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="libelle" class="form-label fw-bold">Libellé de la dépense *</label>
                                <input type="text" class="form-control @error('libelle') is-invalid @enderror" 
                                       id="libelle" name="libelle" required
                                       value="{{ old('libelle') }}" placeholder="Ex: Achat de fournitures">
                                @error('libelle')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="montant" class="form-label fw-bold">Montant *</label>
                                <div class="input-group">
                                    <span class="input-group-text">FCFA</span>
                                    <input type="number" class="form-control @error('montant') is-invalid @enderror" 
                                           id="montant" name="montant" required step="0.01" min="0"
                                           value="{{ old('montant') }}" placeholder="0.00">
                                </div>
                                @error('montant')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="date_depense" class="form-label fw-bold">Date de la dépense *</label>
                                <input type="date" class="form-control @error('date_depense') is-invalid @enderror" 
                                       id="date_depense" name="date_depense" required
                                       value="{{ old('date_depense', now()->format('Y-m-d')) }}">
                                @error('date_depense')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="categorie" class="form-label fw-bold">Catégorie</label>
                                <select class="form-select @error('categorie') is-invalid @enderror" 
                                        id="categorie" name="categorie">
                                    <option value="">Sélectionner une catégorie</option>
                                    @foreach($categories as $categorie)
                                        <option value="{{ $categorie->id }}" {{ old('categorie') == $categorie->id ? 'selected' : '' }}>
                                            {{ $categorie->nom }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('categorie')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Détails de la transaction -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-primary fw-bold mb-3">
                                    <i class="fas fa-exchange-alt me-2"></i>Détails de la transaction
                                </h6>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="caisse_id" class="form-label fw-bold">Caisse</label>
                                <select class="form-select @error('caisse_id') is-invalid @enderror" 
                                        id="caisse_id" name="caisse_id">
                                    <option value="">Sélectionner une caisse</option>
                                    @foreach($caisses as $caisse)
                                        <option value="{{ $caisse->id }}" {{ old('caisse_id') == $caisse->id ? 'selected' : '' }}>
                                            {{ $caisse->nom }} (Solde: {{ number_format($caisse->solde_actuel, 0, ',', ' ') }} FCFA)
                                        </option>
                                    @endforeach
                                </select>
                                @error('caisse_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="compte_comptable_id" class="form-label fw-bold">Compte comptable</label>
                                <select class="form-select @error('compte_comptable_id') is-invalid @enderror" 
                                        id="compte_comptable_id" name="compte_comptable_id">
                                    <option value="">Sélectionner un compte</option>
                                    @foreach($comptes_comptables as $compte)
                                        <option value="{{ $compte->id }}" {{ old('compte_comptable_id') == $compte->id ? 'selected' : '' }}>
                                            {{ $compte->numero }} - {{ $compte->libelle }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('compte_comptable_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="mode_paiement" class="form-label fw-bold">Mode de paiement</label>
                                <select class="form-select @error('mode_paiement') is-invalid @enderror" 
                                        id="mode_paiement" name="mode_paiement">
                                    <option value="">Sélectionner</option>
                                    <option value="especes" {{ old('mode_paiement') == 'especes' ? 'selected' : '' }}>Espèces</option>
                                    <option value="carte_bancaire" {{ old('mode_paiement') == 'carte_bancaire' ? 'selected' : '' }}>Carte bancaire</option>
                                    <option value="virement" {{ old('mode_paiement') == 'virement' ? 'selected' : '' }}>Virement</option>
                                    <option value="cheque" {{ old('mode_paiement') == 'cheque' ? 'selected' : '' }}>Chèque</option>
                                    <option value="autre" {{ old('mode_paiement') == 'autre' ? 'selected' : '' }}>Autre</option>
                                </select>
                                @error('mode_paiement')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="reference" class="form-label fw-bold">Référence</label>
                                <input type="text" class="form-control @error('reference') is-invalid @enderror" 
                                       id="reference" name="reference"
                                       value="{{ old('reference') }}" placeholder="N° de facture, référence...">
                                @error('reference')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Informations supplémentaires -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-primary fw-bold mb-3">
                                    <i class="fas fa-file-alt me-2"></i>Informations supplémentaires
                                </h6>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="beneficiaire" class="form-label fw-bold">Bénéficiaire</label>
                                <input type="text" class="form-control @error('beneficiaire') is-invalid @enderror" 
                                       id="beneficiaire" name="beneficiaire"
                                       value="{{ old('beneficiaire') }}" placeholder="Nom du bénéficiaire">
                                @error('beneficiaire')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="fournisseur_id" class="form-label fw-bold">Fournisseur</label>
                                <select class="form-select @error('fournisseur_id') is-invalid @enderror" 
                                        id="fournisseur_id" name="fournisseur_id">
                                    <option value="">Sélectionner un fournisseur</option>
                                    @if(isset($fournisseurs))
                                        @foreach($fournisseurs as $fournisseur)
                                            <option value="{{ $fournisseur->id }}" {{ old('fournisseur_id') == $fournisseur->id ? 'selected' : '' }}>
                                                {{ $fournisseur->nom }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                                @error('fournisseur_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-12 mb-3">
                                <label for="description" class="form-label fw-bold">Description</label>
                                <textarea class="form-control @error('description') is-invalid @enderror" 
                                          id="description" name="description" rows="3"
                                          placeholder="Description détaillée de la dépense...">{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Justificatif -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h6 class="text-primary fw-bold mb-3">
                                    <i class="fas fa-paperclip me-2"></i>Justificatif
                                </h6>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="justificatif" class="form-label fw-bold">Pièce justificative</label>
                                <input type="file" class="form-control @error('justificatif') is-invalid @enderror" 
                                       id="justificatif" name="justificatif" accept=".pdf,.jpg,.jpeg,.png">
                                <small class="text-muted">Formats acceptés: PDF, JPG, PNG (Max: 5MB)</small>
                                @error('justificatif')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Boutons d'action -->
                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <a href="{{ route('comptabilite.depenses.index') }}" class="btn btn-secondary">
                                            <i class="fas fa-times me-2"></i>Annuler
                                        </a>
                                    </div>
                                    <div>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save me-2"></i>Enregistrer la dépense
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Validation du formulaire
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.querySelector('form');
        const montantInput = document.getElementById('montant');
        const caisseSelect = document.getElementById('caisse_id');
        
        // Validation du montant
        montantInput.addEventListener('input', function() {
            if (this.value < 0) {
                this.value = 0;
            }
        });
        
        // Validation avant soumission
        form.addEventListener('submit', function(e) {
            let isValid = true;
            
            // Vérifier que le montant est positif
            if (parseFloat(montantInput.value) <= 0) {
                montantInput.classList.add('is-invalid');
                isValid = false;
            } else {
                montantInput.classList.remove('is-invalid');
            }
            
            if (!isValid) {
                e.preventDefault();
            }
        });
    });
</script>
@endpush

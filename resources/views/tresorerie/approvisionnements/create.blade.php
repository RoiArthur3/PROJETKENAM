@extends('layouts.app')

@section('title', 'Nouvel Approvisionnement de Caisse')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-md-8">
            <h4 class="mb-0">
                <i class="fas fa-money-bill-transfer text-primary me-2"></i>
                Approvisionnement de Caisse
            </h4>
            <small class="text-muted">Versez de l'argent dans une caisse ou transférez entre caisses.</small>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('tresorerie.approvisionnements.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> Retour
            </a>
        </div>
    </div>

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $message)
                    <li>{{ $message }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('tresorerie.approvisionnements.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="row">
            <div class="col-lg-8">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white">
                        <h6 class="mb-0">
                            <i class="fas fa-info-circle text-primary me-2"></i>
                            Informations de l'approvisionnement
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Référence</label>
                                <input type="text" class="form-control" value="{{ $reference }}" readonly>
                                <small class="text-muted">Générée automatiquement</small>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Mode de paiement <span class="text-danger">*</span></label>
                                <select name="mode" class="form-select @error('mode') is-invalid @enderror" required>
                                    <option value="">Choisir un mode</option>
                                    <option value="Espèces" {{ old('mode') == 'Espèces' ? 'selected' : '' }}>Espèces</option>
                                    <option value="Virement bancaire" {{ old('mode') == 'Virement bancaire' ? 'selected' : '' }}>Virement bancaire</option>
                                    <option value="Chèque" {{ old('mode') == 'Chèque' ? 'selected' : '' }}>Chèque</option>
                                    <option value="Mobile money" {{ old('mode') == 'Mobile money' ? 'selected' : '' }}>Mobile money</option>
                                    <option value="Carte bancaire" {{ old('mode') == 'Carte bancaire' ? 'selected' : '' }}>Carte bancaire</option>
                                    <option value="Autres" {{ old('mode') == 'Autres' ? 'selected' : '' }}>Autres</option>
                                </select>
                                @error('mode') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Caisse à approvisionner <span class="text-danger">*</span></label>
                                <select name="caisse_destination_id" class="form-select @error('caisse_destination_id') is-invalid @enderror" required>
                                    <option value="">Sélectionner la caisse</option>
                                    @foreach($caisses as $caisse)
                                        <option value="{{ $caisse->id }}" {{ old('caisse_destination_id') == $caisse->id ? 'selected' : '' }}>
                                            {{ $caisse->nom ?? $caisse->libelle ?? 'Caisse #'.$caisse->id }}
                                            ({{ number_format($caisse->solde_actuel ?? 0, 0, ',', ' ') }} FCFA)
                                        </option>
                                    @endforeach
                                </select>
                                @error('caisse_destination_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Prendre depuis une autre caisse <small class="text-muted">(optionnel)</small></label>
                                <select name="caisse_source_id" class="form-select @error('caisse_source_id') is-invalid @enderror">
                                    <option value="">-- Aucune (ajout direct) --</option>
                                    @foreach($caisses as $caisse)
                                        <option value="{{ $caisse->id }}" {{ old('caisse_source_id') == $caisse->id ? 'selected' : '' }}>
                                            {{ $caisse->nom ?? $caisse->libelle ?? 'Caisse #'.$caisse->id }}
                                            ({{ number_format($caisse->solde_actuel ?? 0, 0, ',', ' ') }} FCFA)
                                        </option>
                                    @endforeach
                                </select>
                                @error('caisse_source_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                <small class="text-muted">Si renseigné, le montant sera transféré de cette caisse vers la caisse cible.</small>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Montant <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="number" step="1" class="form-control @error('montant') is-invalid @enderror"
                                           name="montant" value="{{ old('montant') }}" required placeholder="0">
                                    <span class="input-group-text">FCFA</span>
                                </div>
                                @error('montant') <div class="text-danger small">{{ $message }}</div> @enderror
                                <small class="text-muted">Un montant negatif permet d'annuler/corriger un approvisionnement.</small>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Motif <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('motif') is-invalid @enderror"
                                      name="motif" rows="3" required placeholder="Raison de l'approvisionnement">{{ old('motif') }}</textarea>
                            @error('motif') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Pièces jointes</label>
                            <input type="file" class="form-control" name="pieces_jointes[]" multiple accept=".pdf,.jpg,.jpeg,.png">
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white">
                        <h6 class="mb-0">
                            <i class="fas fa-question-circle text-info me-2"></i>
                            Comment ça marche
                        </h6>
                    </div>
                    <div class="card-body small">
                        <p class="mb-2"><strong>Ajout direct :</strong> Versez de l'argent dans une caisse (espèces, virement, chèque, etc.). Ne renseignez pas de caisse source.</p>
                        <p class="mb-2"><strong>Transfert :</strong> Transférez de l'argent d'une caisse vers une autre en renseignant les deux caisses.</p>
                        <p class="mb-2"><strong>Mode :</strong> Indiquez comment l'argent arrive (espèces, virement, chèque, mobile money...).</p>
                    </div>
                </div>

                <div class="card shadow-sm">
                    <div class="card-body">
                        <button type="submit" class="btn btn-primary w-100 mb-2">
                            <i class="fas fa-save me-1"></i> Enregistrer l'approvisionnement
                        </button>
                        <a href="{{ route('tresorerie.approvisionnements.index') }}" class="btn btn-outline-secondary w-100">
                            <i class="fas fa-times me-1"></i> Annuler
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@extends('layouts.app')

@section('title', 'Nouveau Rapprochement de Caisse')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Tableau de bord</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('tresorerie.rapprochements.index') }}">Rapprochements</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Nouveau rapprochement</li>
                </ol>
            </nav>
            <h1>Nouveau Rapprochement de Caisse</h1>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('tresorerie.rapprochements.store') }}" method="POST" id="rapprochementForm">
                        @csrf

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="caisse_id" class="form-label">Caisse *</label>
                                <select name="caisse_id" id="caisse_id" class="form-select @error('caisse_id') is-invalid @enderror" required>
                                    <option value="">Sélectionner une caisse</option>
                                    @foreach($caisses as $caisse)
                                        <option value="{{ $caisse->id }}" data-solde="{{ $caisse->solde_actuel }}" {{ old('caisse_id') == $caisse->id ? 'selected' : '' }}>
                                            {{ $caisse->nom }} ({{ number_format($caisse->solde_actuel, 0, ',', ' ') }} FCFA)
                                        </option>
                                    @endforeach
                                </select>
                                @error('caisse_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="date_rapprochement" class="form-label">Date du rapprochement *</label>
                                <input type="date" class="form-control @error('date_rapprochement') is-invalid @enderror"
                                       id="date_rapprochement" name="date_rapprochement"
                                       value="{{ old('date_rapprochement', now()->format('Y-m-d')) }}" required>
                                @error('date_rapprochement')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="solde_comptable" class="form-label">Solde comptable *</label>
                                <div class="input-group">
                                    <input type="number" step="0.01" class="form-control @error('solde_comptable') is-invalid @enderror"
                                           id="solde_comptable" name="solde_comptable"
                                           value="{{ old('solde_comptable') }}" required>
                                    <span class="input-group-text">FCFA</span>
                                    @error('solde_comptable')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label for="solde_reel" class="form-label">Solde réel (physique) *</label>
                                <div class="input-group">
                                    <input type="number" step="0.01" class="form-control @error('solde_reel') is-invalid @enderror"
                                           id="solde_reel" name="solde_reel"
                                           value="{{ old('solde_reel') }}" required>
                                    <span class="input-group-text">FCFA</span>
                                    @error('solde_reel')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="ecart" class="form-label">Écart</label>
                            <div class="input-group">
                                <input type="text" class="form-control bg-light" id="ecart" name="ecart" readonly>
                                <span class="input-group-text">FCFA</span>
                            </div>
                            <small class="form-text text-muted">Écart calculé automatiquement</small>
                        </div>

                        <div class="mb-3">
                            <label for="commentaire" class="form-label">Commentaire</label>
                            <textarea class="form-control @error('commentaire') is-invalid @enderror"
                                      id="commentaire" name="commentaire" rows="3">{{ old('commentaire') }}</textarea>
                            @error('commentaire')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('tresorerie.rapprochements.index') }}" class="btn btn-light">
                                <i class="fas fa-arrow-left me-1"></i> Annuler
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-check-circle me-1"></i> Valider le rapprochement
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0">Instructions</h5>
                </div>
                <div class="card-body">
                    <h6>Comment effectuer un rapprochement ?</h6>
                    <ol class="small">
                        <li>Sélectionnez la caisse à rapprocher</li>
                        <li>Entrez le solde comptable (valeur dans le système)</li>
                        <li>Comptez physiquement l'argent dans la caisse et entrez le montant</li>
                        <li>L'écart sera calculé automatiquement</li>
                        <li>Ajoutez un commentaire si nécessaire</li>
                        <li>Validez le rapprochement</li>
                    </ol>
                    <div class="alert alert-info mt-3 small">
                        <i class="fas fa-info-circle me-2"></i>
                        En cas d'écart important, il est recommandé de vérifier les opérations récentes
                        avant de valider le rapprochement.
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Initialisation des sélecteurs avec Select2
        $('#caisse_id').select2({
            theme: 'bootstrap-5',
            width: '100%',
            placeholder: 'Sélectionner une caisse',
            allowClear: true
        });

        // Calcul automatique de l'écart
        function calculerEcart() {
            const soldeComptable = parseFloat($('#solde_comptable').val()) || 0;
            const soldeReel = parseFloat($('#solde_reel').val()) || 0;
            const ecart = soldeReel - soldeComptable;

            $('#ecart').val(ecart.toFixed(2));

            // Mise en forme de l'écart
            if (ecart > 0) {
                $('#ecart').removeClass('text-danger').addClass('text-success');
            } else if (ecart < 0) {
                $('#ecart').removeClass('text-success').addClass('text-danger');
            } else {
                $('#ecart').removeClass('text-danger text-success');
            }
        }

        // Écouteurs d'événements pour le calcul de l'écart
        $('#solde_comptable, #solde_reel').on('input', calculerEcart);

        // Pré-remplir le solde comptable avec le solde actuel de la caisse sélectionnée
        $('#caisse_id').on('change', function() {
            const selectedOption = $(this).find('option:selected');
            const soldeActuel = parseFloat(selectedOption.data('solde')) || 0;
            $('#solde_comptable').val(soldeActuel.toFixed(2));
            calculerEcart();
        });

        // Validation du formulaire
        $('#rapprochementForm').on('submit', function(e) {
            const ecart = parseFloat($('#ecart').val()) || 0;

            if (ecart !== 0) {
                return confirm('Attention : Il y a un écart entre le solde comptable et le solde réel. Êtes-vous sûr de vouloir valider ce rapprochement ?');
            }

            return true;
        });
    });
</script>
@endpush

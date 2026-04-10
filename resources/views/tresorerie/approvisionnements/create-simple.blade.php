@extends('layouts.app')

@section('title', 'Approvisionnement Simple')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Approvisionnement de Caisse</h3>
                    <div class="card-tools">
                        <a href="{{ route('approvisionnements.index') }}" class="btn btn-default btn-sm">
                            <i class="fas fa-arrow-left"></i> Retour
                        </a>
                    </div>
                </div>
                <form action="{{ route('approvisionnements.store-simple') }}" method="POST">
                    @csrf
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="caisse_id">Caisse <span class="text-danger">*</span></label>
                                    <select name="caisse_id" id="caisse_id" class="form-control" required>
                                        <option value="">Sélectionner une caisse</option>
                                        @foreach($caisses as $caisse)
                                            <option value="{{ $caisse->id }}" 
                                                data-solde="{{ $caisse->solde_actuel }}">
                                                {{ $caisse->nom }} (Solde: {{ number_format($caisse->solde_actuel, 0, ',', ' ') }} FCFA)
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="montant">Montant <span class="text-danger">*</span></label>
                                    <input type="number" name="montant" id="montant" class="form-control" 
                                           step="0.01" min="0.01" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="mode_paiement">Mode de paiement <span class="text-danger">*</span></label>
                                    <select name="mode_paiement" id="mode_paiement" class="form-control" required>
                                        <option value="">Sélectionner</option>
                                        <option value="especes">Espèces</option>
                                        <option value="virement">Virement</option>
                                        <option value="cheque">Chèque</option>
                                        <option value="carte">Carte</option>
                                        <option value="mobile_money">Mobile Money</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="reference">Référence</label>
                                    <input type="text" name="reference" id="reference" class="form-control" 
                                           placeholder="N° chèque, référence virement, etc.">
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="motif">Motif <span class="text-danger">*</span></label>
                                    <textarea name="motif" id="motif" class="form-control" rows="3" required
                                              placeholder="Description du motif d'approvisionnement..."></textarea>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-12">
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i>
                                    <strong>Information:</strong> Le solde de la caisse sera mis à jour automatiquement après validation.
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Enregistrer l'approvisionnement
                        </button>
                        <a href="{{ route('approvisionnements.index') }}" class="btn btn-default">
                            <i class="fas fa-times"></i> Annuler
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const montantInput = document.getElementById('montant');
    const caisseSelect = document.getElementById('caisse_id');
    
    // Afficher le solde actuel quand on sélectionne une caisse
    caisseSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const solde = selectedOption.getAttribute('data-solde');
        if (solde) {
            console.log('Solde actuel de la caisse:', solde + ' FCFA');
        }
    });
    
    // Formater le montant
    montantInput.addEventListener('blur', function() {
        if (this.value) {
            const montant = parseFloat(this.value);
            if (!isNaN(montant)) {
                console.log('Montant à approvisionner:', montant.toLocaleString('fr-FR') + ' FCFA');
            }
        }
    });
});
</script>
@endpush

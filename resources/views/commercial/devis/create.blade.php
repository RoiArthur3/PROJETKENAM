@extends('layouts.app')

@section('title', 'Créer un Devis - KENAM SERVICES')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-file-invoice-dollar me-2"></i>Nouveau Devis / Proforma
                    </h6>
                    <a href="{{ route('commercial.devis.index') }}" class="btn btn-sm btn-secondary">
                        <i class="fas fa-arrow-left me-1"></i>Retour
                    </a>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('commercial.devis.store') }}">
                        @csrf

                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="text-primary mb-3">Informations Client</h5>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="client_id" class="form-label fw-bold">Client *</label>
                                <select name="client_id" id="client_id" class="form-select @error('client_id') is-invalid @enderror" required>
                                    <option value="">-- Sélectionner un client --</option>
                                    @foreach($clients as $client)
                                        <option value="{{ $client->id }}" {{ old('client_id') == $client->id ? 'selected' : '' }}>
                                            {{ $client->nom ?? ($client->raison_sociale ?? $client->company_name) }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('client_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="statut" class="form-label fw-bold">Statut *</label>
                                <select name="statut" id="statut" class="form-select @error('statut') is-invalid @enderror" required>
                                    <option value="">-- Sélectionner un statut --</option>
                                    <option value="en_attente" {{ old('statut') == 'en_attente' ? 'selected' : '' }}>En attente</option>
                                    <option value="accepte" {{ old('statut') == 'accepte' ? 'selected' : '' }}>Accepté</option>
                                    <option value="rejete" {{ old('statut') == 'rejete' ? 'selected' : '' }}>Rejeté</option>
                                </select>
                                @error('statut')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="text-primary mb-3">Références</h5>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="contrat_ref" class="form-label fw-bold">Contrat de référence</label>
                                <input type="text" name="contrat_ref" id="contrat_ref"
                                       class="form-control @error('contrat_ref') is-invalid @enderror"
                                       value="{{ old('contrat_ref') }}"
                                       placeholder="Ex: CTR-2024-001">
                            </div>

                            <div class="col-12 mb-3">
                                <label for="vehicule_id" class="form-label fw-bold">Matériel Roulant / Engin (Selectionner pour auto-remplir le prix)</label>
                                <select name="vehicule_id" id="vehicule_id" class="form-select" onchange="updatePriceFromVehicle()">
                                    <option value="">-- Aucun matériel spécifique --</option>
                                    @foreach($vehicules as $v)
                                        <option value="{{ $v->id }}" data-price="{{ $v->prix_location }}" data-date="{{ $v->date_debut_contrat }}">
                                            {{ $v->immatriculation }} - {{ $v->marque }} {{ $v->modele }} ({{ number_format($v->prix_location, 0, ',', ' ') }} FCFA/j)
                                        </option>
                                    @endforeach
                                </select>
                                <div class="form-text">Si vous séléctionnez un matériel, le prix journalier sera automatiquement appliqué.</div>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="text-primary mb-3">Dates</h5>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="issue_date" class="form-label fw-bold">Date d'émission *</label>
                                <input type="date" name="issue_date" id="issue_date"
                                       class="form-control @error('issue_date') is-invalid @enderror"
                                       value="{{ old('issue_date', date('Y-m-d')) }}" required>
                                @error('issue_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="due_date" class="form-label fw-bold">Date d'échéance</label>
                                <input type="date" name="due_date" id="due_date"
                                       class="form-control @error('due_date') is-invalid @enderror"
                                       value="{{ old('due_date') }}">
                                @error('due_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="validite" class="form-label fw-bold">Validité du devis</label>
                                <input type="date" name="validite" id="validite"
                                       class="form-control @error('validite') is-invalid @enderror"
                                       value="{{ old('validite') }}">
                                @error('validite')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="text-primary mb-3">Détails Financiers</h5>
                            </div>

                            <div class="col-12 mb-3">
                                <label for="objet" class="form-label fw-bold">Objet</label>
                                <input type="text" name="objet" id="objet"
                                       class="form-control @error('objet') is-invalid @enderror"
                                       value="{{ old('objet') }}"
                                       placeholder="Ex: Prestation transport novembre 2024">
                                @error('objet')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="montant_ht" class="form-label fw-bold">Montant HT (FCFA) *</label>
                                <input type="number" name="montant_ht" id="montant_ht"
                                       class="form-control @error('montant_ht') is-invalid @enderror"
                                       value="{{ old('montant_ht') }}"
                                       min="0" step="0.01" required
                                       oninput="calculerTTC()">
                                @error('montant_ht')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="montant_tva" class="form-label fw-bold">TVA (18%)</label>
                                <input type="number" id="montant_tva"
                                       class="form-control bg-light" readonly>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="montant_ttc" class="form-label fw-bold">Total TTC (FCFA)</label>
                                <input type="number" id="montant_ttc"
                                       class="form-control bg-light fw-bold" readonly>
                            </div>

                            <div class="col-12 mb-3">
                                <label for="notes" class="form-label fw-bold">Notes</label>
                                <textarea name="notes" id="notes" rows="3"
                                          class="form-control @error('notes') is-invalid @enderror"
                                          placeholder="Informations complémentaires...">{{ old('notes') }}</textarea>
                                @error('notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                            <a href="{{ route('commercial.devis.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-2"></i>Annuler
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Enregistrer le Devis
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function updatePriceFromVehicle() {
    const select = document.getElementById('vehicule_id');
    const selectedOption = select.options[select.selectedIndex];
    
    if (selectedOption.value) {
        const price = selectedOption.getAttribute('data-price');
        const startDate = selectedOption.getAttribute('data-date');
        
        if (price) {
            document.getElementById('montant_ht').value = price;
            calculerTTC();
        }
        
        if (startDate && startDate !== '') {
            // Optionnel: On peut aussi remplir la date d'émission ou une note
            const notes = document.getElementById('notes');
            notes.value = "Location démarrant le " + startDate + ". " + notes.value;
        }
    }
}

function calculerTTC() {
    const ht = parseFloat(document.getElementById('montant_ht').value) || 0;
    const tva = ht * 0.18;
    const ttc = ht + tva;

    document.getElementById('montant_tva').value = Math.round(tva);
    document.getElementById('montant_ttc').value = Math.round(ttc);
}

// Calculer au chargement si valeur présente
document.addEventListener('DOMContentLoaded', function() {
    calculerTTC();
});
</script>
@endsection

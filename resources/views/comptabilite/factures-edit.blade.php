@extends('layouts.app')

@section('title', 'Modifier Facture - KENAM SERVICES')

@section('content')
<div class="container-fluid mt-4">
    <!-- En-tête -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2>
                <i class="fas fa-edit me-2"></i>Modifier Facture
            </h2>
            <p class="text-muted mb-0">Modifier les informations de la facture</p>
        </div>
        <div>
            <a href="{{ route('comptabilite.factures.show', $facture->id) }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i>Retour
            </a>
        </div>
    </div>

    <!-- Formulaire -->
    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('comptabilite.factures.update', $facture->id) }}">
                @csrf
                @method('PUT')

                <!-- Informations générales -->
                <div class="row mb-4">
                    <div class="col-12">
                        <h5 class="border-bottom pb-2 mb-3">
                            <i class="fas fa-file-invoice me-2"></i>Informations générales
                        </h5>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Numéro Facture</label>
                        <input type="text" name="numero_facture" class="form-control @error('numero_facture') is-invalid @enderror"
                               value="{{ old('numero_facture', $facture->numero_facture ?? $facture->numero ?? '') }}" readonly>
                        @error('numero_facture')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Numéro Facture FNE</label>
                        <input type="text" name="numero_fne" class="form-control @error('numero_fne') is-invalid @enderror"
                               value="{{ old('numero_fne', $facture->numero_fne ?? '') }}"
                               placeholder="Renseigner manuellement le numéro FNE">
                        @error('numero_fne')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mt-3">
                        <label class="form-label">Mission (Cost Control)</label>
                        <select id="vehicle_mission_id" name="vehicle_mission_id" class="form-select @error('vehicle_mission_id') is-invalid @enderror">
                            <option value="">Sélectionner une mission</option>
                            @foreach(($missions ?? collect()) as $mission)
                                <option value="{{ $mission->id }}" {{ (string) old('vehicle_mission_id', $facture->vehicle_mission_id ?? '') === (string) $mission->id ? 'selected' : '' }}>
                                    {{ $mission->reference }}
                                </option>
                            @endforeach
                        </select>
                        @error('vehicle_mission_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mt-3">
                        <label class="form-label">Bon de commande</label>
                        <select id="bon_commande_id" name="bon_commande_id" class="form-select @error('bon_commande_id') is-invalid @enderror">
                            <option value="">Sélectionner un bon de commande</option>
                            @foreach(($bonsCommande ?? collect()) as $bc)
                                <option value="{{ $bc->id }}" data-client-id="{{ $bc->client_id }}" {{ (string) old('bon_commande_id', $facture->bon_commande_id ?? '') === (string) $bc->id ? 'selected' : '' }}>
                                    {{ $bc->numero }}
                                </option>
                            @endforeach
                        </select>
                        @error('bon_commande_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mt-3">
                        <label class="form-label">Client *</label>
                        <select id="client_id" name="client_id" class="form-select @error('client_id') is-invalid @enderror" required>
                            <option value="">Sélectionner un client</option>
                            @foreach($clients as $client)
                                <option value="{{ $client->id }}" {{ old('client_id', $facture->client_id) == $client->id ? 'selected' : '' }}>
                                    {{ $client->nom }}
                                </option>
                            @endforeach
                        </select>
                        @error('client_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mt-3">
                        <label class="form-label">Date Facture *</label>
                        <input type="date" name="date_facture" class="form-control @error('date_facture') is-invalid @enderror"
                               value="{{ old('date_facture', isset($facture->date_facture) ? \Carbon\Carbon::parse($facture->date_facture)->format('Y-m-d') : (isset($facture->date_facturation) ? \Carbon\Carbon::parse($facture->date_facturation)->format('Y-m-d') : '')) }}" required>
                        @error('date_facture')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6 mt-3">
                        <label class="form-label">Date d'échéance</label>
                        <input type="date" name="date_echeance" class="form-control @error('date_echeance') is-invalid @enderror"
                               value="{{ old('date_echeance', isset($facture->date_echeance) && $facture->date_echeance ? \Carbon\Carbon::parse($facture->date_echeance)->format('Y-m-d') : '') }}">
                        @error('date_echeance')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Montants -->
                <div class="row mb-4">
                    <div class="col-12">
                        <h5 class="border-bottom pb-2 mb-3">
                            <i class="fas fa-calculator me-2"></i>Montants
                        </h5>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Montant HT *</label>
                        <input type="number" name="montant_ht" class="form-control @error('montant_ht') is-invalid @enderror"
                               value="{{ old('montant_ht', $facture->montant_ht) }}" step="0.01" min="0" required>
                        @error('montant_ht')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">TVA (%)</label>
                        <input type="number" name="tva" class="form-control @error('tva') is-invalid @enderror"
                               value="{{ old('tva', $facture->tva) }}" step="0.01" min="0" max="100">
                        @error('tva')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Montant TTC</label>
                        <input type="number" name="montant_ttc" class="form-control @error('montant_ttc') is-invalid @enderror"
                               value="{{ old('montant_ttc', $facture->montant_ttc) }}" step="0.01" min="0" readonly>
                        @error('montant_ttc')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Statut -->
                <div class="row mb-4">
                    <div class="col-12">
                        <h5 class="border-bottom pb-2 mb-3">
                            <i class="fas fa-info-circle me-2"></i>Statut
                        </h5>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Statut *</label>
                        <select name="statut" class="form-select @error('statut') is-invalid @enderror" required>
                            <option value="">Sélectionner</option>
                            <option value="en_attente" {{ old('statut', $facture->statut) == 'en_attente' ? 'selected' : '' }}>En attente</option>
                            <option value="payee" {{ old('statut', $facture->statut) == 'payee' ? 'selected' : '' }}>Payée</option>
                            <option value="en_retard" {{ old('statut', $facture->statut) == 'en_retard' ? 'selected' : '' }}>En retard</option>
                            <option value="annulee" {{ old('statut', $facture->statut) == 'annulee' ? 'selected' : '' }}>Annulée</option>
                        </select>
                        @error('statut')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Notes -->
                <div class="row mb-4">
                    <div class="col-12">
                        <h5 class="border-bottom pb-2 mb-3">
                            <i class="fas fa-sticky-note me-2"></i>Notes
                        </h5>
                        <div class="col-md-12">
                            <label class="form-label">Observations</label>
                            <textarea name="observations" class="form-control @error('observations') is-invalid @enderror"
                                      rows="3">{{ old('observations', $facture->observations) }}</textarea>
                            @error('observations')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Boutons -->
                <div class="row">
                    <div class="col-12">
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('comptabilite.factures.show', $facture->id) }}" class="btn btn-secondary">
                                <i class="fas fa-times me-1"></i>Annuler
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i>Mettre à jour la Facture
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
const missionMeta = @json($missionMeta ?? []);

// Calcul automatique du montant TTC
document.querySelector('input[name="montant_ht"]').addEventListener('input', function() {
    const montantHt = parseFloat(this.value) || 0;
    const tva = parseFloat(document.querySelector('input[name="tva"]').value) || 0;
    const montantTtc = montantHt * (1 + tva / 100);
    document.querySelector('input[name="montant_ttc"]').value = montantTtc.toFixed(2);
});

document.querySelector('input[name="tva"]').addEventListener('input', function() {
    const montantHt = parseFloat(document.querySelector('input[name="montant_ht"]').value) || 0;
    const tva = parseFloat(this.value) || 0;
    const montantTtc = montantHt * (1 + tva / 100);
    document.querySelector('input[name="montant_ttc"]').value = montantTtc.toFixed(2);
});

const missionSelect = document.getElementById('vehicle_mission_id');
const clientSelect = document.getElementById('client_id');
const bcSelect = document.getElementById('bon_commande_id');

if (missionSelect) {
    missionSelect.addEventListener('change', function() {
        const missionId = this.value;
        const meta = missionMeta[missionId] || null;

        if (!meta) {
            return;
        }

        if (meta.client_id) {
            clientSelect.value = String(meta.client_id);
        }

        if (meta.bon_commande_id) {
            bcSelect.value = String(meta.bon_commande_id);
        }
    });
}

if (bcSelect) {
    bcSelect.addEventListener('change', function() {
        const selected = this.options[this.selectedIndex];
        const clientId = selected ? selected.getAttribute('data-client-id') : null;

        if (clientId) {
            clientSelect.value = String(clientId);
        }
    });
}
</script>
@endsection

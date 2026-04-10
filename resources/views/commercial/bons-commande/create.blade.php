@extends('layouts.app')

@section('title', 'Nouveau Bon de Commande | KENAM SERVICES')

@section('content')
<x-list-layout
    title="Nouveau Bon de Commande"
    icon="fa-solid fa-file-invoice"
    createRoute="commercial.bons-commande.index"
    createText="Retour à la liste"
>
    <x-slot name="kpis">
        <x-kpi-card title="Total Bons" value="{{ DB::table('bons_commande')->count() }}" icon="fa-solid fa-file-invoice" color="blue" />
        <x-kpi-card title="En Attente" value="{{ DB::table('bons_commande')->where('statut', 'brouillon')->count() }}" icon="fa-solid fa-clock" color="yellow" />
        <x-kpi-card title="Validés" value="{{ DB::table('bons_commande')->where('statut', 'valide')->count() }}" icon="fa-solid fa-check-circle" color="green" />
        <x-kpi-card title="Livrés" value="{{ DB::table('bons_commande')->where('statut', 'livre')->count() }}" icon="fa-solid fa-truck" color="emerald" />
    </x-slot>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fa-solid fa-file-invoice me-2"></i>
                        Nouveau Bon de Commande
                    </h3>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('commercial.bons-commande.store') }}" class="row g-3">
                        @csrf

                        <!-- Client -->
                        <div class="col-md-6">
                            <label for="client_id" class="form-label">Client *</label>
                            <select name="client_id" id="client_id" class="form-select @error('client_id', 'is-invalid')" required>
                                <option value="">Sélectionner un client</option>
                                @foreach($clients as $client)
                                    <option value="{{ $client->id }}" {{ old('client_id') == $client->id ? 'selected' : '' }}>
                                        {{ $client->nom }} ({{ $client->email }})
                                    </option>
                                @endforeach
                            </select>
                            @error('client_id')
                                <div class="invalid-feedback d-block">
                                    <strong>{{ $message }}</strong>
                                </div>
                            @enderror
                        </div>

                        <!-- Contrat -->
                        <div class="col-md-6">
                            <label for="contrat_id" class="form-label">Contrat associé</label>
                            <select name="contrat_id" id="contrat_id" class="form-select @error('contrat_id', 'is-invalid')">
                                <option value="">Aucun contrat</option>
                                @foreach($contrats as $contrat)
                                    <option value="{{ $contrat->id }}" {{ old('contrat_id') == $contrat->id ? 'selected' : '' }}>
                                        {{ $contrat->numero }} - {{ $contrat->client_nom }}
                                    </option>
                                @endforeach
                            </select>
                            @error('contrat_id')
                                <div class="invalid-feedback d-block">
                                    <strong>{{ $message }}</strong>
                                </div>
                            @enderror
                        </div>

                        <!-- Numéro -->
                        <div class="col-md-6">
                            <label for="numero" class="form-label">Numéro du bon *</label>
                            <input type="text" name="numero" id="numero" class="form-control @error('numero', 'is-invalid')"
                                   value="{{ old('numero') }}" placeholder="Ex: BC-2025-001" required>
                            @error('numero')
                                <div class="invalid-feedback d-block">
                                    <strong>{{ $message }}</strong>
                                </div>
                            @enderror
                        </div>

                        <!-- Statut -->
                        <div class="col-md-6">
                            <label for="statut" class="form-label">Statut *</label>
                            <select name="statut" id="statut" class="form-select @error('statut', 'is-invalid')" required>
                                <option value="">Sélectionner un statut</option>
                                <option value="brouillon" {{ old('statut') == 'brouillon' ? 'selected' : '' }}>Brouillon</option>
                                <option value="envoye" {{ old('statut') == 'envoye' ? 'selected' : '' }}>Envoyé</option>
                                <option value="valide" {{ old('statut') == 'valide' ? 'selected' : '' }}>Validé</option>
                                <option value="en_preparation" {{ old('statut') == 'en_preparation' ? 'selected' : '' }}>En préparation</option>
                                <option value="livre" {{ old('statut') == 'livre' ? 'selected' : '' }}>Livre</option>
                                <option value="annule" {{ old('statut') == 'annule' ? 'selected' : '' }}>Annulé</option>
                            </select>
                            @error('statut')
                                <div class="invalid-feedback d-block">
                                    <strong>{{ $message }}</strong>
                                </div>
                            @enderror
                        </div>

                        <!-- Date de commande -->
                        <div class="col-md-6">
                            <label for="date_commande" class="form-label">Date de commande *</label>
                            <input type="date" name="date_commande" id="date_commande" class="form-control @error('date_commande', 'is-invalid')"
                                   value="{{ old('date_commande') }}" required>
                            @error('date_commande')
                                <div class="invalid-feedback d-block">
                                    <strong>{{ $message }}</strong>
                                </div>
                            @enderror
                        </div>

                        <!-- Date de livraison prévue -->
                        <div class="col-md-6">
                            <label for="date_livraison_prevue" class="form-label">Date de livraison prévue</label>
                            <input type="date" name="date_livraison_prevue" id="date_livraison_prevue" class="form-control @error('date_livraison_prevue', 'is-invalid')"
                                   value="{{ old('date_livraison_prevue') }}">
                            @error('date_livraison_prevue')
                                <div class="invalid-feedback d-block">
                                    <strong>{{ $message }}</strong>
                                </div>
                            @enderror
                        </div>

                        <!-- Montant HT -->
                        <div class="col-md-4">
                            <label for="montant_ht" class="form-label">Montant HT (FCFA) *</label>
                            <input type="number" name="montant_ht" id="montant_ht" class="form-control @error('montant_ht', 'is-invalid')"
                                   value="{{ old('montant_ht') }}" step="0.01" min="0" placeholder="0.00" required>
                            @error('montant_ht')
                                <div class="invalid-feedback d-block">
                                    <strong>{{ $message }}</strong>
                                </div>
                            @enderror
                        </div>

                        <!-- TVA -->
                        <div class="col-md-4">
                            <label for="tva" class="form-label">TVA (%) *</label>
                            <input type="number" name="tva" id="tva" class="form-control @error('tva', 'is-invalid')"
                                   value="{{ old('tva', 20) }}" step="0.1" min="0" max="100" placeholder="20.0" required>
                            @error('tva')
                                <div class="invalid-feedback d-block">
                                    <strong>{{ $message }}</strong>
                                </div>
                            @enderror
                        </div>

                        <!-- Montant TTC (calculé) -->
                        <div class="col-md-4">
                            <label for="montant_ttc" class="form-label">Montant TTC (FCFA)</label>
                            <input type="number" name="montant_ttc" id="montant_ttc" class="form-control"
                                   readonly step="0.01" placeholder="0.00">
                            <small class="form-text text-muted">Calculé automatiquement</small>
                        </div>

                        <!-- Notes -->
                        <div class="col-12">
                            <label for="notes" class="form-label">Notes</label>
                            <textarea name="notes" id="notes" class="form-control @error('notes', 'is-invalid')"
                                      rows="3" placeholder="Notes sur le bon de commande...">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback d-block">
                                    <strong>{{ $message }}</strong>
                                </div>
                            @enderror
                        </div>

                        <!-- Conditions de livraison -->
                        <div class="col-12">
                            <label for="conditions_livraison" class="form-label">Conditions de livraison</label>
                            <textarea name="conditions_livraison" id="conditions_livraison" class="form-control @error('conditions_livraison', 'is-invalid')"
                                      rows="3" placeholder="Conditions de livraison...">{{ old('conditions_livraison') }}</textarea>
                            @error('conditions_livraison')
                                <div class="invalid-feedback d-block">
                                    <strong>{{ $message }}</strong>
                                </div>
                            @enderror
                        </div>

                        <!-- Boutons -->
                        <div class="col-12">
                            <div class="d-flex justify-content-between">
                                <a href="{{ route('commercial.bons-commande.index') }}" class="btn btn-secondary">
                                    <i class="fa-solid fa-arrow-left me-2"></i>
                                    Retour à la liste
                                </a>
                                <div>
                                    <button type="reset" class="btn btn-warning me-2">
                                        <i class="fa-solid fa-undo me-2"></i>
                                        Réinitialiser
                                    </button>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fa-solid fa-save me-2"></i>
                                        Enregistrer le bon
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-dashboard-layout>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const montantHt = document.getElementById('montant_ht');
    const tva = document.getElementById('tva');
    const montantTtc = document.getElementById('montant_ttc');

    function calculerTtc() {
        const ht = parseFloat(montantHt.value) || 0;
        const tauxTva = parseFloat(tva.value) || 0;
        const ttc = ht * (1 + tauxTva / 100);
        montantTtc.value = ttc.toFixed(2);
    }

    montantHt.addEventListener('input', calculerTtc);
    tva.addEventListener('input', calculerTtc);

    // Calcul initial
    calculerTtc();
});
</script>
@endpush

@extends('layouts.app')

@section('title', 'Nouveau Bon de Livraison | KENAM SERVICES')

@section('content')
<x-list-layout
    title="Nouveau Bon de Livraison"
    icon="fa-solid fa-truck"
    createRoute="commercial.bons-livraison.index"
    createText="Retour à la liste"
>
    <x-slot name="kpis">
        <x-kpi-card title="Total Bons" value="{{ DB::table('bons_livraison')->count() }}" icon="fa-solid fa-truck" color="blue" />
        <x-kpi-card title="En Préparation" value="{{ DB::table('bons_livraison')->where('statut', 'en_preparation')->count() }}" icon="fa-solid fa-box" color="yellow" />
        <x-kpi-card title="En Transit" value="{{ DB::table('bons_livraison')->where('statut', 'en_transit')->count() }}" icon="fa-solid fa-shipping-fast" color="orange" />
        <x-kpi-card title="Livrés" value="{{ DB::table('bons_livraison')->where('statut', 'livre')->count() }}" icon="fa-solid fa-check-circle" color="green" />
    </x-slot>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fa-solid fa-truck me-2"></i>
                        Nouveau Bon de Livraison
                    </h3>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('commercial.bons-livraison.store') }}" class="row g-3">
                        @csrf

                        <!-- Bon de commande associé -->
                        <div class="col-md-6">
                            <label for="bon_commande_id" class="form-label">Bon de commande <span class="text-danger">*</span></label>
                            <select name="bon_commande_id" id="bon_commande_id" class="form-select @error('bon_commande_id', 'is-invalid')" required>
                                <option value="">Sélectionner un bon de commande</option>
                                @foreach($bonsCommande as $bc)
                                    <option value="{{ $bc->id }}" {{ old('bon_commande_id') == $bc->id ? 'selected' : '' }}">
                                        {{ $bc->numero }} - {{ $bc->client_nom }}
                                    </option>
                                @endforeach
                            </select>
                            @error('bon_commande_id')
                                <div class="invalid-feedback d-block">
                                    <strong>{{ $message }}</strong>
                                </div>
                            @enderror
                        </div>

                        <!-- Numéro -->
                        <div class="col-md-6">
                            <label for="numero" class="form-label">Numéro <span class="text-danger">*</span></label>
                            <input type="text" name="numero" id="numero" class="form-control @error('numero', 'is-invalid')"
                                   value="{{ old('numero') }}" placeholder="Numéro du bon de livraison" required>
                            @error('numero')
                                <div class="invalid-feedback d-block">
                                    <strong>{{ $message }}</strong>
                                </div>
                            @enderror
                        </div>

                        <!-- Date de livraison -->
                        <div class="col-md-6">
                            <label for="date_livraison" class="form-label">Date de livraison <span class="text-danger">*</span></label>
                            <input type="date" name="date_livraison" id="date_livraison" class="form-control @error('date_livraison', 'is-invalid')"
                                   value="{{ old('date_livraison') }}" required>
                            @error('date_livraison')
                                <div class="invalid-feedback d-block">
                                    <strong>{{ $message }}</strong>
                                </div>
                            @enderror
                        </div>

                        <!-- Livreur -->
                        <div class="col-md-6">
                            <label for="livreur" class="form-label">Livreur</label>
                            <input type="text" name="livreur" id="livreur" class="form-control @error('livreur', 'is-invalid')"
                                   value="{{ old('livreur') }}" placeholder="Nom du livreur">
                            @error('livreur')
                                <div class="invalid-feedback d-block">
                                    <strong>{{ $message }}</strong>
                                </div>
                            @enderror
                        </div>

                        <!-- Adresse de livraison -->
                        <div class="col-12">
                            <label for="adresse_livraison" class="form-label">Adresse de livraison</label>
                            <textarea name="adresse_livraison" id="adresse_livraison" class="form-control @error('adresse_livraison', 'is-invalid')"
                                      rows="3" placeholder="Adresse complète de livraison">{{ old('adresse_livraison') }}</textarea>
                            @error('adresse_livraison')
                                <div class="invalid-feedback d-block">
                                    <strong>{{ $message }}</strong>
                                </div>
                            @enderror
                        </div>

                        <!-- Statut -->
                        <div class="col-md-6">
                            <label for="statut" class="form-label">Statut <span class="text-danger">*</span></label>
                            <select name="statut" id="statut" class="form-select @error('statut', 'is-invalid')" required>
                                <option value="en_preparation" {{ old('statut') == 'en_preparation' ? 'selected' : '' }}>En préparation</option>
                                <option value="en_transit" {{ old('statut') == 'en_transit' ? 'selected' : '' }}>En transit</option>
                                <option value="livre" {{ old('statut') == 'livre' ? 'selected' : '' }}>Livre</option>
                                <option value="annule" {{ old('statut') == 'annule' ? 'selected' : '' }}>Annulé</option>
                            </select>
                            @error('statut')
                                <div class="invalid-feedback d-block">
                                    <strong>{{ $message }}</strong>
                                </div>
                            @enderror
                        </div>

                        <!-- Notes -->
                        <div class="col-md-6">
                            <label for="notes" class="form-label">Notes</label>
                            <textarea name="notes" id="notes" class="form-control @error('notes', 'is-invalid')"
                                      rows="3" placeholder="Notes sur la livraison...">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback d-block">
                                    <strong>{{ $message }}</strong>
                                </div>
                            @enderror
                        </div>

                        <!-- Observations -->
                        <div class="col-12">
                            <label for="observations" class="form-label">Observations</label>
                            <textarea name="observations" id="observations" class="form-control @error('observations', 'is-invalid')"
                                      rows="3" placeholder="Observations sur la livraison...">{{ old('observations') }}</textarea>
                            @error('observations')
                                <div class="invalid-feedback d-block">
                                    <strong>{{ $message }}</strong>
                                </div>
                            @enderror
                        </div>

                        <!-- Boutons -->
                        <div class="col-12 d-flex justify-content-between mt-4">
                            <a href="{{ route('commercial.bons-livraison.index') }}" class="btn btn-secondary">
                                <i class="fa-solid fa-times me-2"></i>
                                Annuler
                            </a>
                            <button type="submit" class="btn btn-success">
                                <i class="fa-solid fa-save me-2"></i>
                                Créer le Bon de Livraison
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Script pour générer le numéro automatiquement -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const numeroInput = document.getElementById('numero');
            if (numeroInput && !numeroInput.value) {
                const currentDate = new Date().toISOString().split('T')[0];
                numeroInput.value = 'BL-' + currentDate.replace(/-/g, '') + '-' + Math.floor(Math.random() * 1000);
            }
        });
    </script>
</x-list-layout>
@endsection

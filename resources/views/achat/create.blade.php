@extends('layouts.app')

@section('title', 'Nouvel Achat')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Nouvel achat</h1>
        <a href="{{ route('achat.index') }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-2"></i>Retour</a>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <form action="{{ route('achat.store') }}" method="POST">
                @csrf
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Fournisseur *</label>
                        <select name="fournisseur_id" class="form-select" required>
                            <option value="">Sélectionner</option>
                            @foreach($fournisseurs as $fournisseur)
                                <option value="{{ $fournisseur->id }}" @selected(old('fournisseur_id') == $fournisseur->id)>{{ $fournisseur->raison_sociale }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Type d'achat *</label>
                        <input type="text" name="type_achat" class="form-control" value="{{ old('type_achat') }}" placeholder="Maintenance, prestation, fourniture..." required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Service concerné</label>
                        <input type="text" name="service_concerne" class="form-control" value="{{ old('service_concerne') }}" placeholder="Atelier, RH, Chantier...">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Référence</label>
                        <input type="text" name="reference" class="form-control" value="{{ old('reference') }}" placeholder="Laisser vide pour auto-génération">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Date d'achat *</label>
                        <input type="date" name="date_commande" class="form-control" value="{{ old('date_commande', now()->format('Y-m-d')) }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Date livraison prévue</label>
                        <input type="date" name="date_livraison_prevue" class="form-control" value="{{ old('date_livraison_prevue') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Compte comptable de charge *</label>
                        <select name="compte_comptable_id" class="form-select" required>
                            <option value="">Sélectionner</option>
                            @foreach($comptes as $compte)
                                <option value="{{ $compte->id }}" @selected(old('compte_comptable_id') == $compte->id)>
                                    {{ $compte->numero ?? $compte->numero_compte ?? $compte->code ?? $compte->id }} - {{ $compte->intitule ?? $compte->libelle ?? 'Compte comptable' }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Mode de paiement</label>
                        <select name="mode_paiement" class="form-select">
                            <option value="">Sélectionner</option>
                            <option value="virement" @selected(old('mode_paiement') === 'virement')>Virement</option>
                            <option value="cheque" @selected(old('mode_paiement') === 'cheque')>Chèque</option>
                            <option value="espece" @selected(old('mode_paiement') === 'espece')>Espèces</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Conditions paiement</label>
                        <input type="text" name="conditions_paiement" class="form-control" value="{{ old('conditions_paiement') }}" placeholder="Ex: 30 jours">
                    </div>
                </div>

                <hr class="my-4">

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0">Lignes d'achat</h5>
                    <button type="button" id="add-ligne" class="btn btn-sm btn-outline-primary"><i class="fas fa-plus me-1"></i>Ajouter</button>
                </div>

                <div id="lignes-container"></div>

                <div class="row g-3 mt-3">
                    <div class="col-md-4">
                        <label class="form-label">Frais de livraison</label>
                        <input type="number" step="0.01" min="0" name="frais_livraison" class="form-control" value="{{ old('frais_livraison', 0) }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Remise</label>
                        <input type="number" step="0.01" min="0" name="remise" class="form-control" value="{{ old('remise', 0) }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Type de remise</label>
                        <select name="type_remise" class="form-select">
                            <option value="pourcentage" @selected(old('type_remise', 'pourcentage') === 'pourcentage')>Pourcentage</option>
                            <option value="montant" @selected(old('type_remise') === 'montant')>Montant fixe</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Notes</label>
                        <textarea name="notes" class="form-control" rows="3">{{ old('notes') }}</textarea>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 mt-4">
                    <a href="{{ route('achat.index') }}" class="btn btn-light">Annuler</a>
                    <button type="submit" class="btn btn-primary">Enregistrer l'achat</button>
                </div>
            </form>
        </div>
    </div>
</div>

<template id="ligne-template">
    <div class="border rounded p-3 mb-3 ligne-achat">
        <div class="row g-2">
            <div class="col-md-3">
                <label class="form-label">Article</label>
                <select name="lignes[__INDEX__][article_id]" class="form-select article-select">
                    <option value="">Sélectionner</option>
                    @foreach($articles as $article)
                        <option value="{{ $article->id }}" data-designation="{{ $article->designation }}" data-prix="{{ $article->prix_unitaire ?? 0 }}" data-unite="{{ $article->unite ?? 'Unité' }}">{{ $article->designation }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">Désignation *</label>
                <input type="text" name="lignes[__INDEX__][designation]" class="form-control designation" required>
            </div>
            <div class="col-md-2">
                <label class="form-label">Quantité *</label>
                <input type="number" step="0.001" min="0.001" name="lignes[__INDEX__][quantite]" class="form-control" value="1" required>
            </div>
            <div class="col-md-2">
                <label class="form-label">Unité *</label>
                <input type="text" name="lignes[__INDEX__][unite]" class="form-control unite" value="Unité" required>
            </div>
            <div class="col-md-1 d-flex align-items-end">
                <button type="button" class="btn btn-outline-danger remove-ligne"><i class="fas fa-trash"></i></button>
            </div>
            <div class="col-md-3">
                <label class="form-label">Prix unitaire HT *</label>
                <input type="number" step="0.01" min="0" name="lignes[__INDEX__][prix_unitaire_ht]" class="form-control prix-unitaire" required>
            </div>
            <div class="col-md-2">
                <label class="form-label">TVA (%) *</label>
                <input type="number" step="0.01" min="0" max="100" name="lignes[__INDEX__][tva_taux]" class="form-control" value="18" required>
            </div>
            <div class="col-md-2">
                <label class="form-label">Remise (%)</label>
                <input type="number" step="0.01" min="0" max="100" name="lignes[__INDEX__][remise]" class="form-control" value="0">
            </div>
            <div class="col-md-5">
                <label class="form-label">Description</label>
                <input type="text" name="lignes[__INDEX__][description]" class="form-control">
            </div>
        </div>
    </div>
</template>

@push('scripts')
<script>
let achatLineIndex = 0;

function addAchatLine() {
    const template = document.getElementById('ligne-template').innerHTML.replaceAll('__INDEX__', achatLineIndex++);
    document.getElementById('lignes-container').insertAdjacentHTML('beforeend', template);
}

document.addEventListener('DOMContentLoaded', function () {
    addAchatLine();

    document.getElementById('add-ligne').addEventListener('click', addAchatLine);

    document.addEventListener('click', function (event) {
        if (event.target.closest('.remove-ligne')) {
            event.target.closest('.ligne-achat').remove();
        }
    });

    document.addEventListener('change', function (event) {
        if (!event.target.classList.contains('article-select')) {
            return;
        }

        const option = event.target.options[event.target.selectedIndex];
        const line = event.target.closest('.ligne-achat');
        line.querySelector('.designation').value = option.dataset.designation || '';
        line.querySelector('.prix-unitaire').value = option.dataset.prix || '';
        line.querySelector('.unite').value = option.dataset.unite || 'Unité';
    });
});
</script>
@endpush
@endsection

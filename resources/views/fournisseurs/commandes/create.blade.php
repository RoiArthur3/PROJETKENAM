@extends('layouts.app')

@section('title', 'Nouvelle Commande Fournisseur - KENAM SERVICES')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col">
            <h1 class="h3 mb-0 text-gray-800">Nouvelle Commande Fournisseur</h1>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card shadow">
                <div class="card-header py-3 bg-gradient-success text-white">
                    <h6 class="m-0 font-weight-bold">Informations de la Commande</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('fournisseurs.commandes.store') }}" method="POST">
                        @csrf

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="fournisseur_id" class="form-label">Fournisseur *</label>
                                <select name="fournisseur_id" id="fournisseur_id" class="form-select @error('fournisseur_id') is-invalid @enderror" required>
                                    <option value="">Sélectionner un fournisseur</option>
                                    @foreach($fournisseurs as $fournisseur)
                                        <option value="{{ $fournisseur->id }}" {{ old('fournisseur_id') == $fournisseur->id ? 'selected' : '' }}>
                                            {{ $fournisseur->raison_sociale }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('fournisseur_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="reference" class="form-label">Référence commande</label>
                                <input type="text" name="reference" id="reference" class="form-control @error('reference') is-invalid @enderror"
                                       value="{{ old('reference') }}" placeholder="Ex: CMD-2025-001">
                                <div class="form-text">Laisser vide pour générer automatiquement</div>
                                @error('reference')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="date_commande" class="form-label">Date de commande *</label>
                                <input type="date" name="date_commande" id="date_commande" class="form-control @error('date_commande') is-invalid @enderror"
                                       value="{{ old('date_commande', now()->format('Y-m-d')) }}" required>
                                @error('date_commande')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="date_livraison_prevue" class="form-label">Date de livraison prévue</label>
                                <input type="date" name="date_livraison_prevue" id="date_livraison_prevue" class="form-control @error('date_livraison_prevue') is-invalid @enderror"
                                       value="{{ old('date_livraison_prevue') }}">
                                @error('date_livraison_prevue')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="mode_paiement" class="form-label">Mode de paiement</label>
                                <select name="mode_paiement" id="mode_paiement" class="form-select @error('mode_paiement') is-invalid @enderror">
                                    <option value="">Sélectionner</option>
                                    <option value="virement" {{ old('mode_paiement') == 'virement' ? 'selected' : '' }}>Virement bancaire</option>
                                    <option value="cheque" {{ old('mode_paiement') == 'cheque' ? 'selected' : '' }}>Chèque</option>
                                    <option value="espece" {{ old('mode_paiement') == 'espece' ? 'selected' : '' }}>Espèces</option>
                                    <option value="traite" {{ old('mode_paiement') == 'traite' ? 'selected' : '' }}>Traite</option>
                                </select>
                                @error('mode_paiement')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="conditions_paiement" class="form-label">Conditions de paiement</label>
                                <input type="text" name="conditions_paiement" id="conditions_paiement" class="form-control @error('conditions_paiement') is-invalid @enderror"
                                       value="{{ old('conditions_paiement') }}" placeholder="Ex: 30 jours">
                                @error('conditions_paiement')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Articles -->
                        <div class="row">
                            <div class="col-12 mb-3">
                                <h5 class="text-success mb-3">
                                    <i class="fas fa-list me-2"></i>Articles de la commande
                                </h5>
                                <div id="lignes-container">
                                    <!-- Lignes d'articles ajoutées dynamiquement -->
                                </div>
                                <button type="button" id="add-ligne" class="btn btn-outline-success btn-sm">
                                    <i class="fas fa-plus me-1"></i>Ajouter une ligne
                                </button>
                            </div>
                        </div>

                        <!-- Frais et remise -->
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="frais_livraison" class="form-label">Frais de livraison</label>
                                <input type="number" name="frais_livraison" id="frais_livraison" class="form-control @error('frais_livraison') is-invalid @enderror"
                                       value="{{ old('frais_livraison', 0) }}" step="0.01" min="0" placeholder="0.00">
                                @error('frais_livraison')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="remise" class="form-label">Remise</label>
                                <input type="number" name="remise" id="remise" class="form-control @error('remise') is-invalid @enderror"
                                       value="{{ old('remise', 0) }}" step="0.01" min="0" placeholder="0.00">
                                @error('remise')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="type_remise" class="form-label">Type de remise</label>
                                <select name="type_remise" id="type_remise" class="form-select @error('type_remise') is-invalid @enderror">
                                    <option value="pourcentage" {{ old('type_remise', 'pourcentage') == 'pourcentage' ? 'selected' : '' }}>Pourcentage (%)</option>
                                    <option value="montant" {{ old('type_remise') == 'montant' ? 'selected' : '' }}>Montant fixe</option>
                                </select>
                                @error('type_remise')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Notes -->
                        <div class="row">
                            <div class="col-12 mb-3">
                                <label for="notes" class="form-label">Notes</label>
                                <textarea name="notes" id="notes" class="form-control @error('notes') is-invalid @enderror"
                                          rows="3" placeholder="Notes sur la commande...">{{ old('notes') }}</textarea>
                                @error('notes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Boutons -->
                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex justify-content-between">
                                    <a href="{{ route('fournisseurs.commandes.index') }}" class="btn btn-secondary">
                                        <i class="fas fa-arrow-left me-2"></i>Annuler
                                    </a>
                                    <div>
                                        <button type="reset" class="btn btn-warning me-2">
                                            <i class="fas fa-undo me-2"></i>Réinitialiser
                                        </button>
                                        <button type="submit" class="btn btn-success" id="submitBtn">
                                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true" id="spinner" style="display:none;"></span>
                                            <i class="fas fa-save me-2"></i>Enregistrer la commande
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

<!-- Template pour une ligne d'article -->
<template id="ligne-template">
    <div class="border rounded p-3 mb-3 bg-light ligne-article">
        <div class="row">
            <div class="col-md-4 mb-2">
                <label class="form-label">Article</label>
                <select name="lignes[{{INDEX}}][article_id]" class="form-select article-select">
                    <option value="">Sélectionner un article</option>
                    @foreach($articles as $article)
                        <option value="{{ $article->id }}" data-prix="{{ $article->prix_unitaire ?? 0 }}" data-unite="{{ $article->unite ?? 'Unité' }}">
                            {{ $article->designation }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 mb-2">
                <label class="form-label">Quantité</label>
                <input type="number" name="lignes[{{INDEX}}][quantite]" class="form-control quantite" step="0.001" min="0.001" value="1" required>
            </div>
            <div class="col-md-2 mb-2">
                <label class="form-label">Prix unitaire HT</label>
                <input type="number" name="lignes[{{INDEX}}][prix_unitaire_ht]" class="form-control prix-unitaire" step="0.01" min="0" required>
            </div>
            <div class="col-md-2 mb-2">
                <label class="form-label">TVA (%)</label>
                <select name="lignes[{{INDEX}}][tva_taux]" class="form-select tva-taux">
                    <option value="0">Exonéré (0%)</option>
                    <option value="18" selected>TVA Côte d'Ivoire (18%)</option>
                    <option value="19">TVA Standard (19%)</option>
                    <option value="20">TVA Réduite (20%)</option>
                </select>
            </div>
            <div class="col-md-2 mb-2">
                <label class="form-label">&nbsp;</label><br>
                <button type="button" class="btn btn-danger btn-sm remove-ligne">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>
        <div class="row">
            <div class="col-md-8 mb-2">
                <label class="form-label">Désignation</label>
                <input type="text" name="lignes[{{INDEX}}][designation]" class="form-control designation" required>
            </div>
            <div class="col-md-4 mb-2">
                <label class="form-label">Unité</label>
                <input type="text" name="lignes[{{INDEX}}][unite]" class="form-control unite" value="Unité">
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <label class="form-label">Description</label>
                <textarea name="lignes[{{INDEX}}][description]" class="form-control" rows="2"></textarea>
            </div>
        </div>
    </div>
</template>

<script>
let ligneIndex = 0;

document.addEventListener('DOMContentLoaded', function() {
    // Ajouter une première ligne par défaut
    addLigne();

    // Bouton ajouter ligne
    document.getElementById('add-ligne').addEventListener('click', addLigne);

    // Gestionnaire pour suppression
    document.addEventListener('click', function(e) {
        if (e.target.closest('.remove-ligne')) {
            e.target.closest('.ligne-article').remove();
        }
    });

    // Auto-remplir designation/prix/unité lors de la sélection d'un article
    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('article-select')) {
            const option = e.target.options[e.target.selectedIndex];
            const ligne = e.target.closest('.ligne-article');

            if (option && option.value) {
                ligne.querySelector('.designation').value = option.text;
                ligne.querySelector('.prix-unitaire').value = option.dataset.prix || 0;
                ligne.querySelector('.unite').value = option.dataset.unite || 'Unité';
            }
        }
    });

    // Validation avant soumission
    document.querySelector('form').addEventListener('submit', function(e) {
        const lignes = document.querySelectorAll('.ligne-article');
        if (lignes.length === 0) {
            e.preventDefault();
            alert('Veuillez ajouter au moins un article à la commande.');
            return;
        }

        // Vérifier que chaque ligne a une designation
        let valid = true;
        lignes.forEach(ligne => {
            const designation = ligne.querySelector('.designation').value;
            const quantite = ligne.querySelector('.quantite').value;
            const prix = ligne.querySelector('.prix-unitaire').value;

            if (!designation || !quantite || !prix) {
                valid = false;
            }
        });

        if (!valid) {
            e.preventDefault();
            alert('Veuillez remplir tous les champs obligatoires pour chaque article.');
            return;
        }

        // Afficher le spinner
        document.getElementById('spinner').style.display = 'inline-block';
        document.getElementById('submitBtn').disabled = true;
    });
});

function addLigne() {
    const container = document.getElementById('lignes-container');
    const template = document.getElementById('ligne-template').innerHTML;
    const newLigne = template.replace(/{{INDEX}}/g, ligneIndex);

    const tempDiv = document.createElement('div');
    tempDiv.innerHTML = newLigne;
    container.appendChild(tempDiv.firstElementChild);

    ligneIndex++;
}
</script>
@endsection

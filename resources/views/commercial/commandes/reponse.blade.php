@extends('layouts.app')

@section('title', 'Réponse Logistique | KENAM SERVICES')

@section('content')
<x-list-layout
    title="Réponse logistique"
    icon="fa-solid fa-reply"
    createRoute="commercial.commandes.show"
    createText="Retour"
>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fa-solid fa-reply me-2"></i>
                        {{ $commande->reference }}
                    </h3>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <strong>Demande :</strong>
                        {{ $commande->type_engin }} (Qté {{ $commande->quantite }})
                        @if($commande->lieu)
                            - {{ $commande->lieu }}
                        @endif
                    </div>

                    <form method="POST" action="{{ route('commercial.commandes.reponse.store', $commande->id) }}" class="row g-3" id="reponseForm">
                        @csrf

                        <div class="col-12">
                            <label for="commentaire" class="form-label">Commentaire</label>
                            <textarea name="commentaire" id="commentaire" class="form-control @error('commentaire') is-invalid @enderror" rows="3">{{ old('commentaire') }}</textarea>
                            @error('commentaire')
                                <div class="invalid-feedback d-block"><strong>{{ $message }}</strong></div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">Fournisseurs proposés</h5>
                                <button type="button" class="btn btn-outline-primary btn-sm" id="addFournisseurBtn">
                                    <i class="fa-solid fa-plus me-1"></i>
                                    Ajouter
                                </button>
                            </div>
                        </div>

                        <div class="col-12">
                            @error('fournisseurs')
                                <div class="alert alert-danger"><strong>{{ $message }}</strong></div>
                            @enderror

                            <div class="table-responsive">
                                <table class="table table-sm" id="fournisseursTable">
                                    <thead>
                                        <tr>
                                            <th>Fournisseur *</th>
                                            <th>Engin</th>
                                            <th>Prix</th>
                                            <th>Devise</th>
                                            <th>Détails</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="d-flex justify-content-between">
                                <a href="{{ route('commercial.commandes.show', $commande->id) }}" class="btn btn-secondary">
                                    <i class="fa-solid fa-arrow-left me-2"></i>
                                    Retour
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa-solid fa-save me-2"></i>
                                    Enregistrer la réponse
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-list-layout>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const tbody = document.querySelector('#fournisseursTable tbody');
    const addBtn = document.getElementById('addFournisseurBtn');

    function newRow(index) {
        const tr = document.createElement('tr');
        tr.innerHTML = `
            <td>
                <input type="text" name="fournisseurs[${index}][fournisseur_nom]" class="form-control" required>
            </td>
            <td>
                <input type="text" name="fournisseurs[${index}][engin_disponible]" class="form-control">
            </td>
            <td>
                <input type="number" step="0.01" min="0" name="fournisseurs[${index}][prix]" class="form-control">
            </td>
            <td>
                <input type="text" name="fournisseurs[${index}][devise]" class="form-control" placeholder="FCFA">
            </td>
            <td>
                <input type="text" name="fournisseurs[${index}][details]" class="form-control">
            </td>
            <td>
                <button type="button" class="btn btn-outline-danger btn-sm remove-row">
                    <i class="fa-solid fa-trash"></i>
                </button>
            </td>
        `;
        tr.querySelector('.remove-row').addEventListener('click', function () {
            tr.remove();
            renumber();
        });
        return tr;
    }

    function renumber() {
        const rows = Array.from(tbody.querySelectorAll('tr'));
        rows.forEach((row, idx) => {
            row.querySelectorAll('input').forEach((input) => {
                input.name = input.name.replace(/fournisseurs\[\d+\]/, `fournisseurs[${idx}]`);
            });
        });
    }

    addBtn.addEventListener('click', function () {
        const index = tbody.querySelectorAll('tr').length;
        tbody.appendChild(newRow(index));
    });

    tbody.appendChild(newRow(0));
});
</script>
@endpush

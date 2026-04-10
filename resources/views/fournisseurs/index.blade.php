@extends('layouts.app')

@section('title', 'Fournisseurs - Liste | KENAM SERVICES')

@section('content')
<!-- Messages Flash -->
@if(session('success'))
<div class="alert alert-success alert-dismissible fade show" role="alert">
    <i class="fas fa-check-circle me-2"></i>
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

@if(session('error'))
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <i class="fas fa-exclamation-triangle me-2"></i>
    {{ session('error') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

<x-list-layout
    title="Liste des Fournisseurs"
    icon="fa-industry"
    createRoute="fournisseurs.create"
    createLabel="Nouveau Fournisseur"
    :exportable="true"
    searchPlaceholder="Nom, email, téléphone, catégorie..."
    :count="15"
>
    <!-- KPIs -->
    <x-slot name="kpis">
        <x-kpi-card
            title="Total Fournisseurs"
            :value="$suppliersTotal ?? 0"
            icon="fa-industry"
            color="primary"
            :subtitle="'Dont ' . ($suppliersActive ?? 0) . ' actifs'"
        />
        <x-kpi-card
            title="Fournisseurs Actifs"
            :value="$suppliersActive ?? 0"
            icon="fa-check-circle"
            color="success"
            :subtitle="$suppliersTotal && $suppliersTotal > 0 ? round(($suppliersActive / $suppliersTotal) * 100) . '% d\'activité' : 'Aucune donnée'"
        />
        <x-kpi-card
            title="Catégories"
            :value="$categoriesCount ?? 0"
            icon="fa-tags"
            color="info"
            :subtitle="$lastCategoryAdded ? 'Dernière : ' . $lastCategoryAdded : 'Aucune catégorie'"
        />
    </x-slot>

    <!-- Filtres supplémentaires -->
    <x-slot name="filters">
        <div class="col-md-2">
            <label class="form-label">Catégorie</label>
            <select name="categorie_id" class="form-select">
                <option value="">Toutes</option>
                @foreach(($categories ?? []) as $categorie)
                    <option value="{{ $categorie->id }}" @selected(request('categorie_id') == $categorie->id)>
                        {{ $categorie->nom }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label">Statut</label>
            <select name="statut" class="form-select">
                <option value="">Tous</option>
                <option value="actif" @selected(request('statut') === 'actif')>Actif</option>
                <option value="inactif" @selected(request('statut') === 'inactif')>Inactif</option>
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label">Évaluation</label>
            <select name="evaluation" class="form-select">
                <option value="">Toutes</option>
                <option>Excellente (4.5-5)</option>
                <option>Très bien (4-4.4)</option>
                <option>Bien (3.5-3.9)</option>
                <option>Moyenne (3-3.4)</option>
                <option>À améliorer (< 3)</option>
            </select>
        </div>
    </x-slot>

    <!-- Tableau -->
    <thead class="table-light">
        <tr>
            <th>Fournisseur</th>
            <th>Contact</th>
            <th>Catégorie</th>
            <th>Évaluation</th>
            <th>Dernière Commande</th>
            <th>Montant Total</th>
            <th>Statut</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @forelse(($fournisseurs ?? []) as $fournisseur)
        <tr>
            <td>
                <div class="d-flex align-items-center">
                    <div class="avatar-circle bg-primary text-white me-3">
                        {{ strtoupper(substr($fournisseur->raison_sociale, 0, 2)) }}
                    </div>
                    <div>
                        <div class="fw-bold">{{ $fournisseur->raison_sociale }}</div>
                        <div class="text-muted small">{{ $fournisseur->categorie->nom ?? 'Sans catégorie' }}</div>
                    </div>
                </div>
            </td>
            <td>
                <div>
                    @if($fournisseur->email)
                        <div><i class="fas fa-envelope text-muted me-1"></i>{{ $fournisseur->email }}</div>
                    @endif
                    @if($fournisseur->telephone)
                        <div><i class="fas fa-phone text-muted me-1"></i>{{ $fournisseur->telephone }}</div>
                    @endif
                </div>
            </td>
            <td>
                <span class="badge bg-secondary">{{ $fournisseur->categorie->nom ?? 'N/A' }}</span>
            </td>
            <td>
                <span class="text-muted small">N/A</span>
            </td>
            <td>
                <span class="text-muted small">-</span>
            </td>
            <td>
                <span class="text-muted small">-</span>
            </td>
            <td>
                <span class="badge bg-{{ $fournisseur->est_actif ? 'success' : 'secondary' }}">
                    {{ $fournisseur->est_actif ? 'Actif' : 'Inactif' }}
                </span>
            </td>
            <td>
                <div class="btn-group btn-group-sm">
                    <a href="{{ route('fournisseurs.show', $fournisseur) }}" class="btn btn-outline-primary" title="Voir">
                        <i class="fas fa-eye"></i>
                    </a>
                    <a href="{{ route('fournisseurs.edit', $fournisseur) }}" class="btn btn-outline-warning" title="Modifier">
                        <i class="fas fa-edit"></i>
                    </a>
                    <form action="{{ route('fournisseurs.destroy', $fournisseur) }}" method="POST" onsubmit="return confirm('Supprimer ce fournisseur ? Cette action est irréversible.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger" title="Supprimer">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </div>
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="8" class="text-center text-muted py-4">Aucun fournisseur trouvé.</td>
        </tr>
        @endforelse
    </tbody>

    <!-- Pagination -->
    <x-slot name="pagination">
        @if(isset($fournisseurs))
            {{ $fournisseurs->links() }}
        @endif
    </x-slot>
</x-list-layout>

<style>
.avatar-circle {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 14px;
}
</style>

<script>
function voirCommandes(id) {
    alert('Historique des commandes du fournisseur ' + id + ' - Fonctionnalité en développement');
}

function demanderAudit(id) {
    alert('Demande d\'audit pour le fournisseur ' + id + ' - Fonctionnalité en développement');
}
</script>
@endsection

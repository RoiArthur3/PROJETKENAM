@extends('layouts.app')

@section('title', 'Bons de Livraison | KENAM SERVICES')

@section('content')
<x-list-layout
    title="Bons de Livraison"
    icon="fa-solid fa-truck"
    createRoute="commercial.bons-livraison.create"
    createText="Nouveau Bon"
>
    <x-slot name="kpis">
        <x-kpi-card title="Total Bons" value="{{ DB::table('bons_livraison')->count() }}" icon="fa-solid fa-truck" color="blue" />
        <x-kpi-card title="En Préparation" value="{{ DB::table('bons_livraison')->where('statut', 'en_preparation')->count() }}" icon="fa-solid fa-box" color="yellow" />
        <x-kpi-card title="En Transit" value="{{ DB::table('bons_livraison')->where('statut', 'en_transit')->count() }}" icon="fa-solid fa-shipping-fast" color="orange" />
        <x-kpi-card title="Livrés" value="{{ DB::table('bons_livraison')->where('statut', 'livre')->count() }}" icon="fa-solid fa-check-circle" color="green" />
    </x-slot>
    <x-slot name="filters">
        <form method="GET" action="{{ route('commercial.bons-livraison.index') }}" class="row g-3">
            <div class="col-md-3">
                <label for="search" class="form-label">Recherche</label>
                <input type="text" name="search" id="search" class="form-control"
                       value="{{ request('search') }}" placeholder="Numéro ou client...">
            </div>
            <div class="col-md-2">
                <label for="statut" class="form-label">Statut</label>
                <select name="statut" id="statut" class="form-select">
                    <option value="">Tous</option>
                    <option value="en_preparation" {{ request('statut') == 'en_preparation' ? 'selected' : '' }}>En préparation</option>
                    <option value="en_transit" {{ request('statut') == 'en_transit' ? 'selected' : '' }}>En transit</option>
                    <option value="livre" {{ request('statut') == 'livre' ? 'selected' : '' }}>Livre</option>
                    <option value="annule" {{ request('statut') == 'annule' ? 'selected' : '' }}>Annulé</option>
                </select>
            </div>
            <div class="col-md-2">
                <label for="date_debut" class="form-label">Date début</label>
                <input type="date" name="date_debut" id="date_debut" class="form-control"
                       value="{{ request('date_debut') }}">
            </div>
            <div class="col-md-2">
                <label for="date_fin" class="form-label">Date fin</label>
                <input type="date" name="date_fin" id="date_fin" class="form-control"
                       value="{{ request('date_fin') }}">
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <button type="submit" class="btn btn-outline-primary me-2">
                    <i class="fa-solid fa-search me-1"></i>
                    Filtrer
                </button>
                <a href="{{ route('commercial.bons-livraison.index') }}" class="btn btn-outline-secondary">
                    <i class="fa-solid fa-undo me-1"></i>
                    Réinitialiser
                </a>
            </div>
        </form>
    </x-slot>

    @if($bonsLivraison->count() > 0)
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>Numéro</th>
                        <th>Bon de Commande</th>
                        <th>Client</th>
                        <th>Date livraison</th>
                        <th>Livreur</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($bonsLivraison as $bon)
                        <tr>
                            <td>
                                <span class="badge bg-primary">{{ $bon->numero }}</span>
                            </td>
                            <td>
                                <span class="badge bg-info">{{ $bon->bon_commande_numero }}</span>
                            </td>
                            <td>{{ $bon->client_nom }}</td>
                            <td>{{ \Carbon\Carbon::parse($bon->date_livraison)->format('d/m/Y') }}</td>
                            <td>{{ $bon->livreur ?? '-' }}</td>
                            <td>
                                @switch($bon->statut)
                                    @case('en_preparation')
                                        <span class="badge bg-warning">En préparation</span>
                                    @break
                                    @case('en_transit')
                                        <span class="badge bg-info">En transit</span>
                                    @break
                                    @case('livre')
                                        <span class="badge bg-success">Livre</span>
                                    @break
                                    @case('annule')
                                        <span class="badge bg-danger">Annulé</span>
                                    @break
                                @endswitch
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('commercial.bons-livraison.show', $bon->id) }}"
                                       class="btn btn-outline-primary btn-sm" title="Voir">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                    <a href="{{ route('commercial.bons-livraison.edit', $bon->id) }}"
                                       class="btn btn-outline-warning btn-sm" title="Modifier">
                                        <i class="fa-solid fa-edit"></i>
                                    </a>
                                    <form method="POST" action="{{ route('commercial.bons-livraison.destroy', $bon->id) }}"
                                          class="d-inline" onsubmit="return confirm('Supprimer ce bon de livraison ?')">
                                        @csrf
                                        <button type="submit" class="btn btn-outline-danger btn-sm" title="Supprimer">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-center mt-4">
            {{ $bonsLivraison->links() }}
        </div>
    @else
        <div class="text-center py-5">
            <i class="fa-solid fa-truck fa-3x text-muted mb-3"></i>
            <h5 class="text-muted">Aucun bon de livraison trouvé</h5>
            <p class="text-muted">
                Commencez par créer votre premier bon de livraison.
            </p>
            <a href="{{ route('commercial.bons-livraison.create') }}" class="btn btn-primary">
                <i class="fa-solid fa-plus me-2"></i>
                Créer un Bon de Livraison
            </a>
        </div>
    @endif
</x-list-layout>
@endsection

@extends('layouts.app')

@section('title', 'Commandes | KENAM SERVICES')

@section('content')
<x-list-layout
    title="Commandes (Demande d'engins)"
    icon="fa-solid fa-clipboard-list"
    createRoute="commercial.commandes.create"
    createText="Nouvelle demande"
>
    <x-slot name="filters">
        <form method="GET" action="{{ route('commercial.commandes.index') }}" class="row g-3">
            <div class="col-md-4">
                <label for="search" class="form-label">Recherche</label>
                <input type="text" name="search" id="search" class="form-control" value="{{ request('search') }}" placeholder="Référence, engin, email...">
            </div>
            <div class="col-md-3">
                <label for="statut" class="form-label">Statut</label>
                <select name="statut" id="statut" class="form-select">
                    <option value="">Tous</option>
                    <option value="brouillon" {{ request('statut') == 'brouillon' ? 'selected' : '' }}>Brouillon</option>
                    <option value="envoye_logistique" {{ request('statut') == 'envoye_logistique' ? 'selected' : '' }}>Envoyé logistique</option>
                    <option value="repondu" {{ request('statut') == 'repondu' ? 'selected' : '' }}>Répondu</option>
                    <option value="valide" {{ request('statut') == 'valide' ? 'selected' : '' }}>Validé</option>
                    <option value="refuse" {{ request('statut') == 'refuse' ? 'selected' : '' }}>Refusé</option>
                </select>
            </div>
            <div class="col-md-2">
                <label for="date_debut" class="form-label">Date début</label>
                <input type="date" name="date_debut" id="date_debut" class="form-control" value="{{ request('date_debut') }}">
            </div>
            <div class="col-md-2">
                <label for="date_fin" class="form-label">Date fin</label>
                <input type="date" name="date_fin" id="date_fin" class="form-control" value="{{ request('date_fin') }}">
            </div>
            <div class="col-md-1 d-flex align-items-end">
                <button type="submit" class="btn btn-outline-primary w-100">
                    <i class="fa-solid fa-search"></i>
                </button>
            </div>
        </form>
    </x-slot>

    @if($commandes->count() > 0)
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>Référence</th>
                        <th>Type engin</th>
                        <th>Délai</th>
                        <th>Email logistique</th>
                        <th>Statut</th>
                        <th>Créée le</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($commandes as $commande)
                        <tr>
                            <td><span class="badge bg-primary">{{ $commande->reference }}</span></td>
                            <td>{{ $commande->type_engin }}</td>
                            <td>{{ $commande->date_fin ? $commande->date_fin->format('d/m/Y') : '-' }}</td>
                            <td>{{ $commande->email_service }}</td>
                            <td>
                                @switch($commande->statut)
                                    @case('brouillon')
                                        <span class="badge bg-secondary">Brouillon</span>
                                    @break
                                    @case('envoye_logistique')
                                        <span class="badge bg-info">Envoyé logistique</span>
                                    @break
                                    @case('repondu')
                                        <span class="badge bg-warning">Répondu</span>
                                    @break
                                    @case('valide')
                                        <span class="badge bg-success">Validé</span>
                                    @break
                                    @case('refuse')
                                        <span class="badge bg-danger">Refusé</span>
                                    @break
                                    @default
                                        <span class="badge bg-light text-dark">{{ $commande->statut }}</span>
                                @endswitch
                            </td>
                            <td>{{ $commande->created_at ? $commande->created_at->format('d/m/Y') : '-' }}</td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('commercial.commandes.show', $commande->id) }}" class="btn btn-outline-primary" title="Voir">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                    <a href="{{ route('commercial.commandes.edit', $commande->id) }}" class="btn btn-outline-warning" title="Modifier">
                                        <i class="fa-solid fa-edit"></i>
                                    </a>
                                    <form method="POST" action="{{ route('commercial.commandes.destroy', $commande->id) }}" class="d-inline" onsubmit="return confirm('Supprimer cette commande ?')">
                                        @csrf
                                        <button type="submit" class="btn btn-outline-danger" title="Supprimer">
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

        <div class="d-flex justify-content-center mt-4">
            {{ $commandes->links() }}
        </div>
    @else
        <div class="text-center py-5">
            <i class="fa-solid fa-clipboard-list fa-3x text-muted mb-3"></i>
            <h5 class="text-muted">Aucune demande trouvée</h5>
            <a href="{{ route('commercial.commandes.create') }}" class="btn btn-primary">
                <i class="fa-solid fa-plus me-2"></i>
                Créer une demande
            </a>
        </div>
    @endif
</x-list-layout>
@endsection

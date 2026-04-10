@extends('layouts.app')

@section('title', 'Entrées - Module Magasin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-success text-white">
                    <h4 class="mb-0">
                        <i class="fas fa-arrow-down me-2"></i>
                        Entrées - Module Magasin
                    </h4>
                </div>
                <div class="card-body">
                    <!-- Actions rapides -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="btn-group" role="group">
                                <a href="{{ route('magasin.dashboard') }}" class="btn btn-outline-primary">
                                    <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                                </a>
                                <a href="{{ route('magasin.inventaire') }}" class="btn btn-outline-info">
                                    <i class="fas fa-list me-2"></i>Inventaire
                                </a>
                                <a href="{{ route('magasin.entrees.create') }}" class="btn btn-success">
                                    <i class="fas fa-plus me-2"></i>Nouvelle Entrée
                                </a>
                                <a href="{{ route('magasin.sorties') }}" class="btn btn-outline-warning">
                                    <i class="fas fa-arrow-up me-2"></i>Sorties
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Tableau des entrées -->
                    <div class="row">
                        <div class="col-12">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>ID</th>
                                            <th>Produit</th>
                                            <th>Quantité</th>
                                            <th>Utilisateur</th>
                                            <th>Date</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if(isset($entrees) && $entrees->count() > 0)
                                            @foreach($entrees as $entree)
                                                <tr>
                                                    <td>{{ $entree->id ?? 'N/A' }}</td>
                                                    <td>{{ $entree->produit_nom ?? 'N/A' }}</td>
                                                    <td>
                                                        <span class="badge bg-success">
                                                            {{ $entree->quantite ?? 0 }}
                                                        </span>
                                                    </td>
                                                    <td>{{ $entree->user_name ?? 'N/A' }}</td>
                                                    <td>{{ isset($entree->created_at) && is_object($entree->created_at) ? $entree->created_at->format('d/m/Y H:i') : 'N/A' }}</td>
                                                    <td>
                                                        <div class="btn-group btn-group-sm" role="group">
                                                            <button class="btn btn-outline-primary" title="Voir">
                                                                <i class="fas fa-eye"></i>
                                                            </button>
                                                            <button class="btn btn-outline-warning" title="Modifier">
                                                                <i class="fas fa-edit"></i>
                                                            </button>
                                                            <button class="btn btn-outline-danger" title="Supprimer">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td colspan="6" class="text-center text-muted py-4">
                                                    <i class="fas fa-inbox fa-3x mb-3"></i>
                                                    <p>Aucune entrée enregistrée</p>
                                                </td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination -->
                            @if(isset($entrees) && method_exists($entrees, 'links'))
                                {{ $entrees->links() }}
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

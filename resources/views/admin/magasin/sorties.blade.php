@extends('layouts.app')

@section('title', 'Sorties - Module Magasin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-warning text-white">
                    <h4 class="mb-0">
                        <i class="fas fa-arrow-up me-2"></i>
                        Sorties - Module Magasin
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
                                <a href="{{ route('magasin.entrees') }}" class="btn btn-outline-success">
                                    <i class="fas fa-arrow-down me-2"></i>Entrées
                                </a>
                                <a href="{{ route('magasin.sorties.create') }}" class="btn btn-warning">
                                    <i class="fas fa-plus me-2"></i>Nouvelle Sortie
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Tableau des sorties -->
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
                                        @if(isset($sorties) && $sorties->count() > 0)
                                            @foreach($sorties as $sortie)
                                                <tr>
                                                    <td>{{ $sortie->id ?? 'N/A' }}</td>
                                                    <td>{{ $sortie->produit_nom ?? 'N/A' }}</td>
                                                    <td>
                                                        <span class="badge bg-warning">
                                                            {{ $sortie->quantite ?? 0 }}
                                                        </span>
                                                    </td>
                                                    <td>{{ $sortie->user_name ?? 'N/A' }}</td>
                                                    <td>{{ isset($sortie->created_at) && is_object($sortie->created_at) ? $sortie->created_at->format('d/m/Y H:i') : 'N/A' }}</td>
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
                                                    <i class="fas fa-box-open fa-3x mb-3"></i>
                                                    <p>Aucune sortie enregistrée</p>
                                                </td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination -->
                            @if(isset($sorties) && method_exists($sorties, 'links'))
                                {{ $sorties->links() }}
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

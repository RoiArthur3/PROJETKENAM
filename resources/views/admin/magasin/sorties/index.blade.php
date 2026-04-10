@extends('layouts.app')

@section('title', 'Sorties - Module Magasin')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-warning text-dark">
                    <h4 class="mb-0">
                        <i class="fas fa-arrow-up me-2"></i>
                        Sorties de Stock
                    </h4>
                </div>
                <div class="card-body">
                    
                    <!-- Filtres -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <div class="input-group">
                                <input type="text" class="form-control" placeholder="Rechercher une sortie..." id="searchSortie">
                                <button class="btn btn-outline-secondary" type="button">
                                    <i class="fas fa-search"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-6 text-end">
                            <a href="{{ route('magasin.dashboard') }}" class="btn btn-secondary me-2">
                                <i class="fas fa-arrow-left me-2"></i>Retour
                            </a>
                            <a href="{{ route('magasin.sorties.create') }}" class="btn btn-warning">
                                <i class="fas fa-plus me-2"></i>Nouvelle Sortie
                            </a>
                        </div>
                    </div>

                    <!-- Tableau des sorties -->
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="table-dark">
                                <tr>
                                    <th>Référence</th>
                                    <th>Produit</th>
                                    <th>Quantité</th>
                                    <th>Destination</th>
                                    <th>Utilisateur</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($sorties as $sortie)
                                    <tr>
                                        <td>
                                            <span class="badge bg-warning text-dark">{{ $sortie->reference_sortie }}</span>
                                        </td>
                                        <td>
                                            <strong>{{ $sortie->produit_nom }}</strong>
                                        </td>
                                        <td>
                                            <span class="badge bg-danger">{{ $sortie->quantite }}</span>
                                        </td>
                                        <td>{{ $sortie->destination }}</td>
                                        <td>{{ $sortie->user_name }}</td>
                                        <td>{{ $sortie->created_at->format('d/m/Y H:i') }}</td>
                                        <td>
                                            <div class="btn-group btn-group-sm">
                                                <button class="btn btn-outline-info" title="Détails">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                                <button class="btn btn-outline-primary" title="Imprimer">
                                                    <i class="fas fa-print"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-between align-items-center mt-3">
                        <div>
                            Affichage de {{ $sorties->firstItem() }} à {{ $sorties->lastItem() }} sur {{ $sorties->total() }} sorties
                        </div>
                        {{ $sorties->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Recherche en temps réel
    document.getElementById('searchSortie').addEventListener('keyup', function(e) {
        const searchTerm = e.target.value.toLowerCase();
        const rows = document.querySelectorAll('tbody tr');
        
        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(searchTerm) ? '' : 'none';
        });
    });
});
</script>
@endsection

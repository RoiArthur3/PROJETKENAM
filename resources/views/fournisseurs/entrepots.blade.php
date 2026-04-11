@extends('layouts.app')

@section('title', 'Fournisseurs Entrepôts - KENAM SERVICES')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-info text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="mb-0">
                            <i class="fas fa-warehouse"></i>
                            Fournisseurs Entrepôts
                        </h3>
                        <div>
                            <a href="{{ route('fournisseurs.create') }}" class="btn btn-light btn-sm">
                                <i class="fas fa-plus"></i> Nouveau Fournisseur Entrepôt
                            </a>
                            <a href="{{ route('fournisseurs.dashboard') }}" class="btn btn-secondary btn-sm">
                                <i class="fas fa-tachometer-alt"></i> Dashboard
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <strong>Fournisseurs pour les Entrepôts</strong><br>
                        Cette page affiche les fournisseurs spécialisés dans les services et équipements pour les entrepôts.
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead class="table-dark">
                                <tr>
                                    <th>Référence</th>
                                    <th>Raison Sociale</th>
                                    <th>Email</th>
                                    <th>Téléphone</th>
                                    <th>Ville</th>
                                    <th>Statut</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if($fournisseurs->count() > 0)
                                    @foreach($fournisseurs as $fournisseur)
                                        <tr>
                                            <td><strong>{{ $fournisseur->reference ?? 'N/A' }}</strong></td>
                                            <td>{{ $fournisseur->raison_sociale }}</td>
                                            <td>{{ $fournisseur->email ?? 'N/A' }}</td>
                                            <td>{{ $fournisseur->telephone ?? 'N/A' }}</td>
                                            <td>{{ $fournisseur->ville ?? 'N/A' }}</td>
                                            <td>
                                                @if($fournisseur->est_actif)
                                                    <span class="badge bg-success">Actif</span>
                                                @else
                                                    <span class="badge bg-danger">Inactif</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="{{ route('fournisseurs.show', $fournisseur->id) }}" class="btn btn-outline-primary">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    <a href="{{ route('fournisseurs.edit', $fournisseur->id) }}" class="btn btn-outline-warning">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="7" class="text-center">
                                            <div class="alert alert-warning">
                                                <i class="fas fa-exclamation-triangle"></i>
                                                Aucun fournisseur entrepôt trouvé.
                                                <a href="{{ route('fournisseurs.create') }}" class="btn btn-sm btn-primary ms-2">
                                                    <i class="fas fa-plus"></i> Ajouter un fournisseur
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>

                    @if($fournisseurs->hasPages())
                        <div class="d-flex justify-content-center mt-4">
                            {{ $fournisseurs->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@extends('layouts.app')

@section('title', 'Liste des Fournisseurs - KENAM SERVICES')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="mb-0">
                            <i class="fas fa-truck"></i>
                            Liste des Fournisseurs
                        </h3>
                        <div>
                            <a href="{{ route('fournisseurs.create') }}" class="btn btn-light btn-sm">
                                <i class="fas fa-plus"></i> Nouveau Fournisseur
                            </a>
                            <a href="{{ route('fournisseurs.index') }}" class="btn btn-secondary btn-sm">
                                <i class="fas fa-list"></i> Vue Complète
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <strong>Liste simplifiée des fournisseurs</strong><br>
                        Cette page affiche la liste complète des fournisseurs avec un design simplifié.
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
                                                <td><strong>{{ $fournisseur->reference }}</strong></td>
                                                <td>
                                                    <strong>{{ $fournisseur->raison_sociale }}</strong>
                                                    @if($fournisseur->categorie)
                                                        <br><small class="text-muted">{{ $fournisseur->categorie->nom }}</small>
                                                    @endif
                                                </td>
                                                <td>{{ $fournisseur->email ?? '-' }}</td>
                                                <td>{{ $fournisseur->telephone ?? '-' }}</td>
                                                <td>{{ $fournisseur->ville ?? '-' }}</td>
                                                <td>
                                                    @if($fournisseur->est_actif)
                                                        <span class="badge bg-success">Actif</span>
                                                    @else
                                                        <span class="badge bg-danger">Inactif</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="btn-group">
                                                        <a href="{{ route('fournisseurs.show', $fournisseur->id) }}"
                                                           class="btn btn-sm btn-info" title="Voir">
                                                            <i class="fas fa-eye"></i>
                                                        </a>
                                                        <a href="{{ route('fournisseurs.edit', $fournisseur->id) }}"
                                                           class="btn btn-sm btn-warning" title="Modifier">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="7" class="text-center text-muted py-4">
                                                <i class="fas fa-inbox fa-2x"></i>
                                                <br><br>
                                                <strong>Aucun fournisseur trouvé</strong>
                                                <br>
                                                <a href="{{ route('fournisseurs.create') }}" class="btn btn-primary btn-sm mt-2">
                                                    <i class="fas fa-plus"></i> Ajouter un fournisseur
                                                </a>
                                            </td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-3">
                            <small class="text-muted">
                                <i class="fas fa-chart-bar"></i>
                                Total: {{ $fournisseurs->count() }} fournisseur(s)
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

@extends('layouts.app')

@section('title', 'Opportunités Commerciales - KENAM SERVICES')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-bullseye me-2 text-primary"></i>Opportunités Commerciales
            </h1>
            <p class="text-muted mb-0">Pipeline de vente et suivi des affaires</p>
        </div>
        <a class="btn btn-primary" href="{{ route('commercial.opportunites.create') }}">
            <i class="fas fa-plus me-2"></i>Nouvelle Opportunité
        </a>
    </div>

    <!-- Pipeline de Vente -->
    <div class="row mb-4">
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-project-diagram mr-2"></i>Pipeline de Vente
                    </h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Opportunité</th>
                                    <th>Client</th>
                                    <th>Montant</th>
                                    <th>Statut</th>
                                    <th>Probabilité</th>
                                    <th>Échéance</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse(($opportunites ?? []) as $opp)
                                <tr>
                                    <td>
                                        <div class="font-weight-bold">{{ $opp->titre ?? '-' }}</div>
                                        <div class="text-muted small">ID: #{{ $opp->id ?? 'N/A' }}</div>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2">
                                                {{ substr($opp->client->nom ?? 'N/A', 0, 2) }}
                                            </div>
                                            <div>
                                                <div class="font-weight-bold">{{ $opp->client->nom ?? '-' }}</div>
                                                <div class="text-muted small">Client existant</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="fw-bold text-primary">{{ number_format($opp->montant ?? 0, 0, ',', ' ') }} FCFA</div>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $opp->statut === 'gagné' ? 'success' : ($opp->statut === 'perdu' ? 'danger' : 'warning') }}">
                                            {{ ucfirst($opp->statut ?? 'en cours') }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="progress flex-grow-1 me-2" style="height: 6px;">
                                                <div class="progress-bar bg-{{ $opp->probabilite >= 70 ? 'success' : ($opp->probabilite >= 40 ? 'warning' : 'danger') }}"
                                                     data-width="{{ $opp->probabilite ?? 0 }}" style="width: 0%;"></div>
                                            </div>
                                            <small class="text-muted">{{ $opp->probabilite ?? 0 }}%</small>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="text-muted small">
                                            {{ optional($opp->date_echeance)->format('d/m/Y') }}
                                        </div>
                                    </td>
                                    <td>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="{{ route('commercial.opportunites.show', $opp) }}" class="btn btn-outline-primary" title="Voir">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('commercial.opportunites.edit', $opp) }}" class="btn btn-outline-success" title="Éditer">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('commercial.opportunites.destroy', $opp) }}" method="POST" onsubmit="return confirm('Supprimer cette opportunité ?');">
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
                                    <td colspan="7" class="text-center text-muted py-4">Aucune opportunité trouvée</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Colonne latérale avec statistiques -->
        <div class="col-lg-4">
            <!-- Carte de résumé -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 fw-bold text-primary">Résumé du Pipeline</h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6 mb-3">
                            <div class="border-end">
                                <div class="h4 text-primary mb-0">{{ $opportunites?->count() ?? 0 }}</div>
                                <div class="text-muted small">Total</div>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="h4 text-success mb-0">{{ $opportunites?->where('statut', 'gagné')->count() ?? 0 }}</div>
                            <div class="text-muted small">Gagnées</div>
                        </div>
                    </div>
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="border-end">
                                <div class="h4 text-warning mb-0">{{ $opportunites?->where('statut', 'en cours')->count() ?? 0 }}</div>
                                <div class="text-muted small">En cours</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="h4 text-danger mb-0">{{ $opportunites?->where('statut', 'perdu')->count() ?? 0 }}</div>
                            <div class="text-muted small">Perdues</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Carte de valeur -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 fw-bold text-primary">Valeur du Pipeline</h6>
                </div>
                <div class="card-body">
                    <div class="text-center">
                        <div class="h3 text-primary mb-2">
                            {{ number_format($opportunites?->sum('montant') ?? 0, 0, ',', ' ') }} FCFA
                        </div>
                        <div class="text-muted small">Valeur totale</div>
                        <hr>
                        <div class="row text-center">
                            <div class="col-6">
                                <div class="h5 text-success mb-0">
                                    {{ number_format($opportunites?->where('statut', 'gagné')->sum('montant') ?? 0, 0, ',', ' ') }}
                                </div>
                                <div class="text-muted small">Gagnées</div>
                            </div>
                            <div class="col-6">
                                <div class="h5 text-warning mb-0">
                                    {{ number_format($opportunites?->where('statut', 'en cours')->sum('montant') ?? 0, 0, ',', ' ') }}
                                </div>
                                <div class="text-muted small">En cours</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions rapides -->
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 fw-bold text-primary">Actions Rapides</h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('commercial.opportunites.create') }}" class="btn btn-primary">
                            <i class="fas fa-plus me-2"></i>Nouvelle Opportunité
                        </a>
                        <button class="btn btn-outline-secondary">
                            <i class="fas fa-download me-2"></i>Exporter Pipeline
                        </button>
                        <button class="btn btn-outline-info">
                            <i class="fas fa-chart-line me-2"></i>Rapport d'Analyse
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

<!-- Modal Nouvelle Opportunité -->
<div class="modal fade" id="nouvelleOpportuniteModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Nouvelle Opportunité</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('commercial.opportunites.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Titre de l'opportunité *</label>
                            <input type="text" class="form-control" name="titre" required placeholder="Ex: Contrat Transport Annuel">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Client/Prospect *</label>
                            <select class="form-select" name="client_id" required>
                                <option value="">Sélectionner...</option>
                                @forelse(($clients ?? []) as $client)
                                <option value="{{ $client->id }}">{{ $client->nom ?? $client->raison_sociale ?? $client->id }}</option>
                                @empty
                                <option value="">Aucun client disponible</option>
                                @endforelse
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Montant estimé (FCFA) *</label>
                            <input type="number" class="form-control" name="montant" required placeholder="0">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Statut *</label>
                            <select class="form-select" name="statut" required>
                                <option value="">Sélectionner...</option>
                                <option value="identification">Identification</option>
                                <option value="proposition">Proposition</option>
                                <option value="négociation">Négociation</option>
                                <option value="gagné">Gagné</option>
                                <option value="perdu">Perdu</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Probabilité (%) *</label>
                            <input type="number" class="form-control" name="probabilite" min="0" max="100" required placeholder="0-100">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Date d'échéance *</label>
                            <input type="date" class="form-control" name="date_echeance" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" rows="3" placeholder="Détails de l'opportunité..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-plus me-2"></i>Créer Opportunité
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Script pour les barres de progression (données dynamiques issues de la base) -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.progress-bar[data-width]').forEach(function(bar) {
        const width = bar.getAttribute('data-width') || bar.dataset.width || 0;
        bar.style.width = width + '%';
    });
});
</script>

@endsection

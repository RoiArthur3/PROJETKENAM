@extends('layouts.app')

@section('title', 'Opérations Payées')

@section('content')
<div class="container-fluid mt-4">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
                <div>
                    <h2 class="mb-0">
                        <i class="fas fa-coins me-2 text-success"></i>Opérations Payées & Décaissements
                    </h2>
                    <small class="text-muted">Suivi des opérations exécutées et des décaissements de caisse (admin / superadmin)</small>
                </div>
                <a href="{{ route('validations.pending') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Retour aux validations
                </a>
            </div>

            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show">
                    <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @elseif (session('info'))
                <div class="alert alert-info alert-dismissible fade show">
                    <i class="fas fa-info-circle me-2"></i>{{ session('info') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @elseif (session('error'))
                <div class="alert alert-danger alert-dismissible fade show">
                    <i class="fas fa-exclamation-triangle me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <div class="card shadow mb-4">
                <div class="card-header bg-success text-white">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <h5 class="m-0">
                            <i class="fas fa-hand-holding-usd me-2"></i>{{ $operations->total() }} Paiement(s) trouvé(s)
                        </h5>
                        <form method="GET" class="d-flex align-items-center gap-2">
                            <input type="text" name="q" value="{{ $search }}" class="form-control form-control-sm" placeholder="Recherche (titre, id)..." style="min-width: 220px;">
                            <select name="service_id" class="form-select form-select-sm" style="min-width: 200px;">
                                <option value="">Tous les services</option>
                                @foreach($services as $service)
                                    <option value="{{ $service->id }}" {{ ($serviceId == $service->id) ? 'selected' : '' }}>
                                        {{ $service->nom }}
                                    </option>
                                @endforeach
                            </select>
                            <button class="btn btn-light btn-sm" type="submit">
                                <i class="fas fa-search me-1"></i>Filtrer
                            </button>
                        </form>
                    </div>
                </div>
                <div class="card-body">
                    @if ($operations->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Réf</th>
                                        <th>Titre</th>
                                        <th>Type</th>
                                        <th>Service</th>
                                        <th class="text-end">Montant</th>
                                        <th>Payée le</th>
                                        <th>Payé par</th>
                                        <th>Mode</th>
                                        <th>Caisse</th>
                                        <th>Facture</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($operations as $item)
                                        <tr>
                                            <td>
                                                @if($item['type'] === 'depense')
                                                    <span class="badge bg-warning text-dark">{{ $item['reference'] }}</span>
                                                @else
                                                    <span class="badge bg-success">{{ $item['reference'] }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                {{ \Illuminate\Support\Str::limit($item['titre'], 60) }}
                                                <div class="text-muted small">
                                                    @if($item['type'] === 'depense')
                                                        <i class="fas fa-receipt text-warning me-1"></i>Dépense de caisse
                                                    @else
                                                        <i class="fas fa-check-circle text-success me-1"></i>Opération payée
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                @if($item['type'] === 'depense')
                                                    <span class="badge bg-outline-warning text-warning">Dépense</span>
                                                @else
                                                    <span class="badge bg-outline-success text-success">Opération</span>
                                                @endif
                                            </td>
                                            <td>{{ $item['service'] ?? '-' }}</td>
                                            <td class="text-end fw-bold">
                                                {{ number_format($item['montant'] ?? 0, 0, ',', ' ') }} FCFA
                                            </td>
                                            <td>
                                                @if($item['date_paiement'])
                                                    <span class="badge bg-light text-dark">{{ \Carbon\Carbon::parse($item['date_paiement'])->format('d/m/Y H:i') }}</span>
                                                @else
                                                    <span class="text-muted">Non renseigné</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($item['paye_par'])
                                                    <span class="badge bg-primary">{{ $item['paye_par'] }}</span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($item['mode_paiement'])
                                                    <span class="badge bg-info text-dark">{{ ucfirst($item['mode_paiement']) }}</span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($item['caisse'])
                                                    <span class="badge bg-secondary">{{ $item['caisse']->nom }}</span>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($item['facture'])
                                                    <span class="badge bg-info text-dark">{{ $item['facture']->invoice_number }}</span>
                                                    <small class="d-block text-muted">Statut: {{ ucfirst($item['facture']->status) }}</small>
                                                @else
                                                    <span class="text-muted">-</span>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('operations.show', $item['id']) }}" class="btn btn-sm btn-outline-info">
                                                    <i class="fas fa-eye me-1"></i>Voir
                                                </a>
                                                @if($item['type'] === 'depense')
                                                    <button class="btn btn-sm btn-outline-warning" onclick="showDepenseDetails({{ $item['id'] }})">
                                                        <i class="fas fa-receipt me-1"></i>Dépense
                                                    </button>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-center mt-3">
                            {{ $operations->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                            <p class="text-muted">Aucun paiement trouvé pour le moment.</p>
                        </div>
                    @endif
                </div>
            </div>

            <div class="mt-4">
                <div class="nav nav-tabs" role="tablist">
                    <a href="{{ route('validations.pending') }}" class="nav-link">
                        <i class="fas fa-hourglass-half me-1"></i>En Attente
                    </a>
                    <a href="{{ route('validations.approved') }}" class="nav-link">
                        <i class="fas fa-check-circle me-1"></i>Approuvées
                    </a>
                    <a href="{{ route('validations.rejected') }}" class="nav-link">
                        <i class="fas fa-times-circle me-1"></i>Rejetées
                    </a>
                    <a href="{{ route('validations.history') }}" class="nav-link">
                        <i class="fas fa-history me-1"></i>Historique
                    </a>
                    <a href="{{ route('validations.paid') }}" class="nav-link active">
                        <i class="fas fa-coins me-1"></i>Payées
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function showDepenseDetails(operationId) {
    // Récupérer les détails de la dépense via AJAX
    fetch(`/api/operations/${operationId}/depenses`)
        .then(response => response.json())
        .then(data => {
            if (data.success && data.depenses.length > 0) {
                let content = '<h6>Dépenses de caisse associées:</h6>';
                data.depenses.forEach(depense => {
                    content += `
                        <div class="border rounded p-3 mb-2">
                            <div class="row">
                                <div class="col-md-6">
                                    <strong>Référence:</strong> ${depense.reference}<br>
                                    <strong>Libellé:</strong> ${depense.libelle}<br>
                                    <strong>Montant:</strong> ${new Intl.NumberFormat('fr-FR').format(depense.montant)} FCFA
                                </div>
                                <div class="col-md-6">
                                    <strong>Date:</strong> ${new Date(depense.date_depense).toLocaleDateString('fr-FR')}<br>
                                    <strong>Mode:</strong> ${depense.mode_paiement}<br>
                                    <strong>Caisse:</strong> ${depense.caisse?.nom || 'N/A'}
                                </div>
                            </div>
                            ${depense.description ? `<div class="mt-2"><strong>Description:</strong> ${depense.description}</div>` : ''}
                        </div>
                    `;
                });

                // Afficher dans un modal
                const modalHtml = `
                    <div class="modal fade" id="depenseModal" tabindex="-1">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Détails des dépenses</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    ${content}
                                </div>
                            </div>
                        </div>
                    </div>
                `;

                // Supprimer le modal s'il existe déjà
                const existingModal = document.getElementById('depenseModal');
                if (existingModal) {
                    existingModal.remove();
                }

                // Ajouter le nouveau modal et l'afficher
                document.body.insertAdjacentHTML('beforeend', modalHtml);
                const modal = new bootstrap.Modal(document.getElementById('depenseModal'));
                modal.show();

                // Nettoyer le modal quand il est fermé
                document.getElementById('depenseModal').addEventListener('hidden.bs.modal', function() {
                    this.remove();
                });
            } else {
                alert('Aucune dépense trouvée pour cette opération.');
            }
        })
        .catch(error => {
            console.error('Erreur:', error);
            alert('Erreur lors de la récupération des détails.');
        });
}
</script>
@endpush

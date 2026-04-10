@extends('layouts.app')

@section('title', 'Gestion des virements')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Gestion des virements</h4>
                    <a href="{{ route('tresorerie.virements.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Nouveau virement
                    </a>
                </div>

                <!-- Statistiques -->
                <div class="card-body bg-light">
                    <div class="row text-center">
                        <div class="col-6 col-md-3 mb-3 mb-md-0">
                            <div class="card">
                                <div class="card-body">
                                    <h6 class="text-muted">Total</h6>
                                    <h3>{{ $stats['total'] }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3 mb-3 mb-md-0">
                            <div class="card">
                                <div class="card-body">
                                    <h6 class="text-warning">En attente</h6>
                                    <h3 class="text-warning">{{ $stats['en_attente'] }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="card">
                                <div class="card-body">
                                    <h6 class="text-success">Effectués</h6>
                                    <h3 class="text-success">{{ $stats['effectues'] }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-6 col-md-3">
                            <div class="card">
                                <div class="card-body">
                                    <h6 class="text-danger">Échecs</h6>
                                    <h3 class="text-danger">{{ $stats['echecs'] + $stats['annules'] }}</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Liste des virements -->
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead class="thead-light">
                                <tr>
                                    <th>Référence</th>
                                    <th>Date</th>
                                    <th>Compte source</th>
                                    <th>Compte destination</th>
                                    <th class="text-right">Montant</th>
                                    <th>Statut</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($virements as $virement)
                                    <tr>
                                        <td>{{ $virement->reference }}</td>
                                        <td>{{ $virement->date_virement->format('d/m/Y') }}</td>
                                        <td>{{ optional($virement->compteSource)->intitule_compte ?? 'Compte inconnu' }} ({{ optional(optional($virement->compteSource)->banque)->nom ?? 'Banque inconnue' }})</td>
                                        <td>{{ optional($virement->compteDestination)->intitule_compte ?? 'Compte inconnu' }} ({{ optional(optional($virement->compteDestination)->banque)->nom ?? 'Banque inconnue' }})</td>
                                        <td class="text-right">{{ number_format($virement->montant, 0, ',', ' ') }} {{ $virement->devise }}</td>
                                        <td>
                                            @php
                                                $badgeClass = [
                                                    'en_attente' => 'warning',
                                                    'effectue' => 'success',
                                                    'annule' => 'secondary',
                                                    'echec' => 'danger'
                                                ][$virement->statut] ?? 'secondary';
                                            @endphp
                                            <span class="badge badge-{{ $badgeClass }}">
                                                {{ ucfirst($virement->statut) }}
                                            </span>
                                        </td>
                                        <td>
                                            <a href="{{ route('tresorerie.virements.show', $virement) }}" 
                                               class="btn btn-sm btn-info" 
                                               title="Voir les détails">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if($virement->statut === 'en_attente' && auth()->user()->can('valider virements'))
                                                <button class="btn btn-sm btn-success btn-valider-virement" 
                                                        data-id="{{ $virement->id }}"
                                                        title="Valider le virement">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                                <button class="btn btn-sm btn-danger btn-rejeter-virement" 
                                                        data-id="{{ $virement->id }}"
                                                        title="Rejeter le virement">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">Aucun virement trouvé</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center mt-4">
                        {{ $virements->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de validation de virement -->
<div class="modal fade" id="validationModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="validationForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">Confirmer l'opération</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Fermer">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="commentaire">Commentaire (optionnel)</label>
                        <textarea class="form-control" id="commentaire" name="commentaire" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Confirmer</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Validation d'un virement
        $('.btn-valider-virement').click(function() {
            const virementId = $(this).data('id');
            const url = `{{ url('tresorerie/virements') }}/${virementId}/valider`;
            
            $('#validationForm').attr('action', url);
            $('#validationModal').modal('show');
        });

        // Rejet d'un virement
        $('.btn-rejeter-virement').click(function() {
            const virementId = $(this).data('id');
            const url = `{{ url('tresorerie/virements') }}/${virementId}/rejeter`;
            
            $('#validationForm').attr('action', url);
            $('#validationModal').modal('show');
        });
    });
</script>
@endpush

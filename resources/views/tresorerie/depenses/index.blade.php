@extends('layouts.app')

@section('title', 'Gestion des dépenses')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">Liste des dépenses</h4>
                    <a href="{{ route('tresorerie.depenses.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Nouvelle dépense
                    </a>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <!-- Filtres -->
                    <div class="mb-4">
                        <form method="GET" action="{{ route('tresorerie.depenses.index') }}" class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label fw-bold">Statut</label>
                                <select name="statut" class="form-select">
                                    <option value="">Tous</option>
                                    <option value="brouillon" {{ request('statut') == 'brouillon' ? 'selected' : '' }}>Brouillon</option>
                                    <option value="soumis" {{ request('statut') == 'soumis' ? 'selected' : '' }}>Soumis</option>
                                    <option value="valide" {{ request('statut') == 'valide' ? 'selected' : '' }}>Validé</option>
                                    <option value="rejete" {{ request('statut') == 'rejete' ? 'selected' : '' }}>Rejeté</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold">Caisse</label>
                                <select name="caisse_id" class="form-select">
                                    <option value="">Toutes les caisses</option>
                                    @if(isset($caisses))
                                        @foreach($caisses as $caisse)
                                            <option value="{{ $caisse->id }}" {{ (string)request('caisse_id') === (string)$caisse->id ? 'selected' : '' }}>
                                                {{ $caisse->nom }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold">Bénéficiaire</label>
                                <select name="beneficiaire_id" class="form-select">
                                    <option value="">Tous les bénéficiaires</option>
                                    @if(isset($beneficiaires))
                                        @foreach($beneficiaires as $beneficiaire)
                                            <option value="{{ $beneficiaire->id }}" {{ (string)request('beneficiaire_id') === (string)$beneficiaire->id ? 'selected' : '' }}>
                                                {{ $beneficiaire->name }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold">Date de début</label>
                                <input type="date" name="date_debut" value="{{ request('date_debut') }}" class="form-control">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold">Date de fin</label>
                                <input type="date" name="date_fin" value="{{ request('date_fin') }}" class="form-control">
                            </div>
                            <div class="col-md-3 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary me-2">
                                    <i class="fas fa-filter me-1"></i>Filtrer
                                </button>
                                <a href="{{ route('tresorerie.depenses.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-undo me-1"></i>Réinitialiser
                                </a>
                            </div>
                        </form>
                    </div>

                    <!-- Cartes indicateurs -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="card bg-primary text-white mb-3">
                                <div class="card-body">
                                    <h6 class="card-title">Total Dépenses</h6>
                                    <h3 class="mb-0">{{ number_format($totalDepenses ?? 0, 0, ',', ' ') }} FCFA</h3>
                                    <small>Montant total des dépenses</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white mb-3">
                                <div class="card-body">
                                    <h6 class="card-title">Ce Mois</h6>
                                    <h3 class="mb-0">{{ number_format($depensesMois ?? 0, 0, ',', ' ') }} FCFA</h3>
                                    @php
                                        $evolution = 0;
                                        if(($depensesMoisPrecedent ?? 0) > 0) {
                                            $evolution = (($depensesMois ?? 0) - ($depensesMoisPrecedent ?? 0)) / ($depensesMoisPrecedent ?? 0) * 100;
                                        } elseif(($depensesMois ?? 0) > 0) {
                                            $evolution = 100;
                                        }
                                    @endphp
                                    <small>
                                        <i class="fas fa-arrow-{{ $evolution >= 0 ? 'up' : 'down' }} me-1"></i>
                                        {{ number_format(abs($evolution), 1) }}% vs mois dernier
                                    </small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-dark mb-3">
                                <div class="card-body">
                                    <h6 class="card-title">En Attente</h6>
                                    <h3 class="mb-0">{{ $enAttente ?? 0 }}</h3>
                                    <small>Dépenses en attente</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-info text-white mb-3">
                                <div class="card-body">
                                    <h6 class="card-title">Moyenne/Mois</h6>
                                    <h3 class="mb-0">{{ number_format($moyenneMensuelle ?? 0, 0, ',', ' ') }} FCFA</h3>
                                    <small>Moyenne sur 12 mois</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tableau des dépenses -->
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead class="text-primary">
                                <tr>
                                    <th>Référence</th>
                                    <th>Date</th>
                                    <th>Montant</th>
                                    <th>Bénéficiaire</th>
                                    <th>Caisse</th>
                                    <th>Statut</th>
                                    <th class="text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($depenses as $depense)
                                    <tr>
                                        <td>{{ $depense->reference }}</td>
                                        <td>{{ $depense->date_depense->format('d/m/Y') }}</td>
                                        <td class="fw-bold">{{ number_format($depense->montant, 0, ',', ' ') }} FCFA</td>
                                        <td>{{ $depense->beneficiaire->name ?? 'N/A' }}</td>
                                        <td>{{ $depense->caisse->nom ?? 'N/A' }}</td>
                                        <td>
                                            @php
                                                $badgeClass = [
                                                    'brouillon' => 'secondary',
                                                    'soumise' => 'warning',
                                                    'validee' => 'success',
                                                    'rejetee' => 'danger'
                                                ][$depense->statut] ?? 'secondary';
                                            @endphp
                                            <span class="badge bg-{{ $badgeClass }}">
                                                {{ ucfirst($depense->statut) }}
                                            </span>
                                        </td>
                                        <td class="text-right">
                                            <a href="{{ route('tresorerie.depenses.show', $depense) }}"
                                               class="btn btn-info btn-sm"
                                               title="Voir les détails">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('tresorerie.depenses.edit', $depense) }}"
                                               class="btn btn-warning btn-sm"
                                               title="Modifier">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('tresorerie.depenses.destroy', $depense) }}"
                                                  method="POST"
                                                  class="d-inline"
                                                  onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette dépense ?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm" title="Supprimer">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">
                                            Aucune dépense n'a été trouvée.
                                            <a href="{{ route('tresorerie.depenses.create') }}" class="btn btn-link">
                                                Créer une nouvelle dépense
                                            </a>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($depenses->hasPages())
                        <div class="mt-3">
                            {{ $depenses->withQueryString()->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de rejet -->
<div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="rejectForm" method="POST">
                @csrf
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="rejectModalLabel">Rejeter la dépense</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="motif_rejet" class="form-label">Motif du rejet</label>
                        <textarea class="form-control" id="motif_rejet" name="motif_rejet" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-danger">Confirmer le rejet</button>
                </div>
            </form>
        </div>
    </div>
</div>
@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialisation des tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });

        // Gestion du modal de rejet
        var rejectModal = document.getElementById('rejectModal');
        if (rejectModal) {
            rejectModal.addEventListener('show.bs.modal', function (event) {
                var button = event.relatedTarget;
                var depenseId = button.getAttribute('data-id');
                var form = rejectModal.querySelector('form');
                form.action = '/tresorerie/depenses/' + depenseId + '/reject';
                form.reset();
            });
        }

        // Initialisation de DataTable avec configuration simplifiée
        var table = $('#depensesTable').DataTable({
            // Configuration de base
            dom: "<'row'<'col-sm-12'tr>><'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
            language: {
                url: '//cdn.datatables.net/plug-ins/1.10.24/i18n/French.json',
                search: "",
                searchPlaceholder: "Rechercher..."
            },

            // Désactiver le tri côté serveur (nous utilisons le tri côté client)
            serverSide: false,

            // Désactiver le traitement côté client (nous avons déjà les données)
            processing: false,

            // Configuration des colonnes
            columns: [
                { data: 'id', name: 'id' },                           // Colonne 1 : ID
                { data: 'date_depense', name: 'date_depense' },       // Colonne 2 : Date
                { data: 'reference', name: 'reference' },             // Colonne 3 : Référence
                { data: 'beneficiaire', name: 'beneficiaire' },       // Colonne 4 : Bénéficiaire
                { data: 'caisse', name: 'caisse' },                   // Colonne 5 : Caisse
                { data: 'montant', name: 'montant' },                 // Colonne 6 : Montant
                { data: 'statut', name: 'statut' },                   // Colonne 7 : Statut
                { data: 'actions', name: 'actions' }                   // Colonne 8 : Actions
            ],

            // Configuration du tri
            order: [[1, 'desc']], // Tri par défaut sur la colonne Date (index 1)

            // Configuration des colonnes (largeurs, classes, etc.)
            columnDefs: [
                // Colonnes non triables (ID et Actions)
                { orderable: false, targets: [0, 7] },

                // Tri par date pour la colonne Date (index 1)
                {
                    type: 'date-eu',
                    targets: 1,
                    render: function(data, type, row) {
                        if (type === 'sort') {
                            return new Date(data).getTime();
                        }
                        return data;
                    }
                },

                // Alignement des colonnes
                { className: 'text-end', targets: 5 },    // Montant aligné à droite
                { className: 'text-center', targets: 7 }, // Actions centrées

                // Largeurs des colonnes
                { width: '5%', targets: 0 },   // ID
                { width: '10%', targets: 1 },  // Date
                { width: '15%', targets: 2 },  // Référence
                { width: '20%', targets: 3 },  // Bénéficiaire
                { width: '15%', targets: 4 },  // Caisse
                { width: '10%', targets: 5 },  // Montant
                { width: '10%', targets: 6 },  // Statut
                { width: '15%', targets: 7 }   // Actions
            ],

            // Autres options
            responsive: true,
            autoWidth: false,
            pageLength: 25,
            paging: true,
            info: true,
            searching: true
        });

        // Gestion de la recherche
        $('#searchInput').on('keyup', function() {
            table.search(this.value).draw();
        });
    });
</script>
@endpush

@push('scripts')
<script>
    // Gestion des cases à cocher
    document.addEventListener('DOMContentLoaded', function() {
        // Case à cocher principale
        const selectAllCheckbox = document.querySelector('thead input[type="checkbox"]');
        const checkboxes = document.querySelectorAll('.depense-checkbox');

        if (selectAllCheckbox) {
            selectAllCheckbox.addEventListener('change', function() {
                checkboxes.forEach(checkbox => {
                    checkbox.checked = selectAllCheckbox.checked;
                });
            });
        }

        // Désélectionner la case à cocher principale si une case est décochée
        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                if (!this.checked && selectAllCheckbox.checked) {
                    selectAllCheckbox.checked = false;
                }
            });
        });

        // Gestion de la soumission du formulaire de filtrage
        const filterForm = document.getElementById('filter-form');
        if (filterForm) {
            // Réinitialiser les filtres
            const resetFilters = document.getElementById('reset-filters');
            if (resetFilters) {
                resetFilters.addEventListener('click', function(e) {
                    e.preventDefault();
                    const inputs = filterForm.querySelectorAll('input, select');
                    inputs.forEach(input => {
                        if (input.type === 'text' || input.type === 'search') {
                            input.value = '';
                        } else if (input.type === 'select-one') {
                            input.selectedIndex = 0;
                        } else if (input.type === 'checkbox' || input.type === 'radio') {
                            input.checked = false;
                        }
                    });
                    filterForm.submit();
                });
            }
        }
    });
</script>
@endpush
@endsection

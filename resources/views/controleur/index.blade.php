@extends('layouts.app')

@section('title', 'Gestion des Contrôles - KENAM SERVICES')

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
@endpush

@section('content')
<div class="container-fluid">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">Gestion des Contrôles</h1>
        <a href="{{ route('controleur.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Nouveau Contrôle
        </a>
    </div>

    <!-- Filtres et Recherche -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Filtres de recherche</h6>
            <button class="btn btn-link" type="button" data-bs-toggle="collapse" data-bs-target="#filterCollapse">
                <i class="fas fa-filter"></i> Filtres
            </button>
        </div>
        <div class="collapse show" id="filterCollapse">
            <div class="card-body">
                <form action="{{ route('controleur.index') }}" method="GET" class="row g-3">
                    <div class="col-md-3">
                        <label for="vehicule" class="form-label">Véhicule</label>
                        <select class="form-select" id="vehicule" name="vehicule_id">
                            <option value="">Tous les véhicules</option>
                            @foreach($vehicules ?? [] as $vehicule)
                                <option value="{{ $vehicule->id }}" {{ request('vehicule_id') == $vehicule->id ? 'selected' : '' }}>
                                    {{ $vehicule->immatriculation }} - {{ $vehicule->marque }} {{ $vehicule->modele }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="type_controle" class="form-label">Type de contrôle</label>
                        <select class="form-select" id="type_controle" name="type_controle">
                            <option value="">Tous les types</option>
                            <option value="technique" {{ request('type_controle') == 'technique' ? 'selected' : '' }}>Contrôle technique</option>
                            <option value="securite" {{ request('type_controle') == 'securite' ? 'selected' : '' }}>Contrôle de sécurité</option>
                            <option value="entretien" {{ request('type_controle') == 'entretien' ? 'selected' : '' }}>Contrôle d'entretien</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="resultat" class="form-label">Résultat</label>
                        <select class="form-select" id="resultat" name="resultat">
                            <option value="">Tous les résultats</option>
                            <option value="conforme" {{ request('resultat') == 'conforme' ? 'selected' : '' }}>Conforme</option>
                            <option value="non_conforme" {{ request('resultat') == 'non_conforme' ? 'selected' : '' }}>Non conforme</option>
                            <option value="en_attente" {{ request('resultat') == 'en_attente' ? 'selected' : '' }}>En attente</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="date_debut" class="form-label">Date de début</label>
                        <input type="date" class="form-control" id="date_debut" name="date_debut" value="{{ request('date_debut') }}">
                    </div>
                    <div class="col-md-3">
                        <label for="date_fin" class="form-label">Date de fin</label>
                        <input type="date" class="form-control" id="date_fin" name="date_fin" value="{{ request('date_fin') }}">
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="fas fa-search me-1"></i> Rechercher
                        </button>
                        <a href="{{ route('controleur.index') }}" class="btn btn-secondary">
                            <i class="fas fa-undo me-1"></i> Réinitialiser
                        </a>
                        @if(request()->hasAny(['vehicule_id', 'type_controle', 'resultat', 'date_debut', 'date_fin']))
                            <a href="{{ route('controleur.export') }}?{{ http_build_query(request()->query()) }}" class="btn btn-success float-end">
                                <i class="fas fa-file-excel me-1"></i> Exporter en Excel
                            </a>
                        @endif
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Cartes Résumé -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Total des contrôles</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $controles->total() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clipboard-list fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                En attente</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $controles->where('resultat', 'en_attente')->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Conformes</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $controles->where('resultat', 'conforme')->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                Non conformes</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $controles->where('resultat', 'non_conforme')->count() }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tableau des Contrôles -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">Liste des contrôles</h6>
            <div class="dropdown">
                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-ellipsis-v"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="dropdownMenuButton">
                    <li><a class="dropdown-item" href="#"><i class="fas fa-print me-2"></i>Imprimer</a></li>
                    <li><a class="dropdown-item" href="#"><i class="fas fa-file-pdf me-2"></i>Exporter en PDF</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="{{ route('controleur.export') }}"><i class="fas fa-file-excel me-2"></i>Exporter en Excel</a></li>
                </ul>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover" id="dataTable" width="100%" cellspacing="0">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Véhicule</th>
                            <th>Type</th>
                            <th>Date</th>
                            <th>Résultat</th>
                            <th>Contrôleur</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($controles as $controle)
                        <tr>
                            <td>#{{ str_pad($controle->id, 6, '0', STR_PAD_LEFT) }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="me-2">
                                        <i class="fas fa-car text-primary"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold">{{ $controle->vehicle->immatriculation ?? 'N/A' }}</div>
                                        <div class="text-muted small">{{ $controle->vehicle->marque ?? '' }} {{ $controle->vehicle->modele ?? '' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @php
                                    $typeBadge = [
                                        'technique' => 'info',
                                        'securite' => 'warning',
                                        'entretien' => 'primary',
                                    ][$controle->type_controle] ?? 'secondary';
                                    
                                    $typeText = [
                                        'technique' => 'Technique',
                                        'securite' => 'Sécurité',
                                        'entretien' => 'Entretien',
                                    ][$controle->type_controle] ?? 'Autre';
                                @endphp
                                <span class="badge bg-{{ $typeBadge }}">
                                    {{ $typeText }}
                                </span>
                            </td>
                            <td>{{ $controle->date_controle->format('d/m/Y') }}</td>
                            <td>
                                @php
                                    $resultatClass = [
                                        'conforme' => 'success',
                                        'non_conforme' => 'danger',
                                        'en_attente' => 'warning',
                                    ][$controle->resultat] ?? 'secondary';
                                    
                                    $resultatText = [
                                        'conforme' => 'Conforme',
                                        'non_conforme' => 'Non Conforme',
                                        'en_attente' => 'En Attente',
                                    ][$controle->resultat] ?? 'Inconnu';
                                @endphp
                                <span class="badge bg-{{ $resultatClass }}">
                                    <i class="fas fa-{{ $controle->resultat === 'conforme' ? 'check-circle' : ($controle->resultat === 'non_conforme' ? 'times-circle' : 'clock') }} me-1"></i>
                                    {{ $resultatText }}
                                </span>
                            </td>
                            <td>{{ $controle->user->name ?? 'N/A' }}</td>
                            <td class="text-end">
                                <div class="btn-group" role="group">
                                    <a href="{{ route('controleur.show', $controle->id) }}" class="btn btn-sm btn-outline-primary" data-bs-toggle="tooltip" title="Voir">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('controleur.edit', $controle->id) }}" class="btn btn-sm btn-outline-secondary" data-bs-toggle="tooltip" title="Modifier">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $controle->id }}" title="Supprimer">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                                
                                <!-- Modal de confirmation de suppression -->
                                <div class="modal fade" id="deleteModal{{ $controle->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Confirmer la suppression</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                                            </div>
                                            <div class="modal-body">
                                                Êtes-vous sûr de vouloir supprimer ce contrôle ? Cette action est irréversible.
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                                                <form action="{{ route('controleur.destroy', $controle->id) }}" method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger">Supprimer</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                <i class="fas fa-inbox fa-3x mb-3"></i>
                                <p class="mb-0">Aucun contrôle trouvé</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            @if($controles->hasPages())
            <div class="d-flex justify-content-between align-items-center mt-4">
                <div class="text-muted">
                    Affichage de {{ $controles->firstItem() }} à {{ $controles->lastItem() }} sur {{ $controles->total() }} entrées
                </div>
                <nav aria-label="Pagination">
                    {{ $controles->withQueryString()->links() }}
                </nav>
            </div>
            @endif
        </div>
    </div>
</div>

<script>
// Activer les tooltips
$(document).ready(function() {
    // Initialisation de DataTables avec personnalisation
    const dataTable = $('#dataTable').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/fr-FR.json',
            search: "Rechercher :",
            lengthMenu: "Afficher _MENU_ entrées par page",
            zeroRecords: "Aucun contrôle trouvé",
            info: "Affichage de _START_ à _END_ sur _TOTAL_ entrées",
            infoEmpty: "Aucune entrée disponible",
            infoFiltered: "(filtré de _MAX_ entrées au total)",
            paginate: {
                first: "Premier",
                last: "Dernier",
                next: "Suivant",
                previous: "Précédent"
            }
        },
        order: [[3, 'desc']], // Tri par date par défaut
        responsive: true,
        pageLength: 25,
        dom: '<"row mb-3"<"col-md-6"l><"col-md-6"f>>rt<"row mt-3"<"col-md-6"i><"col-md-6"p>>',
        initComplete: function() {
            // Personnalisation du champ de recherche
            $('.dataTables_filter input')
                .attr('placeholder', 'Rechercher...')
                .addClass('form-control form-control-sm d-inline-block w-auto');
                
            // Personnalisation du sélecteur de nombre d'entrées
            $('.dataTables_length select').addClass('form-select form-select-sm d-inline-block w-auto');
        }
    });

    // Gestion des dates dans les filtres
    const today = new Date().toISOString().split('T')[0];
    const dateFinInput = document.getElementById('date_fin');
    const dateDebutInput = document.getElementById('date_debut');
    
    // Définir la date de fin à aujourd'hui par défaut si vide
    if (dateFinInput && !dateFinInput.value) {
        dateFinInput.value = today;
    }
    
    // Définir la date de début à 1 mois avant par défaut si vide
    if (dateDebutInput && !dateDebutInput.value) {
        const oneMonthAgo = new Date();
        oneMonthAgo.setMonth(oneMonthAgo.getMonth() - 1);
        dateDebutInput.value = oneMonthAgo.toISOString().split('T')[0];
    }
    
    // Validation des dates (date de début <= date de fin)
    if (dateDebutInput && dateFinInput) {
        dateDebutInput.max = today;
        dateFinInput.max = today;
        
        dateDebutInput.addEventListener('change', function() {
            dateFinInput.min = this.value;
            if (new Date(dateFinInput.value) < new Date(this.value)) {
                dateFinInput.value = this.value;
            }
        });
        
        dateFinInput.addEventListener('change', function() {
            if (new Date(this.value) < new Date(dateDebutInput.value)) {
                this.value = dateDebutInput.value;
            }
        });
    }

    // Initialisation des tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Gestion de l'export Excel
    $('.export-excel').on('click', function(e) {
        e.preventDefault();
        
        // Afficher un indicateur de chargement
        const button = $(this);
        const originalText = button.html();
        button.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-1"></i> Génération...');
        
        // Récupérer les paramètres de filtrage
        const filters = {
            vehicule_id: $('#vehicule').val(),
            type_controle: $('#type_controle').val(),
            resultat: $('#resultat').val(),
            date_debut: $('#date_debut').val(),
            date_fin: $('#date_fin').val(),
            _token: '{{ csrf_token() }}'
        };
        
        // Effectuer la requête d'export
        $.ajax({
            url: '{{ route("controleur.export") }}',
            type: 'POST',
            data: filters,
            xhrFields: {
                responseType: 'blob'
            },
            success: function(response) {
                // Créer un lien de téléchargement
                const url = window.URL.createObjectURL(response);
                const a = document.createElement('a');
                a.href = url;
                a.download = 'export-controles-' + new Date().toISOString().split('T')[0] + '.xlsx';
                document.body.appendChild(a);
                a.click();
                window.URL.revokeObjectURL(url);
                
                // Afficher une notification de succès
                Swal.fire({
                    icon: 'success',
                    title: 'Export réussi',
                    text: 'Le fichier Excel a été généré avec succès.',
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000
                });
            },
            error: function(xhr) {
                console.error('Erreur lors de l\'export:', xhr);
                Swal.fire({
                    icon: 'error',
                    title: 'Erreur',
                    text: 'Une erreur est survenue lors de la génération du fichier Excel.',
                    confirmButtonText: 'OK'
                });
            },
            complete: function() {
                // Restaurer le bouton
                button.prop('disabled', false).html(originalText);
            }
        });
    });
});

// Fonction pour confirmer la suppression
function confirmDelete(event, formId) {
    event.preventDefault();
    
    Swal.fire({
        title: 'Êtes-vous sûr ?',
        text: "Vous ne pourrez pas revenir en arrière !",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Oui, supprimer !',
        cancelButtonText: 'Annuler'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById(formId).submit();
        }
    });
}

// Fonction pour afficher les détails d'un contrôle
function showControleDetails(controleId) {
    // Ici, vous pouvez implémenter une requête AJAX pour charger les détails
    // ou rediriger vers la page de détail
    window.location.href = '/controleur/' + controleId;
}

// Fonction pour filtrer les contrôles
function filterChecks() {
    const form = document.querySelector('form[method="GET"]');
    if (form) {
        form.submit();
    }
}
</script>

@if(session('success'))
<script>
    document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({
            icon: 'success',
            title: 'Succès',
            text: '{{ session("success") }}',
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000
        });
    });
</script>
@endif

@if($errors->any())
<script>
    document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({
            icon: 'error',
            title: 'Erreur',
            html: '{!! implode("<br>", $errors->all()) !!}',
            confirmButtonText: 'OK'
        });
    });
</script>
@endif
@endsection

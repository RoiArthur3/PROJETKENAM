@extends('layouts.app')

@section('title', 'Détail du Projet | KENAM SERVICES')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1 text-gray-800 fw-bold">
                <i class="fas fa-project-diagram text-success me-2"></i>{{ $projet->titre ?? 'Projet #' . ($projet->id ?? $id ?? '') }}
            </h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('projets.index') }}" class="text-decoration-none">Projets</a></li>
                    <li class="breadcrumb-item active">Détail</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('projets.edit', $projet->id ?? $id ?? 1) }}" class="btn btn-outline-warning">
                <i class="fas fa-edit me-2"></i>Modifier
            </a>
            <a href="{{ route('projets.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Retour
            </a>
        </div>
    </div>

    <!-- Gantt Chart Section -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white py-3 border-bottom">
            <h6 class="m-0 fw-bold text-primary">
                <i class="fas fa-chart-gantt me-2"></i>Planification du Projet (Diagramme de Gantt)
            </h6>
        </div>
        <div class="card-body" style="overflow-x: auto;">
            <div id="gantt"></div>
        </div>
    </div>

    <div class="row">
        <!-- Informations principales -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3 border-bottom" style="border-left: 4px solid #198754 !important;">
                    <h6 class="m-0 fw-bold text-success">
                        <i class="fas fa-info-circle me-2"></i>Informations du Projet
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small text-muted fw-bold">Titre</label>
                            <p class="mb-0 fw-semibold">{{ $projet->titre ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small text-muted fw-bold">Référence</label>
                            <p class="mb-0"><span class="badge bg-primary">{{ $projet->reference ?? 'PRJ-' . ($projet->id ?? $id ?? '000') }}</span></p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small text-muted fw-bold">Client</label>
                            <p class="mb-0 fw-semibold">{{ $projet->client->nom ?? $projet->client_nom ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small text-muted fw-bold">Responsable</label>
                            <p class="mb-0">{{ $projet->responsable->name ?? $projet->responsable_name ?? 'N/A' }}</p>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small text-muted fw-bold">Date de début</label>
                            <p class="mb-0">{{ isset($projet->date_debut) && $projet->date_debut ? \Carbon\Carbon::parse($projet->date_debut)->format('d/m/Y') : 'N/A' }}</p>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small text-muted fw-bold">Échéance</label>
                            <p class="mb-0">{{ isset($projet->echeance) && $projet->echeance ? \Carbon\Carbon::parse($projet->echeance)->format('d/m/Y') : 'N/A' }}</p>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small text-muted fw-bold">Statut</label>
                            @php
                                $statusConfig = [
                                    'en_cours' => ['bg' => 'primary', 'label' => 'En cours'],
                                    'termine' => ['bg' => 'success', 'label' => 'Terminé'],
                                    'en_attente' => ['bg' => 'warning', 'label' => 'En attente'],
                                    'annule' => ['bg' => 'danger', 'label' => 'Annulé'],
                                ];
                                $status = $statusConfig[$projet->statut ?? 'en_cours'] ?? $statusConfig['en_cours'];
                            @endphp
                            <p class="mb-0"><span class="badge bg-{{ $status['bg'] }}">{{ $status['label'] }}</span></p>
                        </div>
                        <div class="col-12">
                            <label class="form-label small text-muted fw-bold">Description</label>
                            <p class="mb-0">{{ $projet->description ?? 'Aucune description disponible.' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Avancement -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3 border-bottom" style="border-left: 4px solid #0dcaf0 !important;">
                    <h6 class="m-0 fw-bold text-info">
                        <i class="fas fa-tasks me-2"></i>Avancement
                    </h6>
                </div>
                <div class="card-body">
                    @php $avancement = $projet->avancement ?? 0; @endphp
                    <div class="d-flex justify-content-between mb-2">
                        <span class="fw-bold">Progression globale</span>
                        <span class="fw-bold">{{ $avancement }}%</span>
                    </div>
                    <div class="progress" style="height: 20px;">
                        <div class="progress-bar bg-success" role="progressbar" style="width: {{ $avancement }}%"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Budget -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3 border-bottom" style="border-left: 4px solid #ffc107 !important;">
                    <h6 class="m-0 fw-bold text-warning">
                        <i class="fas fa-coins me-2"></i>Budget
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Budget total</span>
                        <span class="fw-bold">{{ number_format($projet->budget ?? 0, 0, ',', ' ') }} FCFA</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Dépensé</span>
                        <span class="fw-bold text-danger">{{ number_format($projet->depenses ?? 0, 0, ',', ' ') }} FCFA</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between">
                        <span>Restant</span>
                        <span class="fw-bold text-success">{{ number_format(($projet->budget ?? 0) - ($projet->depenses ?? 0), 0, ',', ' ') }} FCFA</span>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3 border-bottom" style="border-left: 4px solid #0d6efd !important;">
                    <h6 class="m-0 fw-bold text-primary">
                        <i class="fas fa-cogs me-2"></i>Actions
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('projets.edit', $projet->id ?? $id ?? 1) }}" class="btn btn-outline-warning">
                            <i class="fas fa-edit me-2"></i>Modifier le projet
                        </a>
                        <a href="{{ route('projets.index') }}" class="btn btn-outline-info">
                            <i class="fas fa-list me-2"></i>Liste des projets
                        </a>
                        <button type="button" class="btn btn-outline-danger">
                            <i class="fas fa-trash me-2"></i>Supprimer
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.card {
    border-radius: 0.5rem;
}

.breadcrumb {
    font-size: 0.875rem;
}

.breadcrumb-item a {
    color: #198754;
}

.breadcrumb-item a:hover {
    color: #145c32;
}

.progress {
    border-radius: 0.5rem;
}

.progress-bar {
    border-radius: 0.5rem;
}

/* Frappe Gantt Styles */
.gantt-container {
    background: #fff;
    border-radius: 0.5rem;
    overflow: hidden;
}

.gantt-container svg {
    background: #f8f9fa;
}
</style>

<!-- Frappe Gantt Library -->
<script src="https://cdn.jsdelivr.net/npm/frappe-gantt@0.6.1/dist/frappe-gantt.min.js"></script>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/frappe-gantt@0.6.1/dist/frappe-gantt.css">

<script>
    // Helper function to format date
    function formatDate(date) {
        if (typeof date === 'string') return date;
        const d = new Date(date);
        const month = String(d.getMonth() + 1).padStart(2, '0');
        const day = String(d.getDate()).padStart(2, '0');
        const year = d.getFullYear();
        return `${year}-${month}-${day}`;
    }

    document.addEventListener('DOMContentLoaded', function() {
        const projet = {
            id: {{ $projet->id ?? 1 }},
            titre: "{{ $projet->titre ?? 'Projet' }}",
            dateDebut: "{{ isset($projet->date_debut) && $projet->date_debut ? \Carbon\Carbon::parse($projet->date_debut)->format('Y-m-d') : date('Y-m-d') }}",
            dateFin: "{{ isset($projet->date_fin_prevue) && $projet->date_fin_prevue ? \Carbon\Carbon::parse($projet->date_fin_prevue)->format('Y-m-d') : \Carbon\Carbon::parse($projet->date_debut ?? date('Y-m-d'))->addDays(30)->format('Y-m-d') }}",
            progression: {{ $projet->avancement ?? 0 }}
        };

        // Calculer les phases du projet
        const dateDebut = new Date(projet.dateDebut);
        const dateFin = new Date(projet.dateFin);
        const dureeTotal = Math.ceil((dateFin - dateDebut) / (1000 * 60 * 60 * 24));
        
        const dureePlanification = Math.ceil(dureeTotal * 0.1);
        const dureeExecution = Math.ceil(dureeTotal * 0.7);
        const dureeClosing = Math.ceil(dureeTotal * 0.2);

        const dateDebutExecution = new Date(dateDebut);
        dateDebutExecution.setDate(dateDebutExecution.getDate() + dureePlanification);

        const dateDebutClosing = new Date(dateDebutExecution);
        dateDebutClosing.setDate(dateDebutClosing.getDate() + dureeExecution);

        // Données du Gantt
        const tasks = [
            {
                id: 'planning',
                name: '<i class="fas fa-tasks"></i> Planification & Préparation',
                start: projet.dateDebut,
                end: formatDate(dateDebutExecution),
                progress: Math.min(100, projet.progression),
                dependencies: '',
                custom_class: 'bar-planning'
            },
            {
                id: 'execution',
                name: '<i class="fas fa-cogs"></i> Exécution du Projet',
                start: formatDate(dateDebutExecution),
                end: formatDate(dateDebutClosing),
                progress: Math.max(0, Math.min(100, projet.progression - 10)),
                dependencies: 'planning',
                custom_class: 'bar-execution'
            },
            {
                id: 'closing',
                name: '<i class="fas fa-flag-checkered"></i> Clôture & Bilan',
                start: formatDate(dateDebutClosing),
                end: projet.dateFin,
                progress: 0,
                dependencies: 'execution',
                custom_class: 'bar-closing'
            }
        ];

        // Initialiser le Gantt
        const gantt = new Gantt('#gantt', tasks, {
            header_height: 50,
            column_width: 30,
            step: 24,
            view_modes: ['Quarter Day', 'Half Day', 'Day', 'Week', 'Month'],
            bar_height: 30,
            bar_corner_radius: 3,
            arrow_curve: 5,
            padding: 18,
            view_mode: 'Week',
            date_format: 'YYYY-MM-DD',
            popup_trigger: 'click',
            custom_popup_html: null,
            language: 'fr'
        });

        gantt.change_view_mode('Week');
    });
</script>

<style>
    .gantt {
        background: #f8f9fa !important;
    }

    .bar-planning {
        background-color: #0dcaf0 !important;
    }

    .bar-execution {
        background-color: #198754 !important;
    }

    .bar-closing {
        background-color: #ffc107 !important;
    }

    .gantt-container {
        border-radius: 0.5rem;
    }
</style>
@endsection

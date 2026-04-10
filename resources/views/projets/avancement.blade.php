@extends('layouts.app')

@section('title', 'Suivi d\'Avancement - KENAM SERVICES')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-tasks me-2 text-primary"></i>Suivi d'Avancement des Projets
            </h1>
            <p class="text-muted mb-0">Suivi en temps réel de l'exécution des prestations</p>
        </div>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#updateModal">
            <i class="fas fa-plus me-2"></i>Mettre à Jour
        </button>
    </div>

    <!-- Sélection Projet -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-bold">Sélectionner un Projet</label>
                    <select class="form-select" id="projetSelect" onchange="chargerProjet()">
                        <option value="">-- Choisir un projet --</option>
                        <option value="PRJ-2024-015">PRJ-2024-015 - NESTLE CI (Abidjan → Korhogo)</option>
                        <option value="PRJ-2024-018">PRJ-2024-018 - PETROCI (Abidjan → San-Pédro)</option>
                        <option value="PRJ-2024-020">PRJ-2024-020 - BOLLORE CI (Abidjan → Bouaké)</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold">Statut</label>
                    <select class="form-select">
                        <option>Tous</option>
                        <option>En cours</option>
                        <option>En retard</option>
                        <option>Critique</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">&nbsp;</label>
                    <button class="btn btn-primary w-100">
                        <i class="fas fa-filter me-2"></i>Filtrer
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Détails du Projet Sélectionné -->
    <div class="row mb-4" id="detailsProjet" style="display: none;">
        <div class="col-lg-8">
            <!-- Informations Générales -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h6 class="m-0 fw-bold">
                        <i class="fas fa-info-circle me-2"></i>Informations du Projet
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <strong>N° Projet:</strong> <span id="numProjet">PRJ-2024-015</span>
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Client:</strong> <span id="clientProjet">NESTLE COTE D'IVOIRE</span>
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Contrat:</strong> <a href="{{ route('commercial.contrats.index') }}" id="contratProjet">CTR-2024-002</a>
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Trajet:</strong> <span id="trajetProjet">Abidjan → Korhogo</span>
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Date Début:</strong> <span id="dateDebut">01/11/2024</span>
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Date Fin Prévue:</strong> <span id="dateFin">15/11/2024</span>
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Chef de Projet:</strong> <span id="chefProjet">Kouassi Jean-Marc</span>
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong>Budget:</strong> <span id="budgetProjet" class="fw-bold text-success">15,000,000 FCFA</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Avancement Global -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-success text-white">
                    <h6 class="m-0 fw-bold">
                        <i class="fas fa-chart-line me-2"></i>Avancement Global
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="fw-bold">Progression Globale</span>
                            <span class="fw-bold text-danger" id="pourcentageGlobal">35%</span>
                        </div>
                        <div class="progress" style="height: 25px;">
                            <div class="progress-bar bg-danger" id="barreGlobale" style="width: 35%">35%</div>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-md-4 text-center">
                            <div class="border rounded p-3">
                                <i class="fas fa-hourglass-start fa-2x text-primary mb-2"></i>
                                <div class="fw-bold">Temps Écoulé</div>
                                <div class="h5 mb-0">7 jours</div>
                            </div>
                        </div>
                        <div class="col-md-4 text-center">
                            <div class="border rounded p-3">
                                <i class="fas fa-hourglass-end fa-2x text-warning mb-2"></i>
                                <div class="fw-bold">Temps Restant</div>
                                <div class="h5 mb-0 text-danger">1 jour</div>
                            </div>
                        </div>
                        <div class="col-md-4 text-center">
                            <div class="border rounded p-3">
                                <i class="fas fa-calendar-check fa-2x text-success mb-2"></i>
                                <div class="fw-bold">Durée Totale</div>
                                <div class="h5 mb-0">15 jours</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Étapes du Projet -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-info text-white">
                    <h6 class="m-0 fw-bold">
                        <i class="fas fa-list-check me-2"></i>Étapes du Projet
                    </h6>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <div class="timeline-item">
                            <div class="timeline-marker bg-success">
                                <i class="fas fa-check text-white"></i>
                            </div>
                            <div class="timeline-content">
                                <h6 class="mb-1">Préparation & Planification</h6>
                                <p class="text-muted mb-1">Affectation ressources, briefing équipe</p>
                                <small class="text-success"><i class="fas fa-check-circle"></i> Terminé le 01/11/2024</small>
                                <div class="progress mt-2" style="height: 6px;">
                                    <div class="progress-bar bg-success" style="width: 100%"></div>
                                </div>
                            </div>
                        </div>

                        <div class="timeline-item">
                            <div class="timeline-marker bg-warning">
                                <i class="fas fa-spinner text-white"></i>
                            </div>
                            <div class="timeline-content">
                                <h6 class="mb-1">Chargement Marchandises</h6>
                                <p class="text-muted mb-1">Entrepôt Yopougon - Abidjan</p>
                                <small class="text-warning"><i class="fas fa-clock"></i> En cours depuis le 03/11/2024</small>
                                <div class="progress mt-2" style="height: 6px;">
                                    <div class="progress-bar bg-warning" style="width: 60%"></div>
                                </div>
                            </div>
                        </div>

                        <div class="timeline-item">
                            <div class="timeline-marker bg-secondary">
                                <i class="fas fa-circle text-white"></i>
                            </div>
                            <div class="timeline-content">
                                <h6 class="mb-1">Transport Abidjan → Korhogo</h6>
                                <p class="text-muted mb-1">Trajet principal (650 km)</p>
                                <small class="text-muted"><i class="fas fa-hourglass"></i> Prévu pour le 06/11/2024</small>
                                <div class="progress mt-2" style="height: 6px;">
                                    <div class="progress-bar bg-secondary" style="width: 0%"></div>
                                </div>
                            </div>
                        </div>

                        <div class="timeline-item">
                            <div class="timeline-marker bg-secondary">
                                <i class="fas fa-circle text-white"></i>
                            </div>
                            <div class="timeline-content">
                                <h6 class="mb-1">Livraison & Déchargement</h6>
                                <p class="text-muted mb-1">Site client Korhogo</p>
                                <small class="text-muted"><i class="fas fa-hourglass"></i> Prévu pour le 14/11/2024</small>
                                <div class="progress mt-2" style="height: 6px;">
                                    <div class="progress-bar bg-secondary" style="width: 0%"></div>
                                </div>
                            </div>
                        </div>

                        <div class="timeline-item">
                            <div class="timeline-marker bg-secondary">
                                <i class="fas fa-circle text-white"></i>
                            </div>
                            <div class="timeline-content">
                                <h6 class="mb-1">Clôture & Validation</h6>
                                <p class="text-muted mb-1">Signature BL, rapport final</p>
                                <small class="text-muted"><i class="fas fa-hourglass"></i> Prévu pour le 15/11/2024</small>
                                <div class="progress mt-2" style="height: 6px;">
                                    <div class="progress-bar bg-secondary" style="width: 0%"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Incidents & Alertes -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-danger text-white">
                    <h6 class="m-0 fw-bold">
                        <i class="fas fa-exclamation-triangle me-2"></i>Incidents & Alertes
                    </h6>
                </div>
                <div class="card-body">
                    <div class="alert alert-danger">
                        <strong><i class="fas fa-clock"></i> Retard Critique</strong>
                        <p class="mb-0">Le projet accuse un retard de 6 jours par rapport au planning initial.</p>
                    </div>
                    <div class="alert alert-warning">
                        <strong><i class="fas fa-exclamation-circle"></i> Alerte Météo</strong>
                        <p class="mb-0">Fortes pluies prévues sur l'axe Bouaké-Korhogo.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Ressources Affectées -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-warning text-dark">
                    <h6 class="m-0 fw-bold">
                        <i class="fas fa-truck me-2"></i>Ressources Affectées
                    </h6>
                </div>
                <div class="card-body">
                    <h6 class="fw-bold mb-3">Véhicules</h6>
                    <ul class="list-unstyled">
                        @forelse(($vehicules ?? []) as $veh)
                        <li class="mb-2">
                            <i class="fas fa-truck text-primary"></i>
                            <a href="{{ route('parc.vehicules') }}">{{ $veh->immatriculation }}</a>
                            - {{ $veh->type ?? 'Véhicule' }}
                        </li>
                        @empty
                        <li class="mb-2 text-muted">Aucun véhicule affecté</li>
                        @endforelse
                    </ul>

                    <h6 class="fw-bold mb-3 mt-4">Personnel</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <i class="fas fa-user text-success"></i>
                            <a href="{{ route('rh.agents.index') }}">Koné Mamadou</a> - Chauffeur
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-user text-success"></i>
                            <a href="{{ route('rh.agents.index') }}">Diallo Ibrahim</a> - Chauffeur
                        </li>
                    </ul>

                    <a href="{{ route('projets.index') }}" class="btn btn-sm btn-outline-primary w-100 mt-3">
                        <i class="fas fa-list me-2"></i>Liste des projets
                    </a>
                </div>
            </div>

            <!-- Budget & Coûts -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-info text-white">
                    <h6 class="m-0 fw-bold">
                        <i class="fas fa-coins me-2"></i>Budget & Coûts
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Budget Total:</span>
                        <strong>15,000,000 FCFA</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Dépensé:</span>
                        <strong class="text-warning">8,500,000 FCFA</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Restant:</span>
                        <strong class="text-success">6,500,000 FCFA</strong>
                    </div>
                    <hr>
                    <div class="progress" style="height: 20px;">
                        <div class="progress-bar bg-warning" style="width: 57%">57%</div>
                    </div>
                </div>
            </div>

            <!-- Actions Rapides -->
            <div class="card shadow-sm">
                <div class="card-body">
                    <button type="button" class="btn btn-success w-100 mb-2" data-bs-toggle="modal" data-bs-target="#updateModal">
                        <i class="fas fa-plus me-2"></i>Ajouter une Mise à Jour
                    </button>
                    <a href="{{ route('projets.rapports') }}" class="btn btn-warning w-100 mb-2">
                        <i class="fas fa-file-alt me-2"></i>Générer Rapport
                    </a>
                    <a href="{{ route('projets.cloture.projet', $projet) }}" class="btn btn-info w-100">
                        <i class="fas fa-flag-checkered me-2"></i>Clôturer le Projet
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.timeline {
    position: relative;
    padding-left: 40px;
}
.timeline::before {
    content: '';
    position: absolute;
    left: 15px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #dee2e6;
}
.timeline-item {
    position: relative;
    margin-bottom: 30px;
}
.timeline-marker {
    position: absolute;
    left: -32px;
    width: 30px;
    height: 30px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}
.timeline-content {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 8px;
}
</style>

<script>
function chargerProjet() {
    const select = document.getElementById('projetSelect');
    if (select.value) {
        document.getElementById('detailsProjet').style.display = 'block';
    } else {
        document.getElementById('detailsProjet').style.display = 'none';
    }
}
</script>
@endsection

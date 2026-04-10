@extends('layouts.app')

@section('title', 'Historique - Contrôle & Audit - KENAM SERVICES')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-clock me-2 text-primary"></i>Historique des Contrôles
            </h1>
            <p class="text-muted mb-0">Historique complet des audits et vérifications</p>
        </div>
        <button class="btn btn-primary">
            <i class="fas fa-download me-2"></i>Exporter
        </button>
    </div>

    <!-- Filtres -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Type</label>
                    <select class="form-select">
                        <option>Tous</option>
                        <option>Vérification</option>
                        <option>Audit qualité</option>
                        <option>Contrôle sécurité</option>
                        <option>Validation technique</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Résultat</label>
                    <select class="form-select">
                        <option>Tous</option>
                        <option>Conforme</option>
                        <option>Non conforme</option>
                        <option>En attente</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Période</label>
                    <input type="date" class="form-control" placeholder="Du">
                </div>
                <div class="col-md-3">
                    <label class="form-label">&nbsp;</label>
                    <button class="btn btn-primary w-100">
                        <i class="fas fa-search me-2"></i>Filtrer
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Historique -->
    <div class="card">
        <div class="card-header">
            <h6 class="m-0 fw-bold text-primary">Historique des contrôles</h6>
        </div>
        <div class="card-body">
            <div class="timeline" style="max-height: 600px; overflow-y: auto;">
                <div class="mb-4 pb-3 border-bottom">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="flex-grow-1">
                            <div class="d-flex align-items-center mb-2">
                                <span class="badge bg-success me-2">
                                    <i class="fas fa-check-circle"></i>
                                </span>
                                <h6 class="mb-0">Audit qualité - Module Transport</h6>
                            </div>
                            <p class="small text-muted mb-1">Contrôle des procédures de livraison • Référence: AUD-2024-015</p>
                            <small class="text-muted">Résultat: Conforme • Responsable: Jean Dupont • 05/11/2024</small>
                        </div>
                        <small class="text-muted">Il y a 2 jours</small>
                    </div>
                </div>

                <div class="mb-4 pb-3 border-bottom">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="flex-grow-1">
                            <div class="d-flex align-items-center mb-2">
                                <span class="badge bg-warning me-2">
                                    <i class="fas fa-exclamation-triangle"></i>
                                </span>
                                <h6 class="mb-0">Vérification sécurité - Entrepôt</h6>
                            </div>
                            <p class="small text-muted mb-1">Contrôle des équipements de protection • Référence: VER-2024-008</p>
                            <small class="text-muted">Résultat: Non conforme • Action corrective requise • Responsable: Marie Curie • 03/11/2024</small>
                        </div>
                        <small class="text-muted">Il y a 4 jours</small>
                    </div>
                </div>

                <div class="mb-4 pb-3 border-bottom">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="flex-grow-1">
                            <div class="d-flex align-items-center mb-2">
                                <span class="badge bg-info me-2">
                                    <i class="fas fa-info-circle"></i>
                                </span>
                                <h6 class="mb-0">Validation technique - Dashboard RH</h6>
                            </div>
                            <p class="small text-muted mb-1">Validation des performances • Référence: VAL-TECH-2024-003</p>
                            <small class="text-muted">Résultat: En attente • Responsable: Équipe Dev • 01/11/2024</small>
                        </div>
                        <small class="text-muted">Il y a 6 jours</small>
                    </div>
                </div>

                <div class="mb-4 pb-3 border-bottom">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="flex-grow-1">
                            <div class="d-flex align-items-center mb-2">
                                <span class="badge bg-success me-2">
                                    <i class="fas fa-check-circle"></i>
                                </span>
                                <h6 class="mb-0">Contrôle conformité - Module Fournisseurs</h6>
                            </div>
                            <p class="small text-muted mb-1">Vérification des contrats • Référence: CTRL-2024-012</p>
                            <small class="text-muted">Résultat: Conforme • Responsable: Sophie Martin • 28/10/2024</small>
                        </div>
                        <small class="text-muted">Il y a 10 jours</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.timeline {
    position: relative;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 15px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #e9ecef;
}

.timeline > div {
    position: relative;
    padding-left: 40px;
}

.timeline > div::before {
    content: '';
    position: absolute;
    left: 7px;
    top: 10px;
    width: 16px;
    height: 16px;
    border-radius: 50%;
    background: #6c757d;
}
</style>
@endsection

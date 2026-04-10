@extends('layouts.app')

@section('title', 'Contrats - KENAM SERVICES')

@section('content')
<x-list-layout
    title="Gestion des Contrats"
    icon="fa-file-contract"
    :createRoute="null"
    createLabel="Nouveau Contrat"
    :exportable="true"
    searchPlaceholder="N° contrat, client, référence..."
    :count="28"
>
    <!-- KPIs -->
    <x-slot name="kpis">
        <x-kpi-card
            title="Contrats Actifs"
            value="28"
            icon="fa-check-circle"
            color="success"
            subtitle="385M FCFA"
        />
        <x-kpi-card
            title="À Renouveler"
            value="5"
            icon="fa-clock"
            color="warning"
            subtitle="Dans 30 jours"
        />
        <x-kpi-card
            title="En Cours"
            value="18"
            icon="fa-play-circle"
            color="info"
            subtitle="Projets actifs"
        />
        <x-kpi-card
            title="Résiliés"
            value="3"
            icon="fa-times-circle"
            color="danger"
            subtitle="Ce trimestre"
        />
    </x-slot>

    <!-- Filtres supplémentaires -->
    <x-slot name="filters">
        <div class="col-md-2">
            <label class="form-label">Statut</label>
            <select class="form-select">
                <option value="">Tous</option>
                <option value="actif">Actif</option>
                <option value="en_cours">En cours</option>
                <option value="a_renouveler">À renouveler</option>
                <option value="resilie">Résilié</option>
                <option value="termine">Terminé</option>
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label">Type</label>
            <select class="form-select">
                <option value="">Tous types</option>
                <option value="maintenance">Maintenance</option>
                <option value="reparation">Réparation</option>
                <option value="installation">Installation</option>
                <option value="formation">Formation</option>
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label">Échéance</label>
            <select class="form-select">
                <option value="">Toutes</option>
                <option value="ce_mois">Ce mois</option>
                <option value="prochains_30">30 jours</option>
                <option value="prochains_90">90 jours</option>
                <option value="depasse">Dépassées</option>
            </select>
        </div>
    </x-slot>

    <!-- Actions personnalisées -->
    <x-slot name="customActions">
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#nouveauContratModal">
            <i class="fas fa-plus me-1"></i>Nouveau Contrat
        </button>
    </x-slot>

    <!-- Table Header -->
    <x-slot name="tableHeader">
        <tr>
            <th>N° Contrat</th>
            <th>Client</th>
            <th>Type</th>
            <th>Montant</th>
            <th>Date début</th>
            <th>Date fin</th>
            <th>Statut</th>
            <th>Actions</th>
        </tr>
    </x-slot>

    <!-- Table Body -->
    <x-slot name="tableBody">
        <tr>
            <td>
                <span class="fw-bold text-primary">CTR-2024-015</span>
            </td>
            <td>
                <div class="d-flex align-items-center">
                    <div class="avatar-circle bg-primary me-2">T</div>
                    <span>TechnoPlus SA</span>
                </div>
            </td>
            <td>
                <span class="badge bg-info">Maintenance</span>
            </td>
            <td class="fw-bold">45,000,000 FCFA</td>
            <td>15/01/2024</td>
            <td>15/01/2025</td>
            <td>
                <span class="badge bg-success">Actif</span>
            </td>
            <td>
                <div class="btn-group">
                    <button class="btn btn-sm btn-outline-primary" title="Voir détails">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-warning" title="Modifier">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-info" title="Renouveler">
                        <i class="fas fa-redo"></i>
                    </button>
                </div>
            </td>
        </tr>
        <tr>
            <td>
                <span class="fw-bold text-primary">CTR-2024-014</span>
            </td>
            <td>
                <div class="d-flex align-items-center">
                    <div class="avatar-circle bg-success me-2">L</div>
                    <span>Logistics Pro</span>
                </div>
            </td>
            <td>
                <span class="badge bg-warning">Réparation</span>
            </td>
            <td class="fw-bold">28,500,000 FCFA</td>
            <td>03/01/2024</td>
            <td>03/01/2025</td>
            <td>
                <span class="badge bg-success">Actif</span>
            </td>
            <td>
                <div class="btn-group">
                    <button class="btn btn-sm btn-outline-primary" title="Voir détails">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-warning" title="Modifier">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-info" title="Renouveler">
                        <i class="fas fa-redo"></i>
                    </button>
                </div>
            </td>
        </tr>
        <tr>
            <td>
                <span class="fw-bold text-primary">CTR-2024-013</span>
            </td>
            <td>
                <div class="d-flex align-items-center">
                    <div class="avatar-circle bg-info me-2">E</div>
                    <span>Energy Solutions</span>
                </div>
            </td>
            <td>
                <span class="badge bg-primary">Installation</span>
            </td>
            <td class="fw-bold">67,800,000 FCFA</td>
            <td>22/12/2023</td>
            <td>22/12/2024</td>
            <td>
                <span class="badge bg-warning">À renouveler</span>
            </td>
            <td>
                <div class="btn-group">
                    <button class="btn btn-sm btn-outline-primary" title="Voir détails">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-warning" title="Modifier">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-success" title="Renouveler maintenant">
                        <i class="fas fa-redo"></i>
                    </button>
                </div>
            </td>
        </tr>
        <tr>
            <td>
                <span class="fw-bold text-primary">CTR-2024-012</span>
            </td>
            <td>
                <div class="d-flex align-items-center">
                    <div class="avatar-circle bg-warning me-2">M</div>
                    <span>Maintenance Plus</span>
                </div>
            </td>
            <td>
                <span class="badge bg-info">Maintenance</span>
            </td>
            <td class="fw-bold">32,400,000 FCFA</td>
            <td>10/12/2023</td>
            <td>10/12/2024</td>
            <td>
                <span class="badge bg-success">Actif</span>
            </td>
            <td>
                <div class="btn-group">
                    <button class="btn btn-sm btn-outline-primary" title="Voir détails">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-warning" title="Modifier">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-info" title="Renouveler">
                        <i class="fas fa-redo"></i>
                    </button>
                </div>
            </td>
        </tr>
        <tr>
            <td>
                <span class="fw-bold text-primary">CTR-2024-011</span>
            </td>
            <td>
                <div class="d-flex align-items-center">
                    <div class="avatar-circle bg-danger me-2">S</div>
                    <span>SupplyChain CI</span>
                </div>
            </td>
            <td>
                <span class="badge bg-success">Formation</span>
            </td>
            <td class="fw-bold">18,750,000 FCFA</td>
            <td>05/12/2023</td>
            <td>05/12/2024</td>
            <td>
                <span class="badge bg-danger">Résilié</span>
            </td>
            <td>
                <div class="btn-group">
                    <button class="btn btn-sm btn-outline-primary" title="Voir détails">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button class="btn btn-sm btn-outline-secondary" title="Archiver">
                        <i class="fas fa-archive"></i>
                    </button>
                </div>
            </td>
        </tr>
    </x-slot>
</x-list-layout>

<!-- Modal Nouveau Contrat (conservé) -->
<div class="modal fade" id="nouveauContratModal" tabindex="-1" aria-labelledby="nouveauContratModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="nouveauContratModalLabel">
                    <i class="fas fa-plus me-2"></i>Nouveau Contrat
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Client *</label>
                            <select class="form-select" required>
                                <option value="">Sélectionner un client</option>
                                <option>TechnoPlus SA</option>
                                <option>Logistics Pro</option>
                                <option>Energy Solutions</option>
                                <option>Maintenance Plus</option>
                                <option>SupplyChain CI</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Type de contrat *</label>
                            <select class="form-select" required>
                                <option value="">Sélectionner le type</option>
                                <option value="maintenance">Maintenance</option>
                                <option value="reparation">Réparation</option>
                                <option value="installation">Installation</option>
                                <option value="formation">Formation</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Date de début *</label>
                            <input type="date" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Date de fin *</label>
                            <input type="date" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Montant HT (FCFA) *</label>
                            <input type="number" class="form-control" placeholder="0" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Fréquence de paiement</label>
                            <select class="form-select">
                                <option value="mensuel">Mensuel</option>
                                <option value="trimestriel">Trimestriel</option>
                                <option value="annuel">Annuel</option>
                                <option value="unique">Paiement unique</option>
                            </select>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label fw-bold">Objet du contrat *</label>
                            <textarea class="form-control" rows="3" placeholder="Description détaillée du contrat..." required></textarea>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label fw-bold">Conditions particulières</label>
                            <textarea class="form-control" rows="3" placeholder="Conditions de paiement, pénalités, etc."></textarea>
                        </div>
                        <div class="col-12">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="creerProjet">
                                <label class="form-check-label" for="creerProjet">
                                    Créer automatiquement un projet à partir de ce contrat
                                </label>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="button" class="btn btn-primary">
                    <i class="fas fa-save me-2"></i>Enregistrer
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@extends('layouts.app')

@section('title', 'Pointage d\'Engin Modal | KENAM SERVICES')

@section('content')
<div class="container-fluid py-4">
    <!-- En-tête -->
    <div class="row mb-4">
        <div class="col">
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-cogs me-2 text-info"></i>Pointage d'Engin
            </h1>
            <p class="text-muted mb-0">Cliquez sur le bouton pour ouvrir le formulaire de pointage en modal</p>
        </div>
        <div class="col-auto">
            <button type="button" class="btn btn-primary btn-lg" data-bs-toggle="modal" data-bs-target="#pointageEnginModal">
                <i class="fas fa-plus me-2"></i>Ouvrir le formulaire de pointage
            </button>
        </div>
    </div>

    <!-- Informations -->
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i>Comment utiliser ce modal
                    </h6>
                </div>
                <div class="card-body">
                    <ol>
                        <li>Cliquez sur le bouton "Ouvrir le formulaire de pointage" ci-dessus</li>
                        <li>Sélectionnez un projet dans la liste déroulante</li>
                        <li>Choisissez un engin associé au projet</li>
                        <li>Sélectionnez le fournisseur (Kenam ou externe)</li>
                        <li>Utilisez le pointage automatique ou saisissez manuellement les heures</li>
                        <li>Ajoutez une description si nécessaire</li>
                        <li>Cliquez sur "Enregistrer" pour sauvegarder</li>
                    </ol>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-chart-line me-2"></i>Statistiques rapides
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-4">
                            <div class="text-primary">
                                <i class="fas fa-calendar-day fa-2x"></i>
                                <h4 class="mt-2">{{ $todayPointages ?? 0 }}</h4>
                                <small>Pointages aujourd'hui</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="text-success">
                                <i class="fas fa-clock fa-2x"></i>
                                <h4 class="mt-2">{{ number_format($totalHours ?? 0, 1) }}h</h4>
                                <small>Heures totales</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="text-info">
                                <i class="fas fa-money-bill-wave fa-2x"></i>
                                <h4 class="mt-2">{{ number_format($totalCost ?? 0, 0) }}</h4>
                                <small>Coût total (FCFA)</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Derniers pointages -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">
                        <i class="fas fa-history me-2"></i>Derniers pointages enregistrés
                    </h6>
                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#pointageEnginModal">
                        <i class="fas fa-plus me-1"></i>Ajouter
                    </button>
                </div>
                <div class="card-body">
                    @if(isset($recentPointages) && $recentPointages->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Projet</th>
                                        <th>Engin</th>
                                        <th>Durée</th>
                                        <th>Coût</th>
                                        <th>Statut</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentPointages as $pointage)
                                        <tr>
                                            <td>{{ \Carbon\Carbon::parse($pointage->date_pointage)->format('d/m/Y') }}</td>
                                            <td>{{ $pointage->projet->titre ?? 'N/A' }}</td>
                                            <td>{{ $pointage->vehicle->immatriculation ?? 'N/A' }}</td>
                                            <td>{{ number_format($pointage->quantity ?? 0, 1) }}h</td>
                                            <td>{{ number_format($pointage->supplier_unit_cost * $pointage->quantity ?? 0, 0) }} FCFA</td>
                                            <td>
                                                <span class="badge bg-{{ $pointage->statut == 'validé' ? 'success' : 'warning' }}">
                                                    {{ $pointage->statut ?? 'En cours' }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                            <p class="text-muted">Aucun pointage enregistré aujourd'hui</p>
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#pointageEnginModal">
                                <i class="fas fa-plus me-2"></i>Créer le premier pointage
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Inclure le modal de pointage -->
@include('materiel.cost-control.engin-pointage-modal')

<style>
.card {
    border: none;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    transition: all 0.3s ease;
}

.card:hover {
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
}

.btn-lg {
    padding: 0.75rem 1.5rem;
    font-size: 1.125rem;
}

.table th {
    font-weight: 600;
    font-size: 0.875rem;
    border-top: none;
    background-color: #f8f9fa;
}

.table td {
    font-size: 0.875rem;
    vertical-align: middle;
}

ol li {
    margin-bottom: 0.5rem;
}

.text-primary, .text-success, .text-info {
    transition: all 0.3s ease;
}

.text-primary:hover, .text-success:hover, .text-info:hover {
    transform: translateY(-2px);
}
</style>

<script>
// Fonction pour afficher des alertes
function showAlert(type, message) {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
    alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    document.body.appendChild(alertDiv);
    
    // Auto-suppression après 5 secondes
    setTimeout(() => {
        if (alertDiv.parentNode) {
            alertDiv.remove();
        }
    }, 5000);
}

// Gestion de la soumission du modal
document.getElementById('pointageModalForm')?.addEventListener('submit', function(e) {
    e.preventDefault();
    
    const form = this;
    const submitBtn = form.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    
    // Validation minimale
    const projetId = document.getElementById('modal_projet_id').value;
    const vehicleId = document.getElementById('modal_vehicle_id').value;
    const supplierId = document.getElementById('modal_fournisseur_id').value;
    
    if (!projetId || !vehicleId || !supplierId) {
        showAlert('warning', 'Veuillez remplir tous les champs obligatoires');
        return;
    }
    
    // Vérifier les heures
    const heureDebut = document.getElementById('modal_heure_debut').value;
    const heureFin = document.getElementById('modal_heure_fin').value;
    const autoHeureDebut = document.getElementById('modal_auto_heure_debut').value;
    const autoHeureFin = document.getElementById('modal_auto_heure_fin').value;
    
    if ((!heureDebut || !heureFin) && (!autoHeureDebut || !autoHeureFin)) {
        showAlert('warning', 'Veuillez définir les heures de pointage (automatique ou manuel)');
        return;
    }
    
    // Afficher le chargement
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Enregistrement...';
    
    // Créer FormData
    const formData = new FormData(form);
    
    // Soumettre le formulaire
    fetch(form.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Erreur réseau');
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            // Fermer le modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('pointageEnginModal'));
            modal.hide();
            
            // Afficher le message de succès
            showAlert('success', data.message || 'Pointage enregistré avec succès!');
            
            // Recharger la page après un court délai pour voir les nouvelles données
            setTimeout(() => {
                window.location.reload();
            }, 1500);
        } else {
            showAlert('danger', data.message || 'Erreur lors de l\'enregistrement');
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        showAlert('danger', 'Erreur lors de l\'enregistrement. Veuillez réessayer.');
    })
    .finally(() => {
        // Restaurer le bouton
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
    });
});

// Animation au chargement
document.addEventListener('DOMContentLoaded', function() {
    const cards = document.querySelectorAll('.card');
    cards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        setTimeout(() => {
            card.style.transition = 'all 0.5s ease';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 100);
    });
});

// Mise à jour automatique des statistiques (optionnel)
function updateStats() {
    // Cette fonction peut être appelée périodiquement pour mettre à jour les statistiques
    // sans recharger toute la page
    fetch('/api/pointages/stats')
        .then(response => response.json())
        .then(data => {
            // Mettre à jour les éléments DOM avec les nouvelles données
            if (data.todayPointages !== undefined) {
                document.querySelector('.text-primary h4').textContent = data.todayPointages;
            }
            if (data.totalHours !== undefined) {
                document.querySelector('.text-success h4').textContent = data.totalHours + 'h';
            }
            if (data.totalCost !== undefined) {
                document.querySelector('.text-info h4').textContent = data.totalCost;
            }
        })
        .catch(error => {
            console.error('Erreur de mise à jour des statistiques:', error);
        });
}

// Mettre à jour les stats toutes les 30 secondes (optionnel)
// setInterval(updateStats, 30000);
</script>
@endsection

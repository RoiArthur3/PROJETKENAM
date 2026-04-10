@extends('layouts.app')

@section('title', 'Détails Proforma ' . $devis->reference . ' | KENAM SERVICES')

@section('content')
<x-dashboard-layout title="Détails de la Proforma" icon="fa-file-invoice-dollar" subtitle="Consultation et gestion de la proforma {{ $devis->reference }}">

    <!-- Actions principales -->
    <x-slot name="headerActions">
        <div class="d-flex gap-2">

            <button class="btn btn-primary" onclick="telechargerPDF()">
                <i class="fas fa-download me-1"></i>Télécharger PDF
            </button>
            <button class="btn btn-info" onclick="envoyerEmail()">
                <i class="fas fa-envelope me-1"></i>Envoyer par Email
            </button>
            <button class="btn btn-warning" onclick="modifierProforma()">
                <i class="fas fa-edit me-1"></i>Modifier
            </button>
            <a href="{{ route('commercial.proforma') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i>Retour à la liste
            </a>
        </div>
    </x-slot>

    <!-- Informations générales -->
    <div class="row mb-4">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-gradient-primary text-white">
                    <h6 class="mb-0">
                        <i class="fas fa-info-circle me-2"></i>Informations Générales
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Référence</label>
                                <div class="form-control-plaintext">{{ $devis->reference ?? 'DEV-0023' }}</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Client</label>
                                <div class="form-control-plaintext">{{ $devis->client_name ?? 'TechnoPlus SA' }}</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Statut</label>
                                <span class="badge bg-{{ $devis->statut === 'accepte' ? 'success' : ($devis->statut === 'refuse' ? 'danger' : 'warning') }}">
                                    {{ $devis->statut ?? 'en_attente' }}
                                </span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-bold">Date d'émission</label>
                                <div class="form-control-plaintext">{{ $devis->issue_date ? $devis->issue_date->format('d/m/Y') : date('d/m/Y') }}</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Date d'échéance</label>
                                <div class="form-control-plaintext">{{ $devis->due_date ? $devis->due_date->format('d/m/Y') : date('d/m/Y', strtotime('+30 days')) }}</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">Objet</label>
                                <div class="form-control-plaintext">{{ $devis->objet ?? 'Maintenance préventive et corrective des équipements informatiques' }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm">
                <div class="card-header bg-gradient-success text-white">
                    <h6 class="mb-0">
                        <i class="fas fa-calculator me-2"></i>Résumé Financier
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-12 mb-3">
                            <div class="border rounded p-3">
                                <div class="h4 text-primary mb-1">{{ number_format($devis->montant_ht ?? 2500000, 0, ',', ' ') }} FCFA</div>
                                <div class="text-muted small">Montant HT</div>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="border rounded p-2">
                                <div class="h6 text-info mb-1">{{ number_format(($devis->montant_ht ?? 2500000) * 0.18, 0, ',', ' ') }} FCFA</div>
                                <div class="text-muted small">TVA (18%)</div>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="border rounded p-2">
                                <div class="h6 text-success mb-1">{{ number_format(($devis->montant_ht ?? 2500000) * 1.18, 0, ',', ' ') }} FCFA</div>
                                <div class="text-muted small">Total TTC</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Détail des prestations -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-gradient-info text-white">
                    <h6 class="mb-0">
                        <i class="fas fa-list me-2"></i>Détail des Prestations
                    </h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>N°</th>
                                    <th>Description</th>
                                    <th class="text-center">Quantité</th>
                                    <th class="text-end">Prix Unit.</th>
                                    <th class="text-center">TVA</th>
                                    <th class="text-end">Total HT</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>1</td>
                                    <td>
                                        <strong>Maintenance préventive mensuelle</strong><br>
                                        <small class="text-muted">Visites techniques programmées avec contrôles complets des équipements</small>
                                    </td>
                                    <td class="text-center">12</td>
                                    <td class="text-end">150,000 FCFA</td>
                                    <td class="text-center">18%</td>
                                    <td class="text-end fw-bold">1,800,000 FCFA</td>
                                </tr>
                                <tr>
                                    <td>2</td>
                                    <td>
                                        <strong>Réparation d'urgence</strong><br>
                                        <small class="text-muted">Interventions d'urgence 24/7 pour pannes critiques</small>
                                    </td>
                                    <td class="text-center">1</td>
                                    <td class="text-end">500,000 FCFA</td>
                                    <td class="text-center">18%</td>
                                    <td class="text-end fw-bold">500,000 FCFA</td>
                                </tr>
                                <tr>
                                    <td>3</td>
                                    <td>
                                        <strong>Pièces de rechange</strong><br>
                                        <small class="text-muted">Fourniture de composants et pièces détachées</small>
                                    </td>
                                    <td class="text-center">1</td>
                                    <td class="text-end">200,000 FCFA</td>
                                    <td class="text-center">18%</td>
                                    <td class="text-end fw-bold">200,000 FCFA</td>
                                </tr>
                                <tr class="table-primary">
                                    <td colspan="5" class="text-end fw-bold">SOUS-TOTAL HT</td>
                                    <td class="text-end fw-bold">2,500,000 FCFA</td>
                                </tr>
                                <tr class="table-primary">
                                    <td colspan="5" class="text-end fw-bold">TVA (18%)</td>
                                    <td class="text-end fw-bold">450,000 FCFA</td>
                                </tr>
                                <tr class="table-success">
                                    <td colspan="5" class="text-end fw-bold fs-5">TOTAL TTC</td>
                                    <td class="text-end fw-bold fs-5 text-success">2,950,000 FCFA</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Historique et commentaires -->
    <div class="row mb-4">
        <div class="col-lg-6">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h6 class="mb-0 fw-bold text-primary">
                        <i class="fas fa-history me-2"></i>Historique des Actions
                    </h6>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <div class="timeline-item">
                            <div class="timeline-marker bg-primary"></div>
                            <div class="timeline-content">
                                <div class="d-flex justify-content-between">
                                    <strong>Création de la proforma</strong>
                                    <small class="text-muted">{{ now()->format('d/m/Y H:i') }}</small>
                                </div>
                                <p class="mb-0 small">Proforma créée par {{ auth()->user()->name ?? 'Utilisateur' }}</p>
                            </div>
                        </div>
                        <div class="timeline-item">
                            <div class="timeline-marker bg-info"></div>
                            <div class="timeline-content">
                                <div class="d-flex justify-content-between">
                                    <strong>Envoi au client</strong>
                                    <small class="text-muted">{{ now()->subDays(1)->format('d/m/Y H:i') }}</small>
                                </div>
                                <p class="mb-0 small">Proforma envoyée par email à TechnoPlus SA</p>
                            </div>
                        </div>
                        <div class="timeline-item">
                            <div class="timeline-marker bg-warning"></div>
                            <div class="timeline-content">
                                <div class="d-flex justify-content-between">
                                    <strong>Rappel automatique</strong>
                                    <small class="text-muted">{{ now()->subDays(7)->format('d/m/Y H:i') }}</small>
                                </div>
                                <p class="mb-0 small">Rappel envoyé - Échéance dans 23 jours</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h6 class="mb-0 fw-bold text-primary">
                        <i class="fas fa-comments me-2"></i>Notes et Commentaires
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <textarea class="form-control" rows="4" placeholder="Ajouter un commentaire..." id="nouveauCommentaire"></textarea>
                    </div>
                    <button class="btn btn-primary btn-sm" onclick="ajouterCommentaire()">
                        <i class="fas fa-plus me-1"></i>Ajouter Commentaire
                    </button>

                    <hr class="my-3">

                    <div class="comments-list">
                        <div class="comment-item mb-3">
                            <div class="d-flex justify-content-between">
                                <strong>{{ auth()->user()->name ?? 'Utilisateur' }}</strong>
                                <small class="text-muted">{{ now()->format('d/m/Y H:i') }}</small>
                            </div>
                            <p class="mb-0 small">Proforma préparée selon les spécifications demandées. Client informé des délais de livraison.</p>
                        </div>
                        <div class="comment-item">
                            <div class="d-flex justify-content-between">
                                <strong>Système</strong>
                                <small class="text-muted">{{ now()->subDays(1)->format('d/m/Y H:i') }}</small>
                            </div>
                            <p class="mb-0 small">Email de confirmation envoyé au client.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Actions secondaires -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body text-center">
                    <div class="btn-group" role="group">
                        <button class="btn btn-outline-success" onclick="accepterProforma()">
                            <i class="fas fa-check me-1"></i>Accepter
                        </button>
                        <button class="btn btn-outline-danger" onclick="refuserProforma()">
                            <i class="fas fa-times me-1"></i>Refuser
                        </button>
                        <button class="btn btn-outline-warning" onclick="mettreEnAttente()">
                            <i class="fas fa-clock me-1"></i>Mettre en Attente
                        </button>
                        <button class="btn btn-outline-secondary" onclick="dupliquerProforma()">
                            <i class="fas fa-copy me-1"></i>Dupliquer
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-dashboard-layout>

@push('scripts')
<script>


function telechargerPDF() {
    // Redirection vers le téléchargement PDF
    window.location.href = '/commercial/proforma/{{ $devis->id }}/pdf';
}

function envoyerEmail() {
    if (confirm('Voulez-vous envoyer cette proforma par email au client ?')) {
        alert('Fonctionnalité d\'envoi par email en cours de développement');
    }
}

function modifierProforma() {
    if (confirm('Voulez-vous modifier cette proforma ?')) {
        // Redirection vers la page d'édition
        window.location.href = '/commercial/proforma/{{ $devis->id }}/edit';
    }
}

function accepterProforma() {
    if (confirm('Êtes-vous sûr d\'accepter cette proforma ?')) {
        document.getElementById('statusUpdateForm').querySelector('input[name="statut"]').value = 'accepte';
        document.getElementById('statusUpdateForm').submit();
    }
}

function refuserProforma() {
    if (confirm('Êtes-vous sûr de refuser cette proforma ?')) {
        alert('Statut mis à jour : Refusée');
    }
}

function mettreEnAttente() {
    alert('Statut mis à jour : En Attente');
}

function dupliquerProforma() {
    if (confirm('Créer une copie de cette proforma ?')) {
        alert('Fonctionnalité en cours de développement');
    }
}

function ajouterCommentaire() {
    const commentaire = document.getElementById('nouveauCommentaire').value;
    if (commentaire.trim()) {
        alert('Commentaire ajouté : ' + commentaire);
        document.getElementById('nouveauCommentaire').value = '';
    }
}
</script>
@endpush

<style>
.timeline {
    position: relative;
    padding-left: 30px;
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

.timeline-item {
    position: relative;
    margin-bottom: 20px;
}

.timeline-marker {
    position: absolute;
    left: -22px;
    top: 5px;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    border: 2px solid #fff;
    box-shadow: 0 0 0 2px #e9ecef;
}

.timeline-content {
    background: #f8f9fa;
    padding: 10px 15px;
    border-radius: 8px;
    border-left: 3px solid #007bff;
}

.comment-item {
    padding: 10px;
    background: #f8f9fa;
    border-radius: 6px;
    margin-bottom: 10px;
}

.table th {
    font-weight: 600;
    font-size: 0.875rem;
}

.table-hover tbody tr:hover {
    background-color: rgba(13, 110, 253, 0.05);
}
</style>
@endsection

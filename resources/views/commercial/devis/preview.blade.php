@extends('layouts.app')

@section('title', 'Aperçu Proforma - KENAM SERVICES')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-eye me-2 text-info"></i>Aperçu de la Proforma
            </h1>
            <p class="text-muted mb-0">Prévisualisation avant impression</p>
        </div>
        <div class="btn-group">
            <button class="btn btn-success" onclick="window.print()">
                <i class="fas fa-print me-2"></i>Imprimer
            </button>
            <button class="btn btn-secondary" onclick="window.close()">
                <i class="fas fa-times me-2"></i>Fermer
            </button>
        </div>
    </div>

    <!-- Contenu de l'aperçu -->
    <div class="card shadow">
        <div class="card-body p-5" id="previewContent">
            <!-- Header -->
            <div class="row mb-4">
                <div class="col-6">
                    <h2 class="text-primary mb-1">KENAM SERVICES</h2>
                    <p class="mb-0">123 Rue de la Paix, Abidjan</p>
                    <p class="mb-0">Côte d'Ivoire</p>
                    <p class="mb-0">Tel: +225 XX XX XX XX</p>
                </div>
                <div class="col-6 text-end">
                    <h4 class="text-primary mb-1">PROFORMA</h4>
                    <p class="mb-0 fw-bold" id="preview-reference">DEV-0019</p>
                    <p class="mb-0" id="preview-date">Date: {{ date('d/m/Y') }}</p>
                </div>
            </div>

            <!-- Informations client -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="border p-3">
                        <h6 class="text-primary mb-2">
                            <i class="fas fa-user me-2"></i>Client
                        </h6>
                        <p class="mb-1 fw-bold" id="preview-client">Logistics Pro</p>
                        <p class="mb-0" id="preview-client-address">123 Rue de la Paix, Abidjan, Côte d'Ivoire</p>
                    </div>
                </div>
            </div>

            <!-- Objet et dates -->
            <div class="row mb-4">
                <div class="col-md-8">
                    <strong>Objet:</strong>
                    <p id="preview-objet">Audit système complet et maintenance préventive</p>
                </div>
                <div class="col-md-4">
                    <div class="row">
                        <div class="col-6">
                            <strong>Date d'émission:</strong>
                            <p id="preview-issue-date">{{ date('d/m/Y') }}</p>
                        </div>
                        <div class="col-6">
                            <strong>Échéance:</strong>
                            <p id="preview-due-date">{{ date('d/m/Y', strtotime('+30 days')) }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Prestations -->
            <div class="row mb-4">
                <div class="col-12">
                    <h6 class="text-primary mb-3">
                        <i class="fas fa-list me-2"></i>Détail des Prestations
                    </h6>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead class="table-light">
                                <tr>
                                    <th>N°</th>
                                    <th>Description</th>
                                    <th class="text-center">Qté</th>
                                    <th class="text-end">Prix Unit.</th>
                                    <th class="text-center">TVA</th>
                                    <th class="text-end">Total HT</th>
                                </tr>
                            </thead>
                            <tbody id="preview-prestations">
                                <!-- Les prestations seront ajoutées dynamiquement -->
                            </tbody>
                            <tfoot>
                                <tr class="table-primary">
                                    <td colspan="5" class="text-end fw-bold">TOTAL HT</td>
                                    <td class="text-end fw-bold" id="preview-total-ht">0 FCFA</td>
                                </tr>
                                <tr class="table-primary">
                                    <td colspan="5" class="text-end fw-bold">TVA (18%)</td>
                                    <td class="text-end fw-bold" id="preview-tva">0 FCFA</td>
                                </tr>
                                <tr class="table-success">
                                    <td colspan="5" class="text-end fw-bold fs-5">TOTAL TTC</td>
                                    <td class="text-end fw-bold fs-5 text-success" id="preview-total-ttc">0 FCFA</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Notes -->
            <div class="row mb-4">
                <div class="col-12">
                    <h6 class="text-primary mb-2">
                        <i class="fas fa-sticky-note me-2"></i>Notes
                    </h6>
                    <p id="preview-notes">Aucune note complémentaire.</p>
                </div>
            </div>

            <!-- Conditions -->
            <div class="row">
                <div class="col-12">
                    <div class="border p-3 bg-light">
                        <h6 class="text-primary mb-2">
                            <i class="fas fa-info-circle me-2"></i>Conditions Générales
                        </h6>
                        <ul class="mb-0 small">
                            <li>Validité de l'offre: 30 jours à compter de la date d'émission</li>
                            <li>Paiement: 50% à la commande, 50% à la livraison</li>
                            <li>Délais de livraison: 15 jours ouvrés après confirmation</li>
                            <li>Garantie: 12 mois sur les prestations</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Signature -->
            <div class="row mt-5">
                <div class="col-6">
                    <p class="mb-4">KENAM SERVICES</p>
                    <hr class="w-50">
                    <small>Direction Générale</small>
                </div>
                <div class="col-6 text-end">
                    <p class="mb-4">Le Client</p>
                    <hr class="w-50 ms-auto">
                    <small>Signature et Cachet</small>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Écouter les messages de la fenêtre parente
window.addEventListener('message', function(event) {
    if (event.data.type === 'preview-data') {
        const data = event.data.data;
        updatePreview(data);
    }
});

function updatePreview(data) {
    // Mettre à jour les informations générales
    document.getElementById('preview-reference').textContent = 'DEV-' + String(data.client_id || 19).padStart(4, '0');
    document.getElementById('preview-date').textContent = 'Date: ' + new Date().toLocaleDateString('fr-FR');

    // Mettre à jour le client
    const clientNames = {
        '1': 'TechnoPlus SA',
        '2': 'Logistics Pro',
        '3': 'Energy Solutions'
    };
    document.getElementById('preview-client').textContent = clientNames[data.client_id] || 'Client Inconnu';

    // Mettre à jour l'objet
    document.getElementById('preview-objet').textContent = data.objet || 'Objet non défini';

    // Mettre à jour les dates
    if (data.issue_date) {
        document.getElementById('preview-issue-date').textContent = new Date(data.issue_date).toLocaleDateString('fr-FR');
    }
    if (data.due_date) {
        document.getElementById('preview-due-date').textContent = new Date(data.due_date).toLocaleDateString('fr-FR');
    }

    // Mettre à jour les notes
    document.getElementById('preview-notes').textContent = data.notes || 'Aucune note complémentaire.';

    // Mettre à jour les prestations
    updatePrestations(data.prestations || []);

    // Calculer les totaux
    calculerTotauxPreview(data.prestations || []);
}

function updatePrestations(prestations) {
    const tbody = document.getElementById('preview-prestations');
    tbody.innerHTML = '';

    prestations.forEach((prestation, index) => {
        const quantite = parseFloat(prestation.quantite) || 0;
        const prixUnitaire = parseFloat(prestation.prix_unitaire) || 0;
        const totalHT = quantite * prixUnitaire;

        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${index + 1}</td>
            <td>
                <strong>${prestation.description || 'Prestation sans description'}</strong>
                ${prestation.details ? `<br><small class="text-muted">${prestation.details}</small>` : ''}
            </td>
            <td class="text-center">${quantite}</td>
            <td class="text-end">${prixUnitaire.toLocaleString('fr-FR')} FCFA</td>
            <td class="text-center">${prestation.tva || 18}%</td>
            <td class="text-end fw-bold">${totalHT.toLocaleString('fr-FR')} FCFA</td>
        `;
        tbody.appendChild(row);
    });
}

function calculerTotauxPreview(prestations) {
    let totalHT = 0;

    prestations.forEach(prestation => {
        const quantite = parseFloat(prestation.quantite) || 0;
        const prixUnitaire = parseFloat(prestation.prix_unitaire) || 0;
        totalHT += quantite * prixUnitaire;
    });

    const totalTVA = totalHT * 0.18;
    const totalTTC = totalHT + totalTVA;

    document.getElementById('preview-total-ht').textContent = totalHT.toLocaleString('fr-FR') + ' FCFA';
    document.getElementById('preview-tva').textContent = totalTVA.toLocaleString('fr-FR') + ' FCFA';
    document.getElementById('preview-total-ttc').textContent = totalTTC.toLocaleString('fr-FR') + ' FCFA';
}

// Si pas de données reçues, afficher des données de démonstration
document.addEventListener('DOMContentLoaded', function() {
    // Données de démonstration pour DEV-0019
    const demoData = {
        client_id: 2,
        objet: 'Audit système complet et maintenance préventive',
        notes: 'Audit demandé suite à l\'incident de sécurité du mois dernier.',
        prestations: [{
            description: 'Audit système complet',
            quantite: 1,
            prix_unitaire: 1250000,
            tva: 18,
            details: 'Audit complet des systèmes informatiques, sécurité et conformité'
        }]
    };

    // Attendre un peu au cas où les données arrivent
    setTimeout(() => {
        if (!document.getElementById('preview-prestations').children.length) {
            updatePreview(demoData);
        }
    }, 500);
});
</script>
@endpush

<style media="screen">
@page {
    size: A4;
    margin: 1cm;
}
</style>

<style media="print">
body {
    print-color-adjust: exact;
    -webkit-print-color-adjust: exact;
}
.btn-group {
    display: none !important;
}
</style>
@endsection

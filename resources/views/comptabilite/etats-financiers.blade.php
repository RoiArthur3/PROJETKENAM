@extends('layouts.app')

@section('title', 'États financiers')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 class="h3 mb-0">États financiers</h1>
                    <p class="text-muted">Bilan, compte de résultat et autres états financiers</p>
                </div>
                <a href="{{ route('comptabilite.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Retour
                </a>
            </div>
        </div>
    </div>

    <!-- Période et options -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row align-items-end">
                        <div class="col-md-3">
                            <label class="form-label">Type d'état</label>
                            <select class="form-select" id="type_etat" onchange="changerTypeEtat()">
                                <option value="bilan">Bilan</option>
                                <option value="compte-resultat">Compte de résultat</option>
                                <option value="flux-tresorerie">Flux de trésorerie</option>
                                <option value="variation-capitaux">Variation des capitaux propres</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Période</label>
                            <select class="form-select" id="periode">
                                <option value="mois">Ce mois</option>
                                <option value="trimestre">Ce trimestre</option>
                                <option value="semestre">Ce semestre</option>
                                <option value="annee">Cette année</option>
                                <option value="personnalise">Personnalisé</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Comparaison</label>
                            <select class="form-select" id="comparaison">
                                <option value="aucune">Aucune</option>
                                <option value="periode-precedente">Période précédente</option>
                                <option value="annee-precedente">Année précédente</option>
                                <option value="budget">Budget</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <button class="btn btn-primary w-100" onclick="genererEtat()">
                                <i class="fas fa-chart-line me-2"></i>Générer l'état
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bilan -->
    <div id="bilan_section">
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">ACTIF</h5>
                    </div>
                    <div class="card-body">
                        <h6 class="text-muted">Actif immobilisé</h6>
                        <table class="table table-sm">
                            <tr>
                                <td>Immobilisations corporelles</td>
                                <td class="text-end">15 000 000</td>
                            </tr>
                            <tr>
                                <td>Immobilisations financières</td>
                                <td class="text-end">2 500 000</td>
                            </tr>
                            <tr class="fw-bold">
                                <td>Total actif immobilisé</td>
                                <td class="text-end">17 500 000</td>
                            </tr>
                        </table>
                        <hr>
                        <h6 class="text-muted">Actif circulant</h6>
                        <table class="table table-sm">
                            <tr>
                                <td>Stocks et en-cours</td>
                                <td class="text-end">3 200 000</td>
                            </tr>
                            <tr>
                                <td>Créances clients</td>
                                <td class="text-end">4 800 000</td>
                            </tr>
                            <tr>
                                <td>Disponibilités</td>
                                <td class="text-end">1 500 000</td>
                            </tr>
                            <tr class="fw-bold">
                                <td>Total actif circulant</td>
                                <td class="text-end">9 500 000</td>
                            </tr>
                        </table>
                        <hr>
                        <table class="table">
                            <tr class="fw-bold fs-5">
                                <td>TOTAL ACTIF</td>
                                <td class="text-end">27 000 000</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">PASSIF</h5>
                    </div>
                    <div class="card-body">
                        <h6 class="text-muted">Capitaux propres</h6>
                        <table class="table table-sm">
                            <tr>
                                <td>Capital social</td>
                                <td class="text-end">10 000 000</td>
                            </tr>
                            <tr>
                                <td>Réserves</td>
                                <td class="text-end">2 000 000</td>
                            </tr>
                            <tr>
                                <td>Résultat de l'exercice</td>
                                <td class="text-end">1 500 000</td>
                            </tr>
                            <tr class="fw-bold">
                                <td>Total capitaux propres</td>
                                <td class="text-end">13 500 000</td>
                            </tr>
                        </table>
                        <hr>
                        <h6 class="text-muted">Dettes</h6>
                        <table class="table table-sm">
                            <tr>
                                <td>Emprunts et dettes financières</td>
                                <td class="text-end">8 000 000</td>
                            </tr>
                            <tr>
                                <td>Dettes fournisseurs</td>
                                <td class="text-end">3 500 000</td>
                            </tr>
                            <tr>
                                <td>Dettes fiscales et sociales</td>
                                <td class="text-end">2 000 000</td>
                            </tr>
                            <tr class="fw-bold">
                                <td>Total dettes</td>
                                <td class="text-end">13 500 000</td>
                            </tr>
                        </table>
                        <hr>
                        <table class="table">
                            <tr class="fw-bold fs-5">
                                <td>TOTAL PASSIF</td>
                                <td class="text-end">27 000 000</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Compte de résultat (caché par défaut) -->
    <div id="compte_resultat_section" style="display: none;">
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">PRODUITS</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm">
                            <tr>
                                <td>Ventes de marchandises</td>
                                <td class="text-end">45 000 000</td>
                            </tr>
                            <tr>
                                <td>Prestations de services</td>
                                <td class="text-end">12 000 000</td>
                            </tr>
                            <tr>
                                <td>Produits financiers</td>
                                <td class="text-end">500 000</td>
                            </tr>
                            <tr>
                                <td>Produits exceptionnels</td>
                                <td class="text-end">200 000</td>
                            </tr>
                            <tr class="fw-bold">
                                <td>TOTAL PRODUITS</td>
                                <td class="text-end">57 700 000</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-danger text-white">
                        <h5 class="mb-0">CHARGES</h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm">
                            <tr>
                                <td>Achats de marchandises</td>
                                <td class="text-end">28 000 000</td>
                            </tr>
                            <tr>
                                <td>Services extérieurs</td>
                                <td class="text-end">8 500 000</td>
                            </tr>
                            <tr>
                                <td>Charges de personnel</td>
                                <td class="text-end">12 000 000</td>
                            </tr>
                            <tr>
                                <td>Charges financières</td>
                                <td class="text-end">1 200 000</td>
                            </tr>
                            <tr>
                                <td>Charges exceptionnelles</td>
                                <td class="text-end">500 000</td>
                            </tr>
                            <tr class="fw-bold">
                                <td>TOTAL CHARGES</td>
                                <td class="text-end">50 200 000</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h5>RÉSULTAT DE L'EXERCICE</h5>
                            </div>
                            <div class="col-md-6 text-end">
                                <h3>7 500 000 FCFA</h3>
                                <small>Bénéfice</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Indicateurs clés -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Indicateurs financiers clés</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="text-center">
                                <h4 class="text-primary">13.0%</h4>
                                <p class="mb-0">Rentabilité financière</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <h4 class="text-success">1.5</h4>
                                <p class="mb-0">Ratio de liquidité</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <h4 class="text-warning">50.0%</h4>
                                <p class="mb-0">Ratio d'endettement</p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <h4 class="text-info">2.8</h4>
                                <p class="mb-0">Rotation des stocks</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Actions -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <button class="btn btn-primary" onclick="exporterPDF()">
                                <i class="fas fa-file-pdf me-2"></i>Exporter en PDF
                            </button>
                            <button class="btn btn-success ms-2" onclick="exporterExcel()">
                                <i class="fas fa-file-excel me-2"></i>Exporter en Excel
                            </button>
                            <button class="btn btn-warning ms-2" onclick="imprimer()">
                                <i class="fas fa-print me-2"></i>Imprimer
                            </button>
                        </div>
                        <div>
                            <button class="btn btn-outline-secondary" onclick="sauvegarder()">
                                <i class="fas fa-save me-2"></i>Sauvegarder
                            </button>
                            <button class="btn btn-info ms-2" onclick="partager()">
                                <i class="fas fa-share me-2"></i>Partager
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function changerTypeEtat() {
    const type = document.getElementById('type_etat').value;
    const bilanSection = document.getElementById('bilan_section');
    const compteResultatSection = document.getElementById('compte_resultat_section');

    // Cacher toutes les sections
    bilanSection.style.display = 'none';
    compteResultatSection.style.display = 'none';

    // Afficher la section correspondante
    switch(type) {
        case 'bilan':
            bilanSection.style.display = 'block';
            break;
        case 'compte-resultat':
            compteResultatSection.style.display = 'block';
            break;
        default:
            alert('État ' + type + ' à implémenter');
    }
}

function genererEtat() {
    // Appliquer le type d'état sélectionné (bilan / compte de résultat)
    changerTypeEtat();

    // Scroller vers la section affichée
    const type = document.getElementById('type_etat').value;
    const targetId = type === 'compte-resultat' ? 'compte_resultat_section' : 'bilan_section';
    const target = document.getElementById(targetId);
    if (target) {
        target.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
}

function exporterPDF() {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF();

    const typeEtat = document.getElementById('type_etat').value;
    const date = new Date().toISOString().split('T')[0];
    const filename = `etat_financier_${typeEtat}_${date}.pdf`;

    // Configuration du PDF
    doc.setFont('helvetica');
    doc.setFontSize(16);

    // Titre
    const titre = typeEtat === 'bilan' ? 'BILAN' : 'COMPTE DE RESULTAT';
    doc.text(titre, 105, 20, { align: 'center' });

    doc.setFontSize(12);
    doc.text(`KENAM SERVICES - ${date}`, 105, 35, { align: 'center' });

    let yPosition = 60;

    if (typeEtat === 'bilan') {
        // Section ACTIF
        doc.setFontSize(14);
        doc.setFont('helvetica', 'bold');
        doc.text('ACTIF', 20, yPosition);
        yPosition += 15;

        doc.setFontSize(12);
        doc.setFont('helvetica', 'normal');
        doc.text('Actif immobilisé', 20, yPosition);
        doc.text('17 500 000 FCFA', 170, yPosition);
        yPosition += 10;

        doc.text('Immobilisations corporelles', 30, yPosition);
        doc.text('15 000 000 FCFA', 170, yPosition);
        yPosition += 8;

        doc.text('Immobilisations financières', 30, yPosition);
        doc.text('2 500 000 FCFA', 170, yPosition);
        yPosition += 15;

        doc.setFont('helvetica', 'bold');
        doc.text('Actif circulant', 20, yPosition);
        yPosition += 10;

        doc.setFont('helvetica', 'normal');
        doc.text('Stocks et en-cours', 30, yPosition);
        doc.text('3 200 000 FCFA', 170, yPosition);
        yPosition += 8;

        doc.text('Créances clients', 30, yPosition);
        doc.text('4 800 000 FCFA', 170, yPosition);
        yPosition += 8;

        doc.text('Disponibilités', 30, yPosition);
        doc.text('1 500 000 FCFA', 170, yPosition);
        yPosition += 10;

        doc.setFont('helvetica', 'bold');
        doc.text('TOTAL ACTIF', 20, yPosition);
        doc.text('27 000 000 FCFA', 170, yPosition);
        yPosition += 30;

        // Section PASSIF
        doc.setFontSize(14);
        doc.setFont('helvetica', 'bold');
        doc.text('PASSIF', 20, yPosition);
        yPosition += 15;

        doc.setFontSize(12);
        doc.setFont('helvetica', 'bold');
        doc.text('Capitaux propres', 20, yPosition);
        yPosition += 10;

        doc.setFont('helvetica', 'normal');
        doc.text('Capital social', 30, yPosition);
        doc.text('10 000 000 FCFA', 170, yPosition);
        yPosition += 8;

        doc.text('Réserves', 30, yPosition);
        doc.text('2 000 000 FCFA', 170, yPosition);
        yPosition += 8;

        doc.text('Résultat de l\'exercice', 30, yPosition);
        doc.text('1 500 000 FCFA', 170, yPosition);
        yPosition += 10;

        doc.setFont('helvetica', 'bold');
        doc.text('Total capitaux propres', 30, yPosition);
        doc.text('13 500 000 FCFA', 170, yPosition);
        yPosition += 15;

        doc.setFont('helvetica', 'bold');
        doc.text('Dettes', 20, yPosition);
        yPosition += 10;

        doc.setFont('helvetica', 'normal');
        doc.text('Emprunts et dettes financières', 30, yPosition);
        doc.text('8 000 000 FCFA', 170, yPosition);
        yPosition += 8;

        doc.text('Dettes fournisseurs', 30, yPosition);
        doc.text('3 500 000 FCFA', 170, yPosition);
        yPosition += 8;

        doc.text('Dettes fiscales et sociales', 30, yPosition);
        doc.text('2 000 000 FCFA', 170, yPosition);
        yPosition += 10;

        doc.setFont('helvetica', 'bold');
        doc.text('Total dettes', 30, yPosition);
        doc.text('13 500 000 FCFA', 170, yPosition);
        yPosition += 15;

        doc.setFontSize(14);
        doc.text('TOTAL PASSIF', 20, yPosition);
        doc.text('27 000 000 FCFA', 170, yPosition);

    } else if (typeEtat === 'compte-resultat') {
        // Section PRODUITS
        doc.setFontSize(14);
        doc.setFont('helvetica', 'bold');
        doc.text('PRODUITS', 20, yPosition);
        yPosition += 15;

        doc.setFontSize(12);
        doc.setFont('helvetica', 'normal');
        doc.text('Ventes de marchandises', 20, yPosition);
        doc.text('45 000 000 FCFA', 170, yPosition);
        yPosition += 10;

        doc.text('Prestations de services', 20, yPosition);
        doc.text('12 000 000 FCFA', 170, yPosition);
        yPosition += 10;

        doc.text('Produits financiers', 20, yPosition);
        doc.text('500 000 FCFA', 170, yPosition);
        yPosition += 10;

        doc.text('Produits exceptionnels', 20, yPosition);
        doc.text('200 000 FCFA', 170, yPosition);
        yPosition += 15;

        doc.setFont('helvetica', 'bold');
        doc.text('TOTAL PRODUITS', 20, yPosition);
        doc.text('57 700 000 FCFA', 170, yPosition);
        yPosition += 30;

        // Section CHARGES
        doc.setFontSize(14);
        doc.setFont('helvetica', 'bold');
        doc.text('CHARGES', 20, yPosition);
        yPosition += 15;

        doc.setFontSize(12);
        doc.setFont('helvetica', 'normal');
        doc.text('Achats de marchandises', 20, yPosition);
        doc.text('28 000 000 FCFA', 170, yPosition);
        yPosition += 10;

        doc.text('Services extérieurs', 20, yPosition);
        doc.text('8 500 000 FCFA', 170, yPosition);
        yPosition += 10;

        doc.text('Charges de personnel', 20, yPosition);
        doc.text('12 000 000 FCFA', 170, yPosition);
        yPosition += 10;

        doc.text('Charges financières', 20, yPosition);
        doc.text('1 200 000 FCFA', 170, yPosition);
        yPosition += 10;

        doc.text('Charges exceptionnelles', 20, yPosition);
        doc.text('500 000 FCFA', 170, yPosition);
        yPosition += 15;

        doc.setFont('helvetica', 'bold');
        doc.text('TOTAL CHARGES', 20, yPosition);
        doc.text('50 200 000 FCFA', 170, yPosition);
        yPosition += 30;

        // Résultat
        doc.setFontSize(14);
        doc.setFont('helvetica', 'bold');
        doc.text('RESULTAT DE L\'EXERCICE', 20, yPosition);
        doc.text('7 500 000 FCFA', 170, yPosition);
        yPosition += 10;
        doc.setFontSize(12);
        doc.setFont('helvetica', 'normal');
        doc.text('(Bénéfice)', 20, yPosition);
    } else {
        alert('Type d\'état non supporté pour l\'export PDF');
        return;
    }

    // Pied de page
    doc.setFontSize(8);
    doc.setFont('helvetica', 'italic');
    doc.text('Généré automatiquement le ' + new Date().toLocaleDateString('fr-FR'), 20, 280);
    doc.text('KENAM SERVICES - Système de gestion comptable', 20, 285);

    // Télécharger le PDF
    doc.save(filename);
}

function exporterExcel() {
    const typeEtat = document.getElementById('type_etat').value;
    let csvContent = '';
    const date = new Date().toISOString().split('T')[0];
    const filename = `etat_financier_${typeEtat}_${date}.csv`;

    if (typeEtat === 'bilan') {
        // Export Bilan
        csvContent = 'SECTION,POSTE,MONTANT\n';

        // Actif immobilisé
        csvContent += 'ACTIF IMMOBILISE,Immobilisations corporelles,15000000\n';
        csvContent += 'ACTIF IMMOBILISE,Immobilisations financières,2500000\n';
        csvContent += 'ACTIF IMMOBILISE,Total actif immobilisé,17500000\n';

        // Actif circulant
        csvContent += 'ACTIF CIRCULANT,Stocks et en-cours,3200000\n';
        csvContent += 'ACTIF CIRCULANT,Créances clients,4800000\n';
        csvContent += 'ACTIF CIRCULANT,Disponibilités,1500000\n';
        csvContent += 'ACTIF CIRCULANT,Total actif circulant,9500000\n';
        csvContent += 'TOTAL ACTIF,,27000000\n';

        // Passif
        csvContent += 'CAPITAUX PROPRES,Capital social,10000000\n';
        csvContent += 'CAPITAUX PROPRES,Réserves,2000000\n';
        csvContent += 'CAPITAUX PROPRES,Résultat de l\'exercice,1500000\n';
        csvContent += 'CAPITAUX PROPRES,Total capitaux propres,13500000\n';

        csvContent += 'DETTES,Emprunts et dettes financières,8000000\n';
        csvContent += 'DETTES,Dettes fournisseurs,3500000\n';
        csvContent += 'DETTES,Dettes fiscales et sociales,2000000\n';
        csvContent += 'DETTES,Total dettes,13500000\n';
        csvContent += 'TOTAL PASSIF,,27000000\n';

    } else if (typeEtat === 'compte-resultat') {
        // Export Compte de résultat
        csvContent = 'SECTION,POSTE,MONTANT\n';

        // Produits
        csvContent += 'PRODUITS,Ventes de marchandises,45000000\n';
        csvContent += 'PRODUITS,Prestations de services,12000000\n';
        csvContent += 'PRODUITS,Produits financiers,500000\n';
        csvContent += 'PRODUITS,Produits exceptionnels,200000\n';
        csvContent += 'PRODUITS,Total produits,57700000\n';

        // Charges
        csvContent += 'CHARGES,Achats de marchandises,28000000\n';
        csvContent += 'CHARGES,Services extérieurs,8500000\n';
        csvContent += 'CHARGES,Charges de personnel,12000000\n';
        csvContent += 'CHARGES,Charges financières,1200000\n';
        csvContent += 'CHARGES,Charges exceptionnelles,500000\n';
        csvContent += 'CHARGES,Total charges,50200000\n';

        csvContent += 'RESULTAT,Résultat de l\'exercice,7500000\n';
    } else {
        alert('Type d\'état non supporté pour l\'export');
        return;
    }

    // Créer et télécharger le fichier
    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    if (link.download !== undefined) {
        const url = URL.createObjectURL(blob);
        link.setAttribute('href', url);
        link.setAttribute('download', filename);
        link.style.visibility = 'hidden';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }
}

function imprimer() {
    window.print();
}

function sauvegarder() {
    alert('Sauvegarde de l\'état financier (à implémenter)');
}

function partager() {
    alert('Partage de l\'état financier (à implémenter)');
}

// Gestion de l'export PDF
$('#export-pdf').click(function() {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF();

    // Capture le HTML de la section résultats
    const content = $('#resultats').html();

    // Génère le PDF (implémentation basique)
    doc.text('État Financier - KENAM SERVICES', 20, 20);
    doc.fromHTML(content, 15, 30);
    doc.save('etat-financier.pdf');
});

// Gestion de l'export Excel
$('#export-excel').click(function() {
    // Implémentation basique (peut être améliorée)
    let csvContent = "data:text/csv;charset=utf-8,";

    $('#resultats table tr').each(function() {
        const row = [];
        $(this).find('td, th').each(function() {
            row.push($(this).text().trim());
        });
        csvContent += row.join(";") + "\r\n";
    });

    const encodedUri = encodeURI(csvContent);
    const link = document.createElement("a");
    link.setAttribute("href", encodedUri);
    link.setAttribute("download", "etat-financier.csv");
    document.body.appendChild(link);
    link.click();
});
</script>
@endsection

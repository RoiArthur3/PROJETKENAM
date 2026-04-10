<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>État des Charges</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 20px;
            line-height: 1.4;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .section {
            margin-bottom: 25px;
        }
        .section-title {
            font-weight: bold;
            font-size: 14px;
            color: #28a745;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
            margin-bottom: 10px;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .table th, .table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        .table th {
            background-color: #f8f9fa;
            font-weight: bold;
        }
        .table td.text-end {
            text-align: right;
        }
        .total {
            font-weight: bold;
            background-color: #f8f9fa;
        }
        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 10px;
            color: #666;
        }
        .stats-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat-box {
            border: 1px solid #ddd;
            padding: 15px;
            text-align: center;
        }
        .stat-value {
            font-size: 18px;
            font-weight: bold;
            color: #28a745;
        }
        .stat-label {
            font-size: 11px;
            color: #666;
        }
    </style>
</head>
<body>
    @include('pdf.company-header')
    <div class="header">
        <h1>ÉTAT DES CHARGES</h1>
        <h2>KENAM SERVICES</h2>
        <p>Date: {{ now()->format('d/m/Y') }}</p>
        <p>Période: {{ $debut }} au {{ $fin }}</p>
    </div>

    <div class="section">
        <div class="section-title">STATISTIQUES</div>
        <div class="stats-grid">
            <div class="stat-box">
                <div class="stat-value">{{ number_format($stats['total_charges'] ?? 0, 0, ',', ' ') }} FCFA</div>
                <div class="stat-label">Total des charges</div>
            </div>
            <div class="stat-box">
                <div class="stat-value">{{ number_format($stats['moyenne_mensuelle'] ?? 0, 0, ',', ' ') }} FCFA</div>
                <div class="stat-label">Moyenne mensuelle</div>
            </div>
            <div class="stat-box">
                <div class="stat-value">{{ number_format($stats['charges_annee'] ?? 0, 0, ',', ' ') }} FCFA</div>
                <div class="stat-label">Total annuel</div>
            </div>
            <div class="stat-box">
                <div class="stat-value">4</div>
                <div class="stat-label">Nombre de charges</div>
            </div>
        </div>
    </div>

    <div class="section">
        <div class="section-title">DÉTAIL DES CHARGES</div>
        <table class="table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Libellé</th>
                    <th>Catégorie</th>
                    <th>Montant</th>
                    <th>Mode Paiement</th>
                    <th>Fournisseur</th>
                    <th>Statut</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>01/01/2024</td>
                    <td>Loyer bureau - Janvier</td>
                    <td>Loyer</td>
                    <td class="text-end">{{ number_format(1500000, 0, ',', ' ') }} FCFA</td>
                    <td>Virement</td>
                    <td>SCI KENAM</td>
                    <td>Payé</td>
                </tr>
                <tr>
                    <td>31/01/2024</td>
                    <td>Salaires - Janvier</td>
                    <td>Salaires</td>
                    <td class="text-end">{{ number_format(8000000, 0, ',', ' ') }} FCFA</td>
                    <td>Virement</td>
                    <td>Divers</td>
                    <td>Payé</td>
                </tr>
                <tr>
                    <td>15/01/2024</td>
                    <td>Fournitures de bureau</td>
                    <td>Fournitures</td>
                    <td class="text-end">{{ number_format(200000, 0, ',', ' ') }} FCFA</td>
                    <td>Espèces</td>
                    <td>Papeterie ABC</td>
                    <td>Payé</td>
                </tr>
                <tr>
                    <td>20/01/2024</td>
                    <td>Internet et Téléphonie</td>
                    <td>Services</td>
                    <td class="text-end">{{ number_format(350000, 0, ',', ' ') }} FCFA</td>
                    <td>Virement</td>
                    <td>Orange CI</td>
                    <td>En attente</td>
                </tr>
            </tbody>
            <tfoot>
                <tr class="total">
                    <td colspan="3">TOTAL</td>
                    <td class="text-end">{{ number_format(10050000, 0, ',', ' ') }} FCFA</td>
                    <td colspan="3"></td>
                </tr>
            </tfoot>
        </table>
    </div>

    <div class="section">
        <div class="section-title">RÉPARTITION PAR CATÉGORIE</div>
        <table class="table">
            <tr>
                <th>Catégorie</th>
                <th>Nombre</th>
                <th>Total</th>
                <th>Pourcentage</th>
            </tr>
            <tr>
                <td>Loyer</td>
                <td>1</td>
                <td class="text-end">{{ number_format(1500000, 0, ',', ' ') }} FCFA</td>
                <td class="text-end">14.9%</td>
            </tr>
            <tr>
                <td>Salaires</td>
                <td>1</td>
                <td class="text-end">{{ number_format(8000000, 0, ',', ' ') }} FCFA</td>
                <td class="text-end">79.6%</td>
            </tr>
            <tr>
                <td>Fournitures</td>
                <td>1</td>
                <td class="text-end">{{ number_format(200000, 0, ',', ' ') }} FCFA</td>
                <td class="text-end">2.0%</td>
            </tr>
            <tr>
                <td>Services</td>
                <td>1</td>
                <td class="text-end">{{ number_format(350000, 0, ',', ' ') }} FCFA</td>
                <td class="text-end">3.5%</td>
            </tr>
            <tr class="total">
                <td>TOTAL</td>
                <td>4</td>
                <td class="text-end">{{ number_format(10050000, 0, ',', ' ') }} FCFA</td>
                <td class="text-end">100.0%</td>
            </tr>
        </table>
    </div>

    <div class="section">
        <div class="section-title">RÉPARTITION PAR STATUT</div>
        <table class="table">
            <tr>
                <th>Statut</th>
                <th>Nombre</th>
                <th>Total</th>
                <th>Pourcentage</th>
            </tr>
            <tr>
                <td>Payé</td>
                <td>3</td>
                <td class="text-end">{{ number_format(9700000, 0, ',', ' ') }} FCFA</td>
                <td class="text-end">96.5%</td>
            </tr>
            <tr>
                <td>En attente</td>
                <td>1</td>
                <td class="text-end">{{ number_format(350000, 0, ',', ' ') }} FCFA</td>
                <td class="text-end">3.5%</td>
            </tr>
            <tr class="total">
                <td>TOTAL</td>
                <td>4</td>
                <td class="text-end">{{ number_format(10050000, 0, ',', ' ') }} FCFA</td>
                <td class="text-end">100.0%</td>
            </tr>
        </table>
    </div>

    @include('pdf.company-footer')

    <script>
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>

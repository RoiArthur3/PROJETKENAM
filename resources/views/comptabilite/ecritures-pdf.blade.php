<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Journal Comptable</title>
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
            grid-template-columns: 1fr 1fr 1fr 1fr;
            gap: 15px;
            margin-bottom: 30px;
        }
        .stat-box {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: center;
        }
        .stat-value {
            font-size: 16px;
            font-weight: bold;
            color: #28a745;
        }
        .stat-label {
            font-size: 10px;
            color: #666;
        }
        .badge {
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: bold;
        }
        .badge-info { background-color: #17a2b8; color: white; }
        .badge-success { background-color: #28a745; color: white; }
        .badge-warning { background-color: #ffc107; color: black; }
        .badge-primary { background-color: #007bff; color: white; }
    </style>
</head>
<body>
    @include('pdf.company-header')
    <div class="header">
        <h1>JOURNAL COMPTABLE</h1>
        <h2>KENAM SERVICES</h2>
        <p>Date: {{ now()->format('d/m/Y') }}</p>
        <p>Période: {{ $filters['periode'] ?? 'Toutes périodes' }}</p>
    </div>

    <div class="section">
        <div class="section-title">STATISTIQUES DU JOURNAL</div>
        <div class="stats-grid">
            <div class="stat-box">
                <div class="stat-value">156</div>
                <div class="stat-label">Total écritures</div>
            </div>
            <div class="stat-box">
                <div class="stat-value">45 000 000 FCFA</div>
                <div class="stat-label">Total débit</div>
            </div>
            <div class="stat-box">
                <div class="stat-value">45 000 000 FCFA</div>
                <div class="stat-label">Total crédit</div>
            </div>
            <div class="stat-box">
                <div class="stat-value">0 FCFA</div>
                <div class="stat-label">Solde</div>
            </div>
        </div>
    </div>

    <div class="section">
        <div class="section-title">DÉTAIL DES ÉCRITURES</div>
        <table class="table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Référence</th>
                    <th>Libellé</th>
                    <th>Compte Débit</th>
                    <th>Compte Crédit</th>
                    <th>Montant</th>
                    <th>Débit</th>
                    <th>Crédit</th>
                    <th>Journal</th>
                    <th>Pièce</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>01/01/2024</td>
                    <td>AC001</td>
                    <td>Achat fournitures bureau</td>
                    <td>606000</td>
                    <td>401000</td>
                    <td class="text-end">{{ number_format(500000, 0, ',', ' ') }} FCFA</td>
                    <td class="text-end">{{ number_format(500000, 0, ',', ' ') }} FCFA</td>
                    <td class="text-end">-</td>
                    <td><span class="badge badge-info">Achats</span></td>
                    <td>FAC001</td>
                </tr>
                <tr>
                    <td>02/01/2024</td>
                    <td>VT001</td>
                    <td>Vente marchandises</td>
                    <td>411000</td>
                    <td>707000</td>
                    <td class="text-end">{{ number_format(1200000, 0, ',', ' ') }} FCFA</td>
                    <td class="text-end">-</td>
                    <td class="text-end">{{ number_format(1200000, 0, ',', ' ') }} FCFA</td>
                    <td><span class="badge badge-success">Ventes</span></td>
                    <td>FAC002</td>
                </tr>
                <tr>
                    <td>03/01/2024</td>
                    <td>BN001</td>
                    <td>Paiement loyer janvier</td>
                    <td>613000</td>
                    <td>512000</td>
                    <td class="text-end">{{ number_format(1500000, 0, ',', ' ') }} FCFA</td>
                    <td class="text-end">{{ number_format(1500000, 0, ',', ' ') }} FCFA</td>
                    <td class="text-end">-</td>
                    <td><span class="badge badge-warning">Banque</span></td>
                    <td>BN001</td>
                </tr>
                <tr>
                    <td>04/01/2024</td>
                    <td>CA001</td>
                    <td>Versement espèces en banque</td>
                    <td>512000</td>
                    <td>531000</td>
                    <td class="text-end">{{ number_format(800000, 0, ',', ' ') }} FCFA</td>
                    <td class="text-end">{{ number_format(800000, 0, ',', ' ') }} FCFA</td>
                    <td class="text-end">-</td>
                    <td><span class="badge badge-primary">Caisse</span></td>
                    <td>CA001</td>
                </tr>
            </tbody>
            <tfoot>
                <tr class="total">
                    <td colspan="5">TOTAL</td>
                    <td class="text-end">{{ number_format(4000000, 0, ',', ' ') }} FCFA</td>
                    <td class="text-end">{{ number_format(2800000, 0, ',', ' ') }} FCFA</td>
                    <td class="text-end">{{ number_format(1200000, 0, ',', ' ') }} FCFA</td>
                    <td colspan="2"></td>
                </tr>
            </tfoot>
        </table>
    </div>

    <div class="section">
        <div class="section-title">RÉPARTITION PAR JOURNAL</div>
        <table class="table">
            <tr>
                <th>Journal</th>
                <th>Nombre d'écritures</th>
                <th>Total Débit</th>
                <th>Total Crédit</th>
                <th>Solde</th>
            </tr>
            <tr>
                <td>Achats</td>
                <td>45</td>
                <td class="text-end">{{ number_format(15000000, 0, ',', ' ') }} FCFA</td>
                <td class="text-end">{{ number_format(15000000, 0, ',', ' ') }} FCFA</td>
                <td class="text-end">0 FCFA</td>
            </tr>
            <tr>
                <td>Ventes</td>
                <td>38</td>
                <td class="text-end">{{ number_format(12000000, 0, ',', ' ') }} FCFA</td>
                <td class="text-end">{{ number_format(12000000, 0, ',', ' ') }} FCFA</td>
                <td class="text-end">0 FCFA</td>
            </tr>
            <tr>
                <td>Banque</td>
                <td>52</td>
                <td class="text-end">{{ number_format(13000000, 0, ',', ' ') }} FCFA</td>
                <td class="text-end">{{ number_format(13000000, 0, ',', ' ') }} FCFA</td>
                <td class="text-end">0 FCFA</td>
            </tr>
            <tr>
                <td>Caisse</td>
                <td>21</td>
                <td class="text-end">{{ number_format(5000000, 0, ',', ' ') }} FCFA</td>
                <td class="text-end">{{ number_format(5000000, 0, ',', ' ') }} FCFA</td>
                <td class="text-end">0 FCFA</td>
            </tr>
            <tr class="total">
                <td>TOTAL</td>
                <td>156</td>
                <td class="text-end">{{ number_format(45000000, 0, ',', ' ') }} FCFA</td>
                <td class="text-end">{{ number_format(45000000, 0, ',', ' ') }} FCFA</td>
                <td class="text-end">0 FCFA</td>
            </tr>
        </table>
    </div>

    <div class="section">
        <div class="section-title">RÉPARTITION PAR COMPTE</div>
        <table class="table">
            <tr>
                <th>Compte</th>
                <th>Libellé</th>
                <th>Nombre d'écritures</th>
                <th>Total Débit</th>
                <th>Total Crédit</th>
                <th>Solde</th>
            </tr>
            <tr>
                <td>512000</td>
                <td>Banque</td>
                <td>28</td>
                <td class="text-end">{{ number_format(15000000, 0, ',', ' ') }} FCFA</td>
                <td class="text-end">{{ number_format(12000000, 0, ',', ' ') }} FCFA</td>
                <td class="text-end">{{ number_format(3000000, 0, ',', ' ') }} FCFA</td>
            </tr>
            <tr>
                <td>531000</td>
                <td>Caisse</td>
                <td>15</td>
                <td class="text-end">{{ number_format(8000000, 0, ',', ' ') }} FCFA</td>
                <td class="text-end">{{ number_format(8000000, 0, ',', ' ') }} FCFA</td>
                <td class="text-end">0 FCFA</td>
            </tr>
            <tr>
                <td>401000</td>
                <td>Fournisseurs</td>
                <td>22</td>
                <td class="text-end">{{ number_format(12000000, 0, ',', ' ') }} FCFA</td>
                <td class="text-end">{{ number_format(12000000, 0, ',', ' ') }} FCFA</td>
                <td class="text-end">0 FCFA</td>
            </tr>
            <tr>
                <td>411000</td>
                <td>Clients</td>
                <td>18</td>
                <td class="text-end">{{ number_format(10000000, 0, ',', ' ') }} FCFA</td>
                <td class="text-end">{{ number_format(10000000, 0, ',', ' ') }} FCFA</td>
                <td class="text-end">0 FCFA</td>
            </tr>
            <tr class="total">
                <td colspan="2">TOTAL</td>
                <td>83</td>
                <td class="text-end">{{ number_format(45000000, 0, ',', ' ') }} FCFA</td>
                <td class="text-end">{{ number_format(42000000, 0, ',', ' ') }} FCFA</td>
                <td class="text-end">{{ number_format(3000000, 0, ',', ' ') }} FCFA</td>
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

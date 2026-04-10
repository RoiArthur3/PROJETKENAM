<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Déclaration CNSS</title>
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
        .table td {
            text-align: right;
        }
        .table td:first-child {
            text-align: left;
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
        .taux-box {
            border: 1px solid #ddd;
            padding: 10px;
            margin-bottom: 15px;
        }
        .taux-title {
            font-weight: bold;
            margin-bottom: 5px;
        }
    </style>
</head>
<body>
    @include('pdf.company-header')
    <div class="header">
        <h1>DÉCLARATION CNSS</h1>
        <h2>KENAM SERVICES</h2>
        <p>Période: {{ $debut }} au {{ $fin }}</p>
        <p>Date: {{ now()->format('d/m/Y') }}</p>
    </div>

    <div class="section">
        <div class="section-title">TAUX DE COTISATION</div>
        <div style="display: flex; gap: 20px;">
            <div class="taux-box" style="flex: 1;">
                <div class="taux-title">PART EMPLOYEUR (15.0%)</div>
                <div>- Prestations familiales: 5.5%</div>
                <div>- Accidents de travail: 2.0%</div>
                <div>- Retraite: 7.5%</div>
            </div>
            <div class="taux-box" style="flex: 1;">
                <div class="taux-title">PART EMPLOYÉ (5.5%)</div>
                <div>- Retraite: 5.5%</div>
            </div>
        </div>
        <div class="taux-box">
            <div class="taux-title">TOTAL GLOBAL: 20.5%</div>
            <div>Plafond de calcul: {{ number_format($plafond, 0, ',', ' ') }} FCFA par salarié</div>
        </div>
    </div>

    <div class="section">
        <div class="section-title">DÉTAIL DES SALARIÉS</div>
        <table class="table">
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Matricule</th>
                    <th>Catégorie</th>
                    <th>Salaire brut</th>
                    <th>Base calcul</th>
                    <th>Cot. employeur</th>
                    <th>Cot. employé</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($resultats as $resultat)
                <tr>
                    <td>{{ $resultat['salarie']['nom'] }}</td>
                    <td>{{ $resultat['salarie']['matricule'] }}</td>
                    <td>{{ $resultat['salarie']['categorie'] }}</td>
                    <td>{{ number_format($resultat['salarie']['salaire_brut'], 0, ',', ' ') }} FCFA</td>
                    <td>{{ number_format($resultat['base_calcul'], 0, ',', ' ') }} FCFA</td>
                    <td>{{ number_format($resultat['cotisations']['employeur']['total'], 0, ',', ' ') }} FCFA</td>
                    <td>{{ number_format($resultat['cotisations']['employe']['total'], 0, ',', ' ') }} FCFA</td>
                    <td>{{ number_format($resultat['cotisation_global'], 0, ',', ' ') }} FCFA</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="total">
                    <td colspan="3">TOTAL</td>
                    <td>{{ number_format($totaux['salaire_brut'], 0, ',', ' ') }} FCFA</td>
                    <td>-</td>
                    <td>{{ number_format($totaux['cotisation_employeur'], 0, ',', ' ') }} FCFA</td>
                    <td>{{ number_format($totaux['cotisation_employe'], 0, ',', ' ') }} FCFA</td>
                    <td>{{ number_format($totaux['cotisation_global'], 0, ',', ' ') }} FCFA</td>
                </tr>
            </tfoot>
        </table>
    </div>

    <div class="section">
        <div class="section-title">RÉCAPITULATIF DES COTISATIONS</div>
        <table class="table">
            <tr>
                <th>Libellé</th>
                <th>Montant</th>
            </tr>
            <tr>
                <td>Total des salaires bruts</td>
                <td>{{ number_format($totaux['salaire_brut'], 0, ',', ' ') }} FCFA</td>
            </tr>
            <tr>
                <td>Cotisations employeur (15.0%)</td>
                <td>{{ number_format($totaux['cotisation_employeur'], 0, ',', ' ') }} FCFA</td>
            </tr>
            <tr>
                <td>Cotisations employés (5.5%)</td>
                <td>{{ number_format($totaux['cotisation_employe'], 0, ',', ' ') }} FCFA</td>
            </tr>
            <tr class="total" style="font-size: 14px; color: #dc3545;">
                <td>TOTAL À VERSER CNSS</td>
                <td>{{ number_format($totaux['cotisation_global'], 0, ',', ' ') }} FCFA</td>
            </tr>
        </table>
    </div>

    <div class="section">
        <div class="section-title">DÉTAIL PAR TYPE DE COTISATION</div>
        <table class="table">
            <tr>
                <th>Type de cotisation</th>
                <th>Taux</th>
                <th>Base totale</th>
                <th>Montant</th>
            </tr>
            <tr>
                <td>Prestations familiales</td>
                <td>5.5%</td>
                <td>{{ number_format($totaux['cotisation_employeur'] / 0.15 * 0.055, 0, ',', ' ') }} FCFA</td>
                <td>{{ number_format($totaux['cotisation_employeur'] / 0.15 * 0.055, 0, ',', ' ') }} FCFA</td>
            </tr>
            <tr>
                <td>Accidents de travail</td>
                <td>2.0%</td>
                <td>{{ number_format($totaux['cotisation_employeur'] / 0.15 * 0.02, 0, ',', ' ') }} FCFA</td>
                <td>{{ number_format($totaux['cotisation_employeur'] / 0.15 * 0.02, 0, ',', ' ') }} FCFA</td>
            </tr>
            <tr>
                <td>Retraite employeur</td>
                <td>7.5%</td>
                <td>{{ number_format($totaux['cotisation_employeur'] / 0.15 * 0.075, 0, ',', ' ') }} FCFA</td>
                <td>{{ number_format($totaux['cotisation_employeur'] / 0.15 * 0.075, 0, ',', ' ') }} FCFA</td>
            </tr>
            <tr>
                <td>Retraite employés</td>
                <td>5.5%</td>
                <td>{{ number_format($totaux['cotisation_employe'] / 0.055, 0, ',', ' ') }} FCFA</td>
                <td>{{ number_format($totaux['cotisation_employe'], 0, ',', ' ') }} FCFA</td>
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

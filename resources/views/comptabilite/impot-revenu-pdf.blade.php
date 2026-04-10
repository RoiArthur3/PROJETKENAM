<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Déclaration d'Impôt sur le Revenu</title>
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
        .row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
        }
        .label {
            font-weight: 600;
        }
        .value {
            text-align: right;
        }
        .total {
            font-weight: bold;
            border-top: 1px solid #333;
            padding-top: 5px;
            margin-top: 10px;
        }
        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 10px;
            color: #666;
        }
    </style>
</head>
<body>
    @include('pdf.company-header')
    <div class="header">
        <h1>DÉCLARATION D'IMPÔT SUR LE REVENU</h1>
        <h2>KENAM SERVICES</h2>
        <p>Période: {{ $debut }} au {{ $fin }}</p>
        <p>Date: {{ now()->format('d/m/Y') }}</p>
    </div>

    <div class="section">
        <div class="section-title">INFORMATIONS PERSONNELLES</div>
        <div class="row">
            <span class="label">Nom complet:</span>
            <span class="value">{{ auth()->user()->name ?? 'N/A' }}</span>
        </div>
        <div class="row">
            <span class="label">Nombre de parts fiscales:</span>
            <span class="value">{{ $data['nombre_parts'] }}</span>
        </div>
    </div>

    <div class="section">
        <div class="section-title">REVENU DÉCLARÉ</div>
        <div class="row">
            <span class="label">Salaires et traitements:</span>
            <span class="value">{{ number_format($data['revenus_salaire'], 0, ',', ' ') }} FCFA</span>
        </div>
        @if($data['revenus_autres'] ?? 0 > 0)
        <div class="row">
            <span class="label">Autres revenus:</span>
            <span class="value">{{ number_format($data['revenus_autres'], 0, ',', ' ') }} FCFA</span>
        </div>
        @endif
        <div class="row total">
            <span class="label">TOTAL REVENU:</span>
            <span class="value">{{ number_format($revenu_total, 0, ',', ' ') }} FCFA</span>
        </div>
    </div>

    <div class="section">
        <div class="section-title">CHARGES DÉDUCTIBLES</div>
        @if($data['charges_familiales'] ?? 0 > 0)
        <div class="row">
            <span class="label">Charges familiales:</span>
            <span class="value">{{ number_format($data['charges_familiales'], 0, ',', ' ') }} FCFA</span>
        </div>
        @endif
        @if($data['charges_professionnelles'] ?? 0 > 0)
        <div class="row">
            <span class="label">Charges professionnelles:</span>
            <span class="value">{{ number_format($data['charges_professionnelles'], 0, ',', ' ') }} FCFA</span>
        </div>
        @endif
        <div class="row total">
            <span class="label">TOTAL CHARGES:</span>
            <span class="value">{{ number_format($total_charges, 0, ',', ' ') }} FCFA</span>
        </div>
    </div>

    <div class="section">
        <div class="section-title">CALCUL DE L'IMPÔT</div>
        <div class="row">
            <span class="label">Revenu imposable:</span>
            <span class="value">{{ number_format($revenu_imposable, 0, ',', ' ') }} FCFA</span>
        </div>
        <div class="row">
            <span class="label">Impôt brut:</span>
            <span class="value">{{ number_format($impot_brut, 0, ',', ' ') }} FCFA</span>
        </div>
        <div class="row">
            <span class="label">Nombre de parts:</span>
            <span class="value">{{ $data['nombre_parts'] }}</span>
        </div>
        <div class="row total" style="font-size: 14px; color: #dc3545;">
            <span class="label">IMPÔT À PAYER:</span>
            <span class="value">{{ number_format($impot_net, 0, ',', ' ') }} FCFA</span>
        </div>
    </div>

    <div class="section">
        <div class="section-title">BARÈME IGR OFFICIEL — CGI Art. 123 (DGI Côte d'Ivoire)</div>
        <div class="row">
            <span class="label">0 - 600 000 FCFA:</span>
            <span class="value">0%</span>
        </div>
        <div class="row">
            <span class="label">600 001 - 1 800 000 FCFA:</span>
            <span class="value">10%</span>
        </div>
        <div class="row">
            <span class="label">1 800 001 - 3 000 000 FCFA:</span>
            <span class="value">15%</span>
        </div>
        <div class="row">
            <span class="label">3 000 001 - 7 200 000 FCFA:</span>
            <span class="value">20%</span>
        </div>
        <div class="row">
            <span class="label">7 200 001 - 12 000 000 FCFA:</span>
            <span class="value">25%</span>
        </div>
        <div class="row">
            <span class="label">Au-delà de 12 000 000 FCFA:</span>
            <span class="value">30%</span>
        </div>
    </div>

    @include('pdf.company-footer')

    <script>
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>

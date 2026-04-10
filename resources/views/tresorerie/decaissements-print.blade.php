<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>REÇU DE DÉCAISSEMENT - {{ $decaissement->reference }}</title>
    <style>
        body { font-family: 'Courier New', Courier, monospace; color: #000; margin: 0; padding: 20px; font-size: 13px; }
        .receipt-container { max-width: 800px; margin: 0 auto; border: 2px solid #000; padding: 20px; }
        .header { display: flex; justify-content: space-between; border-bottom: 2px solid #000; padding-bottom: 10px; margin-bottom: 20px; }
        .company-info h1 { margin: 0; font-size: 20px; }
        .receipt-title { text-align: center; margin-bottom: 30px; }
        .receipt-title h2 { display: inline-block; border: 2px solid #000; padding: 10px 40px; margin: 0; text-transform: uppercase; }
        .details-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 30px; }
        .detail-item { margin-bottom: 10px; }
        .label { font-weight: bold; text-decoration: underline; }
        .amount-box { border: 2px solid #000; padding: 15px; text-align: center; font-size: 18px; font-weight: bold; margin: 20px 0; }
        .footer-sigs { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px; margin-top: 50px; text-align: center; }
        .sig-box { min-height: 100px; }
        @media print {
            .no-print { display: none; }
            body { padding: 0; }
            .receipt-container { border: none; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="text-align: center; margin-bottom: 20px;">
        <button onclick="window.print()" style="padding: 10px 20px; background: #28a745; color: white; border: none; border-radius: 5px; cursor: pointer;">
            🖨️ IMPRIMER LE REÇU
        </button>
        <a href="{{ route('tresorerie.decaissements') }}" style="padding: 10px 20px; text-decoration: none; color: #333; margin-left: 10px;">
            Retour
        </a>
    </div>

    <div class="receipt-container">
        <div class="header">
            <div class="company-info">
                <h1>{{ $entreprise->nom_entreprise ?? 'KENAM SERVICES' }}</h1>
                <p>{{ $entreprise->adresse ?? 'Abidjan, Côte d\'Ivoire' }}</p>
                <p>Tel: {{ $entreprise->telephone ?? '+225 00 00 00 00 00' }}</p>
            </div>
            <div class="ref-info" style="text-align: right;">
                <p><strong>Réf:</strong> {{ $decaissement->reference }}</p>
                <p><strong>Date:</strong> {{ \Carbon\Carbon::parse($decaissement->date_depense)->format('d/m/Y') }}</p>
            </div>
        </div>

        <div class="receipt-title">
            <h2>REÇU DE DÉCAISSEMENT</h2>
        </div>

        <div class="amount-box">
            MONTANT : {{ number_format($decaissement->montant, 0, ',', ' ') }} FCFA
        </div>

        <div class="details-grid">
            <div class="detail-item">
                <span class="label">Bénéficiaire :</span>
                <span>{{ $decaissement->beneficiaire ?? 'Non spécifié' }}</span>
            </div>
            <div class="detail-item">
                <span class="label">Source :</span>
                <span>{{ $decaissement->caisse->nom ?? 'Caisse' }}</span>
            </div>
            <div class="detail-item" style="grid-column: 1 / -1;">
                <span class="label">Libellé :</span>
                <span>{{ $decaissement->libelle }}</span>
            </div>
            @if($decaissement->operation)
            <div class="detail-item">
                <span class="label">Opération liée :</span>
                <span>{{ $decaissement->operation->numero_ordre ?? '#OP-'.$decaissement->operation->id }}</span>
            </div>
            @endif
            <div class="detail-item">
                <span class="label">Référence Pièce :</span>
                <span>{{ $decaissement->justification ?? 'N/A' }}</span>
            </div>
        </div>

        <p><strong>Arrêté le présent reçu à la somme de :</strong> 
           <i style="text-transform: capitalize;">{{ \NumberFormatter::create('fr', \NumberFormatter::SPELLOUT)->format($decaissement->montant) }} francs CFA</i>
        </p>

        <div class="footer-sigs">
            <div class="sig-box">
                <p><strong>Le Bénéficiaire</strong></p>
            </div>
            <div class="sig-box">
                <p><strong>La Comptabilité</strong></p>
            </div>
            <div class="sig-box">
                <p><strong>Le Caissier</strong></p>
                <p style="margin-top: 40px; font-size: 11px;">{{ Auth::user()->name }}</p>
            </div>
        </div>
        
        <div style="margin-top: 30px; font-size: 10px; text-align: center; color: #666; border-top: 1px dashed #ccc; padding-top: 5px;">
            Imprimé le {{ date('d/m/Y à H:i') }} - KENAM SERVICES Digital Platform
        </div>
    </div>

    <script>
        // Lancer l'impression automatiquement au chargement si besoin
        // window.onload = function() { window.print(); }
    </script>
</body>
</html>

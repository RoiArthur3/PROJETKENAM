<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Contrat de Travail - {{ $agent->name }}</title>
    <style>
        body { font-family: 'Helvetica', sans-serif; font-size: 13px; line-height: 1.6; }
        .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #333; padding-bottom: 10px; }
        .title { text-transform: uppercase; font-size: 18px; font-weight: bold; margin: 20px 0; border: 1px solid #000; padding: 10px; text-align: center; }
        .section { margin-top: 20px; }
        .section-title { font-weight: bold; text-decoration: underline; text-transform: uppercase; margin-bottom: 10px; }
        .footer { position: fixed; bottom: 0; width: 100%; text-align: center; font-size: 10px; border-top: 1px solid #ccc; padding-top: 5px; }
        .signature-box { margin-top: 50px; }
        .signature { display: inline-block; width: 45%; vertical-align: top; }
        .text-right { text-align: right; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        table th, table td { border: 1px solid #000; padding: 8px; text-align: left; }
    </style>
</head>
<body>
    @include('pdf.company-header')

    <div class="title">
        CONTRAT DE TRAVAIL À DURÉE {{ $agent->contrat == 'CDI' ? 'INDÉTERMINÉE' : 'DÉTERMINÉE' }}
    </div>

    <div class="section">
        <div class="section-title">Entre les soussignés :</div>
        <p><strong>La société KENAM SERVICES</strong>, représentée par son Gérant, ci-après dénommée "L'Employeur",</p>
        <p>D'une part,</p>
        <p><strong>Et</strong></p>
        <p><strong>M/Mme {{ $agent->name }}</strong>, né(e) le {{ \Carbon\Carbon::parse($agent->date_naissance)->format('d/m/Y') }} à {{ $agent->lieu_naissance }}, de nationalité {{ $agent->nationalite }}, résidant à {{ $agent->adresse_postale ?? 'Abidjan' }}.<br>
        Ci-après dénommé(e) "L'Employé",</p>
        <p>D'autre part,</p>
    </div>

    <div class="section">
        <div class="section-title">Article 1 : Engagement & Fonction</div>
        <p>L'Employeur engage l'Employé en qualité de <strong>{{ $agent->role }}</strong>, classé à la catégorie <strong>{{ $agent->categorie_professionnelle ?? 'Non définie' }}</strong> de la Convention Collective Interprofessionnelle.</p>
    </div>

    <div class="section">
        <div class="section-title">Article 2 : Durée & Période d'essai</div>
        <p>Le présent contrat prend effet le <strong>{{ \Carbon\Carbon::parse($agent->date_embauche)->format('d/m/Y') }}</strong>.
        @if($agent->contrat == 'CDD')
            Il est conclu pour une durée déterminée prenant fin le {{ \Carbon\Carbon::parse($agent->date_fin_contrat)->format('d/m/Y') }}.
        @endif
        Une période d'essai de <strong>{{ $agent->periode_essai ?? '1' }} mois</strong> est prévue, durant laquelle chacune des parties pourra rompre le contrat sans préavis ni indemnités.</p>
    </div>

    <div class="section">
        <div class="section-title">Article 3 : Rémunération</div>
        <p>Pour l'exercice de ses fonctions, l'Employé percevra une rémunération mensuelle brute globale de <strong>{{ number_format($agent->salaire, 0, ',', ' ') }} FCFA</strong> se décomposant comme suit :</p>
        <ul>
            <li>Salaire de base : {{ number_format($agent->salaire_base, 0, ',', ' ') }} FCFA</li>
            @if($agent->sursalaire > 0) <li>Sursalaire : {{ number_format($agent->sursalaire, 0, ',', ' ') }} FCFA</li> @endif
            @if($agent->indemnite_transport > 0) <li>Indemnité de transport : {{ number_format($agent->indemnite_transport, 0, ',', ' ') }} FCFA</li> @endif
            @if($agent->indemnite_logement > 0) <li>Indemnité de logement : {{ number_format($agent->indemnite_logement, 0, ',', ' ') }} FCFA</li> @endif
        </ul>
    </div>

    <div class="section">
        <div class="section-title">Article 4 : Obligations</div>
        <p>L'Employé s'engage à consacrer toute son activité professionnelle au service de l'entreprise et à respecter le règlement intérieur en vigueur.</p>
    </div>

    <p style="margin-top: 40px;">Fait à Abidjan, le {{ date('d/m/Y') }}, en deux exemplaires originaux.</p>

    <div class="signature-box">
        <div class="signature">
            <strong>L'Employé(e)</strong><br>
            (Précédé de "Lu et approuvé")
        </div>
        <div class="signature text-right">
            <strong>Pour KENAM SERVICES</strong><br>
            Le Gérant
        </div>
    </div>

    @include('pdf.company-footer')
</body>
</html>

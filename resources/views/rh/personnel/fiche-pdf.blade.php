<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Fiche Personnel</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #222;
            margin: 24px;
        }

        .header {
            border-bottom: 2px solid #1f4f8a;
            padding-bottom: 10px;
            margin-bottom: 16px;
        }

        .title {
            font-size: 20px;
            font-weight: bold;
            color: #1f4f8a;
            margin: 0;
        }

        .subtitle {
            margin-top: 4px;
            color: #666;
            font-size: 11px;
        }

        .section {
            margin-bottom: 16px;
            page-break-inside: avoid;
        }

        .section h3 {
            margin: 0 0 8px 0;
            font-size: 14px;
            color: #1f4f8a;
            border-left: 4px solid #1f4f8a;
            padding-left: 8px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        td {
            padding: 6px 8px;
            border: 1px solid #ddd;
            vertical-align: top;
        }

        td.label {
            width: 35%;
            font-weight: bold;
            background: #f7f9fc;
        }

        .footer {
            margin-top: 20px;
            font-size: 10px;
            color: #777;
            text-align: right;
        }
    </style>
</head>
<body>
    <div class="header">
        <p class="title">Fiche Personnel</p>
        <p class="subtitle">Document genere le {{ now()->format('d/m/Y H:i') }}</p>
    </div>

    <div class="section">
        <h3>Identite</h3>
        <table>
            <tr>
                <td class="label">Matricule</td>
                <td>{{ $personnel->matricule ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">Nom complet</td>
                <td>{{ trim(($personnel->nom ?? '') . ' ' . ($personnel->prenoms ?? '')) ?: '-' }}</td>
            </tr>
            <tr>
                <td class="label">Date de naissance</td>
                <td>{{ optional($personnel->date_naissance)->format('d/m/Y') ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">Lieu de naissance</td>
                <td>{{ $personnel->lieu_naissance ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">Nationalite</td>
                <td>{{ $personnel->nationalite ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">Sexe</td>
                <td>{{ $personnel->sexe ?? '-' }}</td>
            </tr>
        </table>
    </div>

    <div class="section">
        <h3>Contact</h3>
        <table>
            <tr>
                <td class="label">Telephone principal</td>
                <td>{{ $personnel->telephone_principal ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">Telephone secondaire</td>
                <td>{{ $personnel->telephone_secondaire ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">Email</td>
                <td>{{ $personnel->email_personnel ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">Adresse</td>
                <td>{{ $personnel->adresse_residence ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">Ville</td>
                <td>{{ $personnel->ville ?? '-' }}</td>
            </tr>
        </table>
    </div>

    <div class="section">
        <h3>Informations professionnelles</h3>
        <table>
            <tr>
                <td class="label">Poste</td>
                <td>{{ $personnel->poste ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">Service</td>
                <td>{{ $personnel->service ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">Type de contrat</td>
                <td>{{ $personnel->type_contrat ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">Date d'embauche</td>
                <td>{{ optional($personnel->date_embauche)->format('d/m/Y') ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">Date fin de contrat</td>
                <td>{{ optional($personnel->date_fin_contrat)->format('d/m/Y') ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">Salaire de base</td>
                <td>
                    @if(!is_null($personnel->salaire_base))
                        {{ number_format((float) $personnel->salaire_base, 0, ',', ' ') }} {{ $personnel->devise ?? 'FCFA' }}
                    @else
                        -
                    @endif
                </td>
            </tr>
            <tr>
                <td class="label">Statut</td>
                <td>{{ $personnel->statut ?? '-' }}</td>
            </tr>
        </table>
    </div>

    <div class="section">
        <h3>Contact d'urgence</h3>
        <table>
            <tr>
                <td class="label">Nom</td>
                <td>{{ $personnel->nom_urgence ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">Telephone</td>
                <td>{{ $personnel->telephone_urgence ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">Lien de parente</td>
                <td>{{ $personnel->lien_parente ?? '-' }}</td>
            </tr>
        </table>
    </div>

    <div class="footer">
        KENAM SERVICES - Fiche personnel
    </div>
</body>
</html>

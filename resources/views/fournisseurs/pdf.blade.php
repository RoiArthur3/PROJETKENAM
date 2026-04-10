<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Fiche Fournisseur - {{ $fournisseur->raison_sociale }}</title>
    <style>
        body { font-family: sans-serif; font-size: 14px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #ddd; padding-bottom: 10px; }
        .section { margin-bottom: 20px; }
        .title { font-weight: bold; font-size: 16px; margin-bottom: 10px; color: #333; border-bottom: 1px solid #eee; }
        .row { display: flex; flex-wrap: wrap; margin-bottom: 5px; }
        .label { font-weight: bold; width: 150px; display: inline-block; }
        .value { }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    @include('pdf.company-header')
    <div class="header">
        <h1>Fiche Fournisseur</h1>
        <h2>{{ $fournisseur->raison_sociale }}</h2>
    </div>

    <div class="section">
        <div class="title">Informations Générales</div>
        <div><span class="label">Catégorie:</span> <span class="value">{{ $fournisseur->categorie ? $fournisseur->categorie->nom : 'N/A' }}</span></div>
        <div><span class="label">Référence:</span> <span class="value">{{ $fournisseur->reference }}</span></div>
        <div><span class="label">SIRET:</span> <span class="value">{{ $fournisseur->siret ?? 'N/A' }}</span></div>
        <div><span class="label">Email:</span> <span class="value">{{ $fournisseur->email }}</span></div>
        <div><span class="label">Téléphone:</span> <span class="value">{{ $fournisseur->telephone }}</span></div>
        <div><span class="label">Adresse:</span> <span class="value">{{ $fournisseur->adresse }} {{ $fournisseur->code_postal }} {{ $fournisseur->ville }}</span></div>
    </div>

    @if($fournisseur->contacts && count($fournisseur->contacts) > 0)
    <div class="section">
        <div class="title">Contacts</div>
        <table>
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Fonction</th>
                    <th>Email</th>
                    <th>Téléphone</th>
                </tr>
            </thead>
            <tbody>
                @foreach($fournisseur->contacts as $contact)
                <tr>
                    <td>{{ $contact['nom'] ?? '' }} {{ $contact['prenom'] ?? '' }}</td>
                    <td>{{ $contact['fonction'] ?? '' }}</td>
                    <td>{{ $contact['email'] ?? '' }}</td>
                    <td>{{ $contact['telephone'] ?? '' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    <div class="section">
        <div class="title">Notes</div>
        <p>{{ $fournisseur->notes ?? 'Aucune note.' }}</p>
    </div>
    @include('pdf.company-footer')
</body>
</html>

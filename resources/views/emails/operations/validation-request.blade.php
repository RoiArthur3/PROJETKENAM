@extends('emails.layouts.master')

@section('headerTitle', '📋 Nouvelle Opération à Valider')
@section('headerSubtitle', 'Une requête d\'opération nécessite votre validation')

@section('content')
<p>Bonjour,</p>

<p>Une nouvelle requête d'opération a été soumise et requiert votre validation pour pouvoir être traitée.</p>

<div class="info-card">
    <h3>📋 Détails de l'Opération</h3>
    <p><strong>Titre:</strong> {{ $operation->titre }}</p>
    <p><strong>Description:</strong> {{ $operation->description ?: 'Non spécifiée' }}</p>
    @if($operation->montant)
        <p><strong>Montant =</strong> {{ number_format($operation->montant, 0, ',', ' ') }} FCFA</p>
    @endif
    <p><strong>Priorité:</strong> <span class="priority-{{ $operation->priorite }}">{{ $operation->priorite }}</span></p>
    <p><strong>Demandeur:</strong> {{ $operation->demandeur_name }} ({{ $operation->demandeur_email }})</p>
    <p><strong>Date de soumission:</strong> {{ $operation->created_at->format('d/m/Y H:i') }}</p>
    @if($operation->echeance)
        <p><strong>Échéance:</strong> {{ $operation->echeance->format('d/m/Y') }}</p>
    @endif
</div>

@if($operation->fichiers && $operation->fichiers->count() > 0)
<div class="info-card">
    <h3>📎 Pièces Jointes</h3>
    <table style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr style="background-color: #f8f9fa;">
                <th style="padding: 8px; text-align: left; border: 1px solid #dee2e6;">Nom du fichier</th>
                <th style="padding: 8px; text-align: left; border: 1px solid #dee2e6;">Taille</th>
                <th style="padding: 8px; text-align: left; border: 1px solid #dee2e6;">Type</th>
            </tr>
        </thead>
        <tbody>
            @foreach($fichiers as $fichier)
            <tr>
                <td style="padding: 8px; border: 1px solid #dee2e6;">
                    <a href="{{ asset('storage/' . $fichier->chemin) }}" target="_blank" style="color: #007bff; text-decoration: none;">
                        {{ $fichier->nom_original }}
                    </a>
                </td>
                <td style="padding: 8px; border: 1px solid #dee2e6;">{{ number_format($fichier->taille / 1024, 2) }} Mo</td>
                <td style="padding: 8px; border: 1px solid #dee2e6;">{{ $fichier->type_fichier }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endif

<h3>📧 Actions requises</h3>
<ul>
    <li>Examiner les détails de l'opération ci-dessus</li>
    <li>Vérifier la conformité avec les procédures internes</li>
    <li>Approuver ou rejeter la demande</li>
    <li>Notifier le demandeur de votre décision</li>
</ul>

<div style="text-align: center; margin: 30px 0;">
    <a href="{{ route('operations.validate', $operation->id) }}" class="btn btn-primary">
        👁️ Accéder à la Page de Validation
    </a>
</div>

<p style="font-size: 14px; color: #6b7280;">
    Pour toute question, n'hésitez pas à nous contacter via les informations ci-dessous.
</p>
@endsection

@extends('layouts.email')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <h2 style="color: #007bff; margin-bottom: 20px;">
                <i class="fas fa-bell"></i> Nouvelle opération assignée
            </h2>

            <p>Bonjour,</p>

            <p>Une nouvelle opération vous a été assignée dans le système KENAM SERVICES.</p>

            <div style="background-color: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0;">
                <h3 style="color: #495057; margin-top: 0;">Détails de l'opération</h3>

                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td style="padding: 8px; border-bottom: 1px solid #dee2e6; font-weight: bold; width: 150px;">Titre:</td>
                        <td style="padding: 8px; border-bottom: 1px solid #dee2e6;">{{ $operation->titre }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px; border-bottom: 1px solid #dee2e6; font-weight: bold;">Priorité:</td>
                        <td style="padding: 8px; border-bottom: 1px solid #dee2e6;">
                            <span style="background-color: {{ $operation->priorite === 'urgente' ? '#dc3545' : ($operation->priorite === 'haute' ? '#fd7e14' : ($operation->priorite === 'moyenne' ? '#ffc107' : '#28a745')) }}; color: white; padding: 2px 8px; border-radius: 4px; font-size: 12px;">
                                {{ strtoupper($operation->priorite) }}
                            </span>
                        </td>
                    </tr>
                    @if($operation->description)
                    <tr>
                        <td style="padding: 8px; border-bottom: 1px solid #dee2e6; font-weight: bold;">Description:</td>
                        <td style="padding: 8px; border-bottom: 1px solid #dee2e6;">{{ $operation->description }}</td>
                    </tr>
                    @endif
                    @if($operation->echeance)
                    <tr>
                        <td style="padding: 8px; border-bottom: 1px solid #dee2e6; font-weight: bold;">Échéance:</td>
                        <td style="padding: 8px; border-bottom: 1px solid #dee2e6;">{{ \Carbon\Carbon::parse($operation->echeance)->format('d/m/Y') }}</td>
                    </tr>
                    @endif
                    <tr>
                        <td style="padding: 8px; border-bottom: 1px solid #dee2e6; font-weight: bold;">Demandeur:</td>
                        <td style="padding: 8px; border-bottom: 1px solid #dee2e6;">{{ $demandeur ? $demandeur->name : 'Système' }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px; border-bottom: 1px solid #dee2e6; font-weight: bold;">Email du demandeur:</td>
                        <td style="padding: 8px; border-bottom: 1px solid #dee2e6;">{{ $demandeur ? $demandeur->email : ($operation->demandeur_email ?? 'N/A') }}</td>
                    </tr>
                    <tr>
                        <td style="padding: 8px; font-weight: bold;">Service assigné:</td>
                        <td style="padding: 8px;">{{ $serviceOperationnel ? $serviceOperationnel->nom : 'Non spécifié' }}</td>
                    </tr>
                </table>
            </div>

            <div style="text-align: center; margin: 30px 0;">
                <a href="http://127.0.0.1:8000/validations/dashboard" style="background-color: #007bff; color: white; padding: 12px 30px; text-decoration: none; border-radius: 5px; display: inline-block;">
                    <i class="fas fa-eye"></i> Voir l'opération
                </a>
            </div>

            <p style="color: #6c757d; font-size: 14px; margin-top: 30px;">
                <em>Cet email a été envoyé automatiquement par le système KENAM SERVICES.
                Si vous n'êtes pas concerné par cette opération, veuillez ignorer cet email.</em>
            </p>
        </div>
    </div>
</div>
@endsection

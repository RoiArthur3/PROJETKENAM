@extends('layouts.app')

@section('title', 'Détail Employé')

@section('content')
<div class="container mt-4">
    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">Détail de l'employé</h4>
        </div>
        <div class="card-body">
            @if($employe)
                <table class="table table-bordered">
                    <tr><th>Matricule</th><td>{{ $employe->matricule ?? '-' }}</td></tr>
                    <tr><th>Nom</th><td>{{ $employe->nom ?? '-' }}</td></tr>
                    <tr><th>Prénoms</th><td>{{ $employe->prenoms ?? '-' }}</td></tr>
                    <tr><th>Poste</th><td>{{ $employe->poste ?? '-' }}</td></tr>
                    <tr><th>Service</th><td>{{ $employe->service ?? '-' }}</td></tr>
                    <tr><th>Email</th><td>{{ $employe->email_personnel ?? '-' }}</td></tr>
                    <tr><th>Téléphone</th><td>{{ $employe->telephone_principal ?? '-' }}</td></tr>
                    <tr><th>Date d'embauche</th><td>{{ $employe->date_embauche ?? '-' }}</td></tr>
                    <tr><th>Statut</th><td>{{ $employe->statut ?? '-' }}</td></tr>
                    <tr><th>Salaire</th><td>{{ $employe->salaire_base ?? '-' }} {{ $employe->devise ?? '' }}</td></tr>
                    <!-- Ajoute d'autres champs si besoin -->
                </table>
            @else
                <div class="alert alert-danger">Employé non trouvé.</div>
            @endif
        </div>
    </div>
</div>
@endsection

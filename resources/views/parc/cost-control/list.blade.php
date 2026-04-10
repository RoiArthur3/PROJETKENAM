@extends('layouts.app')

@section('title', 'Liste des pointages | KENAM SERVICES')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4"><i class="fas fa-stopwatch me-2 text-info"></i>Liste des pointages d'engins</h1>
    <p class="text-muted">Tableau des pointages enregistrés.</p>
    <!-- Tableau des pointages ici -->
    <a href="{{ route('materiel.cost-control.index') }}" class="btn btn-light border">Retour</a>
</div>
@endsection
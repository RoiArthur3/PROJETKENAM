@extends('layouts.app')

@section('title', 'Détail pointage engin | KENAM SERVICES')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4"><i class="fas fa-stopwatch me-2 text-info"></i>Détail du pointage d'engin</h1>
    <p class="text-muted">Affichage des informations détaillées du pointage.</p>
    <!-- Détails du pointage ici -->
    <a href="{{ route('materiel.cost-control.index') }}" class="btn btn-light border">Retour</a>
</div>
@endsection
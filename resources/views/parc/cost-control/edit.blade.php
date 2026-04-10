@extends('layouts.app')

@section('title', 'Modifier pointage engin | KENAM SERVICES')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4"><i class="fas fa-stopwatch me-2 text-info"></i>Modifier pointage d'engin</h1>
    <p class="text-muted">Formulaire d'édition d'un pointage existant.</p>
    <!-- Formulaire d'édition ici -->
    <a href="{{ route('materiel.cost-control.index') }}" class="btn btn-light border">Retour</a>
</div>
@endsection
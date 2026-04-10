@extends('layouts.app')

@section('title', 'Modifier charge / CA | KENAM SERVICES')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4"><i class="fas fa-wallet me-2 text-info"></i>Modifier charge / chiffre d'affaires</h1>
    <p class="text-muted">Formulaire d'édition d'une charge ou d'un CA.</p>
    <!-- Formulaire d'édition ici -->
    <a href="{{ route('materiel.cost-control.index') }}" class="btn btn-light border">Retour</a>
</div>
@endsection
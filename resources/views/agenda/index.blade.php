@extends('layouts.app')

@section('title', 'Agenda & Planification - KENAM SERVICES')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-calendar-alt me-2 text-primary"></i>Agenda & Planification
            </h1>
            <p class="text-muted mb-0">Vue calendrier des opérations, missions et tâches planifiées</p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-primary"><i class="fas fa-plus me-2"></i>Nouvel évènement</button>
            <button class="btn btn-outline-secondary"><i class="fas fa-file-export me-2"></i>Exporter</button>
        </div>
    </div>

    <div class="card shadow">
        <div class="card-body">
            <div class="text-center text-muted py-5">
                <i class="fas fa-calendar-alt fa-3x mb-3 text-primary"></i>
                <div class="mb-2">Le calendrier interactif sera intégré ici.</div>
                <small>Utilisez les boutons en haut à droite pour ajouter un évènement.</small>
            </div>
        </div>
    </div>
</div>
@endsection

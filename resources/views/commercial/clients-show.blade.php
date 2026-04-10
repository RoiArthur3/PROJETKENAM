@extends('layouts.app')

@section('title', 'Commercial - Détail client | KENAM SERVICES')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-building me-2"></i>Fiche client #{{ $clientData['id'] ?? '-' }}
                    </h5>
                    <a href="{{ route('commercial.clients.index') }}" class="btn btn-light btn-sm">
                        <i class="fas fa-arrow-left me-1"></i>Retour à la liste
                    </a>
                </div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-4">Nom</dt>
                        <dd class="col-sm-8">{{ $clientData['nom'] ?? 'Inexistant' }}</dd>

                        <dt class="col-sm-4">Contact</dt>
                        <dd class="col-sm-8">{{ $clientData['contact'] ?? 'Inexistant' }}</dd>

                        <dt class="col-sm-4">Email</dt>
                        <dd class="col-sm-8">{{ $clientData['email'] ?? 'Inexistant' }}</dd>

                        <dt class="col-sm-4">Téléphone</dt>
                        <dd class="col-sm-8">{{ $clientData['telephone'] ?? 'Inexistant' }}</dd>

                        <dt class="col-sm-4">Ville</dt>
                        <dd class="col-sm-8">{{ $clientData['ville'] ?? 'Inexistant' }}</dd>

                        <dt class="col-sm-4">Catégorie</dt>
                        <dd class="col-sm-8">{{ $clientData['categorie'] ?? 'Inexistant' }}</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

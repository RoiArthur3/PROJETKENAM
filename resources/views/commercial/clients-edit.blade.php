@extends('layouts.app')

@section('title', 'Commercial - Modifier client | KENAM SERVICES')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-warning text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-user-edit me-2"></i>Modifier le client #{{ $clientData['id'] ?? '-' }} (démo)
                    </h5>
                    <a href="{{ route('commercial.clients.index') }}" class="btn btn-light btn-sm">
                        <i class="fas fa-arrow-left me-1"></i>Retour à la liste
                    </a>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('commercial.clients.update', $clientData['id'] ?? 0) }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label class="form-label fw-bold">Nom de la société *</label>
                            <input type="text" name="nom" class="form-control" value="{{ $clientData['nom'] ?? '' }}">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Contact principal *</label>
                            <input type="text" name="contact" class="form-control" value="{{ $clientData['contact'] ?? '' }}">
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Email *</label>
                                <input type="email" name="email" class="form-control" value="{{ $clientData['email'] ?? '' }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Téléphone *</label>
                                <input type="text" name="telephone" class="form-control" value="{{ $clientData['telephone'] ?? '' }}">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Ville *</label>
                                <input type="text" name="ville" class="form-control" value="{{ $clientData['ville'] ?? '' }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Catégorie *</label>
                                <input type="text" name="categorie" class="form-control" value="{{ $clientData['categorie'] ?? '' }}">
                            </div>
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('commercial.clients.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-1"></i>Annuler
                            </a>
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-save me-1"></i>Enregistrer (démo)
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

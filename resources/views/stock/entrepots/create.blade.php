@extends('layouts.app')
@section('title', 'Nouvel Entrepôt - KENAM SERVICES')
@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary"><i class="fas fa-warehouse me-2"></i>Nouvel Entrepôt</h6>
                    <a href="{{ route('stock.entrepots') }}" class="btn btn-sm btn-secondary"><i class="fas fa-arrow-left me-1"></i>Retour</a>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('stock.entrepots.store') }}">
                        @csrf
                        <div class="row mb-3">
                            <div class="col-md-6"><label class="form-label fw-bold">Code *</label><input type="text" name="code" class="form-control" required></div>
                            <div class="col-md-6"><label class="form-label fw-bold">Nom *</label><input type="text" name="nom" class="form-control" required></div>
                        </div>
                        <div class="mb-3"><label class="form-label fw-bold">Adresse</label><input type="text" name="adresse" class="form-control"></div>
                        <div class="row mb-3">
                            <div class="col-md-6"><label class="form-label fw-bold">Responsable</label><input type="text" name="responsable" class="form-control"></div>
                            <div class="col-md-6"><label class="form-label fw-bold">Téléphone</label><input type="tel" name="telephone" class="form-control"></div>
                        </div>
                        <div class="mb-3"><label class="form-label fw-bold">Capacité (m²)</label><input type="number" name="capacite" class="form-control" min="0"></div>
                        <div class="d-flex justify-content-between mt-4 pt-3 border-top">
                            <a href="{{ route('stock.entrepots') }}" class="btn btn-secondary"><i class="fas fa-times me-2"></i>Annuler</a>
                            <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Enregistrer</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

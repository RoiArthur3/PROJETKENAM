@extends('layouts.app')

@section('title', 'Modifier Opération Bancaire - KENAM SERVICES')

@section('content')
<div class="content-wrapper">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Modifier Opération Bancaire</h1>
        <div class="d-flex gap-2">
            <a href="{{ route('tresorerie.banque') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Retour
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('tresorerie.banque.update', $banque->id) }}">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="reference" class="form-label">Référence</label>
                                <input type="text" class="form-control" id="reference" name="reference" value="{{ $banque->reference }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="date_operation" class="form-label">Date d'opération</label>
                                <input type="date" class="form-control" id="date_operation" name="date_operation" value="{{ \Carbon\Carbon::parse($banque->date_operation)->format('Y-m-d') }}" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 mb-3">
                                <label for="libelle" class="form-label">Libellé</label>
                                <input type="text" class="form-control" id="libelle" name="libelle" value="{{ $banque->libelle }}" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="montant" class="form-label">Montant (FCFA)</label>
                                <input type="number" class="form-control" id="montant" name="montant" value="{{ $banque->montant }}" required min="0" step="100">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="type" class="form-label">Type d'opération</label>
                                <select class="form-select" id="type" name="type" required>
                                    <option value="débit" {{ $banque->type == 'débit' ? 'selected' : '' }}>Débit</option>
                                    <option value="crédit" {{ $banque->type == 'crédit' ? 'selected' : '' }}>Crédit</option>
                                </select>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="banque" class="form-label">Banque</label>
                                <select class="form-select" id="banque" name="banque" required>
                                    <option value="ECOBANK" {{ $banque->banque == 'ECOBANK' ? 'selected' : '' }}>ECOBANK</option>
                                    <option value="SGBCI" {{ $banque->banque == 'SGBCI' ? 'selected' : '' }}>SGBCI</option>
                                    <option value="BIAO" {{ $banque->banque == 'BIAO' ? 'selected' : '' }}>BIAO</option>
                                    <option value="NSIA BANQUE" {{ $banque->banque == 'NSIA BANQUE' ? 'selected' : '' }}>NSIA BANQUE</option>
                                    <option value="UBA" {{ $banque->banque == 'UBA' ? 'selected' : '' }}>UBA</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <label for="statut" class="form-label">Statut</label>
                                <select class="form-select" id="statut" name="statut" required>
                                    <option value="validé" {{ $banque->statut == 'validé' ? 'selected' : '' }}>Validé</option>
                                    <option value="en attente" {{ $banque->statut == 'en attente' ? 'selected' : '' }}>En attente</option>
                                    <option value="annulé" {{ $banque->statut == 'annulé' ? 'selected' : '' }}>Annulé</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 mb-3">
                                <label for="notes" class="form-label">Notes</label>
                                <textarea class="form-control" id="notes" name="notes" rows="3">{{ $banque->notes ?? '' }}</textarea>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Mettre à jour
                                </button>
                                <a href="{{ route('tresorerie.banque') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-times me-2"></i>Annuler
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

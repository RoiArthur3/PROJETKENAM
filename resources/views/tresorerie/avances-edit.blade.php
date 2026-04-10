@extends('layouts.app')

@section('title', 'Modifier Avance - KENAM SERVICES')

@section('content')
<div class="content-wrapper">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Modifier Avance</h1>
        <div class="d-flex gap-2">
            <a href="{{ route('tresorerie.avances') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Retour
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form method="POST" action="{{ route('tresorerie.avances.update', $avance->id) }}">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="reference" class="form-label">Référence</label>
                                <input type="text" class="form-control" id="reference" name="reference" value="{{ $avance->reference }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="date_avance" class="form-label">Date de l'avance</label>
                                <input type="date" class="form-control" id="date_avance" name="date_avance" value="{{ \Carbon\Carbon::parse($avance->date_avance)->format('Y-m-d') }}" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="beneficiaire" class="form-label">Bénéficiaire</label>
                                <input type="text" class="form-control" id="beneficiaire" name="beneficiaire" value="{{ $avance->beneficiaire }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="montant" class="form-label">Montant (FCFA)</label>
                                <input type="number" class="form-control" id="montant" name="montant" value="{{ $avance->montant }}" required min="0" step="100">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="motif" class="form-label">Motif</label>
                                <select class="form-select" id="motif" name="motif" required>
                                    <option value="Mission" {{ $avance->motif == 'Mission' ? 'selected' : '' }}>Mission</option>
                                    <option value="Frais terrain" {{ $avance->motif == 'Frais terrain' ? 'selected' : '' }}>Frais terrain</option>
                                    <option value="Urgence" {{ $avance->motif == 'Urgence' ? 'selected' : '' }}>Urgence</option>
                                    <option value="Déplacement" {{ $avance->motif == 'Déplacement' ? 'selected' : '' }}>Déplacement</option>
                                    <option value="Autres" {{ $avance->motif == 'Autres' ? 'selected' : '' }}>Autres</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="caisse" class="form-label">Caisse</label>
                                <select class="form-select" id="caisse" name="caisse" required>
                                    <option value="Caisse Principale" {{ $avance->caisse == 'Caisse Principale' ? 'selected' : '' }}>Caisse Principale</option>
                                    <option value="Caisse Secondaire" {{ $avance->caisse == 'Caisse Secondaire' ? 'selected' : '' }}>Caisse Secondaire</option>
                                    <option value="Caisse Mobile" {{ $avance->caisse == 'Caisse Mobile' ? 'selected' : '' }}>Caisse Mobile</option>
                                </select>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="statut" class="form-label">Statut</label>
                                <select class="form-select" id="statut" name="statut" required>
                                    <option value="validé" {{ $avance->statut == 'validé' ? 'selected' : '' }}>Validé</option>
                                    <option value="en attente" {{ $avance->statut == 'en attente' ? 'selected' : '' }}>En attente</option>
                                    <option value="annulé" {{ $avance->statut == 'annulé' ? 'selected' : '' }}>Annulé</option>
                                    <option value="remboursé" {{ $avance->statut == 'remboursé' ? 'selected' : '' }}>Remboursé</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="date_remboursement" class="form-label">Date de remboursement</label>
                                <input type="date" class="form-control" id="date_remboursement" name="date_remboursement" value="{{ $avance->date_remboursement ?? '' }}">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea class="form-control" id="description" name="description" rows="3">{{ $avance->description ?? '' }}</textarea>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Mettre à jour
                                </button>
                                <a href="{{ route('tresorerie.avances') }}" class="btn btn-outline-secondary">
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

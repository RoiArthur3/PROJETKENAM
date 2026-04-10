@extends('layouts.app')

@section('title', 'Modifier Heure Supplémentaire | RH')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="mb-0">
                    <i class="fas fa-clock text-warning me-2"></i>
                    Modifier Heure Supplémentaire
                </h4>
                <a href="{{ route('rh.heures-sup.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-2"></i>Retour
                </a>
            </div>

            <div class="card">
                <div class="card-body">
                    <form action="{{ route('rh.heures-sup.update', $heureData['id']) }}" method="POST">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Agent</label>
                                    <input type="text" class="form-control" value="{{ $heureData['agent_nom'] }}" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Date</label>
                                    <input type="date" class="form-control" name="date" value="{{ now()->format('Y-m-d') }}" required>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Nombre d'heures</label>
                                    <input type="number" class="form-control" name="nombre_heures" step="0.5" min="0.5" value="2" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="form-label">Motif</label>
                                    <select class="form-select" name="motif" required>
                                        <option value="surcharge">Surcharge de travail</option>
                                        <option value="urgence">Travail d'urgence</option>
                                        <option value="weekend">Travail week-end</option>
                                        <option value="nuit">Travail de nuit</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="description" rows="3" placeholder="Description du travail effectué..."></textarea>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('rh.heures-sup.index') }}" class="btn btn-outline-secondary">Annuler</a>
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-save me-2"></i>Enregistrer
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@extends('layouts.app')

@section('title', 'Détails Caisse')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-md-8">
            <h4 class="mb-0">
                <i class="fas fa-cash-register text-primary me-2"></i>
                Détails de la caisse
            </h4>
            <small class="text-muted">Consultez les informations et les derniers mouvements.</small>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('tresorerie.caisses.index') }}" class="btn btn-outline-secondary me-2">
                <i class="fas fa-arrow-left me-1"></i>
                Retour
            </a>
            <a href="{{ route('tresorerie.caisses.edit', $caisse) }}" class="btn btn-primary">
                <i class="fas fa-edit me-1"></i>
                Modifier
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h6 class="mb-0">
                        <i class="fas fa-info-circle text-primary me-2"></i>
                        Informations
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="text-muted small">Nom</div>
                            <div class="fw-semibold">{{ $caisse->nom }}</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="text-muted small">Type</div>
                            <div class="fw-semibold text-capitalize">{{ $caisse->type }}</div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="text-muted small">Solde actuel</div>
                            <div class="fw-semibold">{{ number_format((float)$caisse->solde_actuel, 0, ',', ' ') }} {{ $caisse->devise }}</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="text-muted small">Solde initial</div>
                            <div class="fw-semibold">{{ number_format((float)$caisse->solde_initial, 0, ',', ' ') }} {{ $caisse->devise }}</div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="text-muted small">Responsable</div>
                            <div class="fw-semibold">{{ $caisse->responsable?->name ?? '—' }}</div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="text-muted small">Statut</div>
                            <div>
                                @if($caisse->est_active)
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-secondary">Inactive</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    @if($caisse->description)
                        <div class="mb-0">
                            <div class="text-muted small">Description</div>
                            <div>{{ $caisse->description }}</div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h6 class="mb-0">
                        <i class="fas fa-list text-primary me-2"></i>
                        Derniers mouvements
                    </h6>
                </div>
                <div class="card-body">
                    @if($caisse->mouvements->count())
                        <div class="list-group">
                            @foreach($caisse->mouvements as $m)
                                <div class="list-group-item d-flex justify-content-between align-items-start">
                                    <div>
                                        <div class="fw-semibold">{{ $m->type_mouvement }}</div>
                                        <div class="small text-muted">{{ $m->created_at?->format('d/m/Y H:i') }}</div>
                                    </div>
                                    <div class="text-end">
                                        <div class="fw-semibold">{{ number_format((float)$m->montant, 0, ',', ' ') }}</div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-muted">Aucun mouvement.</div>
                    @endif
                </div>
            </div>

            <form action="{{ route('tresorerie.caisses.destroy', $caisse) }}" method="POST" onsubmit="return confirm('Supprimer cette caisse ?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-outline-danger w-100">
                    <i class="fas fa-trash me-1"></i>
                    Supprimer
                </button>
            </form>
        </div>
    </div>
</div>
@endsection


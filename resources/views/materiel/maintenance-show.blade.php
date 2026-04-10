@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Détails maintenance</h5>
                    <div class="d-flex gap-2">
                        <a href="{{ route('materiel.maintenance.edit', $maintenance) }}" class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-edit"></i> Modifier
                        </a>
                        <a href="{{ route('materiel.maintenance.index') }}" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-arrow-left"></i> Retour
                        </a>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <div class="text-muted">Référence</div>
                            <div class="fw-bold">{{ $maintenance->reference }}</div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-muted">Date</div>
                            <div class="fw-bold">{{ optional($maintenance->date_maintenance)->format('d/m/Y') }}</div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-muted">Statut</div>
                            <div class="fw-bold">{{ ucfirst($maintenance->statut) }}</div>
                        </div>

                        <div class="col-md-6">
                            <div class="text-muted">Véhicule</div>
                            <div class="fw-bold">
                                {{ $maintenance->vehicule->marque ?? '' }} {{ $maintenance->vehicule->modele ?? '' }}
                                ({{ $maintenance->vehicule->immatriculation ?? '' }})
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-muted">Type</div>
                            <div class="fw-bold">{{ ucfirst($maintenance->type) }}</div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-muted">Kilométrage</div>
                            <div class="fw-bold">{{ number_format($maintenance->kilometrage, 0, ',', ' ') }} km</div>
                        </div>

                        <div class="col-md-6">
                            <div class="text-muted">Technicien</div>
                            <div class="fw-bold">{{ $maintenance->technicien_nom ?? ('Technicien #' . ($maintenance->technicien_id ?? '-')) }}</div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-muted">Coût</div>
                            <div class="fw-bold">{{ number_format($maintenance->cout, 0, ',', ' ') }} FCFA</div>
                        </div>
                        <div class="col-md-3">
                            <div class="text-muted">Durée</div>
                            <div class="fw-bold">{{ $maintenance->duree ?? '-' }}</div>
                        </div>

                        <div class="col-12">
                            <div class="text-muted">Description</div>
                            <div class="fw-bold">{{ $maintenance->description }}</div>
                        </div>

                        <div class="col-12">
                            <div class="text-muted">Pièces utilisées</div>
                            <div class="fw-bold">{{ $maintenance->pieces_utilisees ?? '-' }}</div>
                        </div>

                        <div class="col-md-6">
                            <div class="text-muted">Prochaine échéance</div>
                            <div class="fw-bold">{{ optional($maintenance->prochaine_echeance)->format('d/m/Y') ?? '-' }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="text-muted">Terminé le</div>
                            <div class="fw-bold">{{ optional($maintenance->completed_at)->format('d/m/Y H:i') ?? '-' }}</div>
                        </div>

                        <div class="col-md-6">
                            <div class="text-muted">Résultat</div>
                            <div class="fw-bold">{{ $maintenance->resultat ?? '-' }}</div>
                        </div>
                        <div class="col-md-6">
                            <div class="text-muted">Compte-rendu</div>
                            <div class="fw-bold">{{ $maintenance->resultat_notes ?? '-' }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@extends('layouts.app')

@section('title', 'Detail parametrage camion plateau')

@section('content')
<div class="container-fluid">
    @include('materiel.cost-control.camion-plateau._nav')

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h4 mb-0"><i class="fas fa-cog me-2 text-primary"></i>Detail parametrage</h1>
        <div class="d-flex gap-2">
            <a href="{{ route('materiel.cost-control.plateau.parametrage.edit', $param) }}" class="btn btn-warning btn-sm">
                <i class="fas fa-edit me-1"></i>Modifier
            </a>
            <a href="{{ route('materiel.cost-control.plateau.parametrage') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left me-1"></i>Retour
            </a>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="text-muted small">Engin</div>
                    <div class="fw-semibold">{{ $param->vehicle->immatriculation ?? '-' }}</div>
                </div>
                <div class="col-md-6">
                    <div class="text-muted small">Client</div>
                    <div class="fw-semibold">{{ $param->client->nom ?? '-' }}</div>
                </div>
                <div class="col-md-4">
                    <div class="text-muted small">Type facturation client</div>
                    <div class="fw-semibold">{{ $param->type_facturation === 'monthly' ? 'Forfait mensuel' : 'A la tache / voyage' }}</div>
                </div>
                <div class="col-md-4">
                    <div class="text-muted small">Prix client voyage</div>
                    <div class="fw-semibold">{{ number_format((float) $param->trip_client_price, 0, ',', ' ') }} FCFA</div>
                </div>
                <div class="col-md-4">
                    <div class="text-muted small">Forfait mensuel client</div>
                    <div class="fw-semibold">{{ number_format((float) $param->monthly_flat_rate, 0, ',', ' ') }} FCFA</div>
                </div>
                <div class="col-md-4">
                    <div class="text-muted small">Seuil voyages/mois</div>
                    <div class="fw-semibold">{{ $param->monthly_trip_threshold ?? 0 }}</div>
                </div>
                <div class="col-md-4">
                    <div class="text-muted small">Surcharge voyage supp.</div>
                    <div class="fw-semibold">{{ number_format((float) $param->extra_trip_unit_price, 0, ',', ' ') }} FCFA</div>
                </div>
                <div class="col-md-4">
                    <div class="text-muted small">Paiement fournisseur</div>
                    <div class="fw-semibold">{{ $param->supplier_type_paiement === 'monthly' ? 'Mensuel' : 'Par voyage' }}</div>
                </div>
                <div class="col-md-6">
                    <div class="text-muted small">Cout fournisseur mensuel</div>
                    <div class="fw-semibold text-danger">{{ number_format((float) $param->supplier_monthly_cost, 0, ',', ' ') }} FCFA</div>
                </div>
                <div class="col-md-6">
                    <div class="text-muted small">Cout fournisseur voyage</div>
                    <div class="fw-semibold text-danger">{{ number_format((float) $param->supplier_trip_cost, 0, ',', ' ') }} FCFA</div>
                </div>
                <div class="col-md-6">
                    <div class="text-muted small">Statut</div>
                    <div>
                        <span class="badge bg-{{ $param->is_active ? 'success' : 'danger' }}">{{ $param->is_active ? 'Actif' : 'Inactif' }}</span>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="text-muted small">Cree le</div>
                    <div class="fw-semibold">{{ optional($param->created_at)->format('d/m/Y H:i') }}</div>
                </div>
                <div class="col-12">
                    <div class="text-muted small">Notes</div>
                    <div>{{ $param->notes ?: '-' }}</div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@extends('layouts.app')

@section('title', 'Checking par Type de Matériel')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Checking par Type de Matériel</h1>
            <p class="text-muted">Vue d'ensemble des vérifications groupées par catégorie de matériel roulant.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('fleet.checking.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-list me-2"></i>Toutes les vérifications
            </a>
            <a href="{{ route('fleet.checking.create') }}" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Nouvelle Vérification
            </a>
        </div>
    </div>

    <div class="row">
        @forelse($stats as $type => $data)
            <div class="col-xl-4 col-md-6 mb-4">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    {{ $type }}
                                </div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    {{ $data['checkings_count'] }} Vérifications
                                </div>
                                <div class="mt-2 small text-muted">
                                    <i class="fas fa-truck me-1"></i> {{ $data['count'] }} matériels enregistrés
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas @if($type == 'Camion') fa-truck @elseif($type == 'Machine') fa-tractor @else fa-car @endif fa-2x text-gray-300"></i>
                            </div>
                        </div>
                        <hr>
                        <div class="mt-2">
                            @if($data['latest_checking'])
                                <div class="small">
                                    <strong>Dernière vérification :</strong><br>
                                    {{ $data['latest_checking']->title }}<br>
                                    <span class="text-muted">Par {{ optional($data['latest_checking']->inspector)->name ?? 'N/A' }} le {{ $data['latest_checking']->created_at->format('d/m/Y') }}</span>
                                </div>
                            @else
                                <div class="small text-muted italic">Aucune vérification enregistrée</div>
                            @endif
                        </div>
                        <div class="mt-3">
                            <a href="{{ route('fleet.vehicules', ['type' => $type]) }}" class="btn btn-sm btn-outline-info w-100 mb-2">
                                Voir les matériels
                            </a>
                            <a href="{{ route('fleet.checking.create', ['type_materiel' => $type]) }}" class="btn btn-sm btn-primary w-100">
                                Lancer un check
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info">
                    Aucun type de matériel roulant trouvé dans la base de données.
                </div>
            </div>
        @endforelse
    </div>
</div>
@endsection

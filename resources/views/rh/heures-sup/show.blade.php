@extends('layouts.app')

@section('title', 'Détails Heure Supplémentaire | RH')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="mb-0">
                    <i class="fas fa-clock text-warning me-2"></i>
                    Détails Heure Supplémentaire
                </h4>
                <div>
                    <a href="{{ route('rh.heures-sup.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Retour
                    </a>
                    <a href="{{ route('rh.heures-sup.edit', $heureData['id']) }}" class="btn btn-warning">
                        <i class="fas fa-edit me-2"></i>Modifier
                    </a>
                </div>
            </div>

            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-muted mb-3">Informations générales</h6>
                            <table class="table table-borderless">
                                <tr>
                                    <td class="fw-bold" width="150">Agent:</td>
                                    <td>{{ $heureData['agent_nom'] }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Date:</td>
                                    <td>{{ $heureData['date'] }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold">Nombre d'heures:</td>
                                    <td>
                                        <span class="badge bg-warning text-dark">
                                            {{ $heureData['nombre_heures'] }}h
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted mb-3">Statut</h6>
                            <div class="text-center">
                                <div class="badge bg-success fs-6 p-3">
                                    <i class="fas fa-check-circle me-2"></i>
                                    Validé
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

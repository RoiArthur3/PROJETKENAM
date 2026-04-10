@extends('layouts.app')

@section('title', 'Pointage Facial - Dashboard RH')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-id-card me-2 text-primary"></i>Pointage Facial Automatique
            </h1>
            <p class="text-muted mb-0">Vue d'ensemble du pointage automatique par reconnaissance faciale</p>
        </div>
        <div>
            <a href="{{ route('rh.facial-devices.index') }}" class="btn btn-outline-primary">
                <i class="fas fa-camera me-2"></i>Terminaux
            </a>
        </div>
    </div>

    @if(!($hasFacialDevicesTable ?? true) || !($hasFacialEventsTable ?? true))
        <div class="alert alert-warning" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            Certaines tables du module de pointage facial sont introuvables sur cette base de données:
            @if(!($hasFacialDevicesTable ?? true))
                <strong>facial_devices</strong>
            @endif
            @if(!($hasFacialDevicesTable ?? true) && !($hasFacialEventsTable ?? true))
                et
            @endif
            @if(!($hasFacialEventsTable ?? true))
                <strong>facial_events</strong>
            @endif
            . Les statistiques associées sont temporairement affichées à 0.
        </div>
    @endif

    <!-- KPIs -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card border-start border-primary border-4 shadow-sm">
                <div class="card-body">
                    <div class="text-muted small text-uppercase fw-bold">Événements aujourd'hui</div>
                    <div class="h3 mb-0 text-primary">{{ $todayEvents }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-start border-success border-4 shadow-sm">
                <div class="card-body">
                    <div class="text-muted small text-uppercase fw-bold">Pointages aujourd'hui</div>
                    <div class="h3 mb-0 text-success">{{ $todayPointages }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-start border-info border-4 shadow-sm">
                <div class="card-body">
                    <div class="text-muted small text-uppercase fw-bold">Terminaux actifs</div>
                    <div class="h3 mb-0 text-info">{{ $activeDevices }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card border-start border-warning border-4 shadow-sm">
                <div class="card-body">
                    <div class="text-muted small text-uppercase fw-bold">En ligne</div>
                    <div class="h3 mb-0 text-warning">{{ $onlineDevices }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Événements récents -->
        <div class="col-lg-8 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-light py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-history me-2"></i>Événements récents (dernière heure)
                    </h6>
                </div>
                <div class="card-body">
                    @if($recentEvents->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Heure</th>
                                        <th>Employé</th>
                                        <th>Terminal</th>
                                        <th>Sens</th>
                                        <th>Statut</th>
                                        <th>Confiance</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentEvents as $event)
                                        <tr>
                                            <td>{{ $event->event_time->format('H:i:s') }}</td>
                                            <td>
                                                @if($event->pointage && $event->pointage->personnel)
                                                    {{ $event->pointage->personnel->nom ?? $event->pointage->personnel->prenom ?? $event->employee_code }}
                                                @else
                                                    {{ $event->employee_name ?? $event->employee_code }}
                                                @endif
                                            </td>
                                            <td>{{ $event->device->name ?? '-' }}</td>
                                            <td>
                                                @switch($event->direction)
                                                    @case('entry')
                                                        <span class="badge bg-success">Entrée</span>
                                                        @break
                                                    @case('exit')
                                                        <span class="badge bg-danger">Sortie</span>
                                                        @break
                                                    @default
                                                        <span class="badge bg-secondary">{{ $event->direction }}</span>
                                                @endswitch
                                            </td>
                                            <td>
                                                @if($event->processing_status === 'processed')
                                                    <span class="badge bg-success">Traité</span>
                                                @else
                                                    <span class="badge bg-warning">En attente</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($event->confidence)
                                                    <span class="badge bg-info">{{ number_format($event->confidence, 1) }}%</span>
                                                @else
                                                    -
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-3">
                            <p class="text-muted mb-0">Aucun événement récent</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Terminaux actifs -->
        <div class="col-lg-4 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-light py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-camera me-2"></i>Terminaux actifs
                    </h6>
                </div>
                <div class="card-body">
                    @foreach($devicesWithLastEvent as $device)
                        <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                            <div>
                                <div class="fw-bold">{{ $device->name }}</div>
                                <small class="text-muted">{{ $device->ip_address }}:{{ $device->port }}</small>
                            </div>
                            <div class="text-end">
                                @if($device->last_seen_at && $device->last_seen_at->gt(now()->subMinutes(5)))
                                    <span class="badge bg-success">En ligne</span>
                                @else
                                    <span class="badge bg-secondary">Hors ligne</span>
                                @endif
                                @if($device->events->isNotEmpty())
                                    <div class="small text-muted mt-1">
                                        {{ $device->events->first()->event_time->diffForHumans() }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Pointages du jour -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-light py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-user-clock me-2"></i>Pointages du jour
                    </h6>
                </div>
                <div class="card-body">
                    @if($todayPointagesByPersonnel->count() > 0)
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Employé</th>
                                        <th>Matricule</th>
                                        <th>Arrivée</th>
                                        <th>Départ</th>
                                        <th>Statut</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($todayPointagesByPersonnel as $pointage)
                                        <tr>
                                            <td>
                                                @if($pointage->personnel)
                                                    {{ $pointage->personnel->nom }} {{ $pointage->personnel->prenom }}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>{{ $pointage->personnel->matricule ?? '-' }}</td>
                                            <td>{{ $pointage->heure_arrivee ?? '-' }}</td>
                                            <td>{{ $pointage->heure_depart ?? '-' }}</td>
                                            <td>
                                                <span class="badge bg-success">Présent</span>
                                            </td>
                                            <td>
                                                <a href="{{ route('rh.pointages.index') }}?date={{ optional($pointage->date_pointage)->format('Y-m-d') }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-list"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-3">
                            <p class="text-muted mb-0">Aucun pointage aujourd'hui</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

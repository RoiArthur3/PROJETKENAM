@extends('layouts.app')

@section('title', 'Fiche Agent - ' . $agent->name . ' - RH')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-md-8">
            <h4 class="mb-0">
                <i class="fas fa-user-circle text-primary me-2"></i>
                Fiche Personnelle - {{ $agent->name }}
            </h4>
            <small class="text-muted">Informations détaillées et statistiques de l'agent.</small>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('rh.agents.index') }}" class="btn btn-outline-secondary me-2">
                <i class="fas fa-arrow-left me-1"></i>
                Retour à la liste
            </a>
            <a href="{{ route('rh.agents.edit', $agent) }}" class="btn btn-primary">
                <i class="fas fa-edit me-1"></i>
                Modifier
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Informations personnelles -->
        <div class="col-lg-4">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0">
                        <i class="fas fa-id-card me-2"></i>
                        Informations Personnelles
                    </h6>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <div class="avatar-lg bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mx-auto mb-2">
                            <i class="fas fa-user fa-2x"></i>
                        </div>
                        <h5>{{ $agent->name }}</h5>
                        <p class="text-muted mb-0">{{ ucfirst($agent->role ?? 'N/A') }}</p>
                    </div>

                    <hr>

                    <div class="row g-2">
                        <div class="col-12">
                            <small class="text-muted">Email</small>
                            <div>{{ $agent->email }}</div>
                        </div>
                        <div class="col-12">
                            <small class="text-muted">Téléphone</small>
                            <div>{{ $agent->phone ?? 'Non renseigné' }}</div>
                        </div>
                        <div class="col-12">
                            <small class="text-muted">Service</small>
                            <div>
                                @if($agent->service)
                                    <span class="badge bg-info">{{ $agent->service->nom }}</span>
                                @else
                                    <span class="badge bg-secondary">Non assigné</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-12">
                            <small class="text-muted">Statut</small>
                            <div>
                                <span class="badge bg-{{ $agent->actif ? 'success' : 'secondary' }}">
                                    {{ $agent->actif ? 'Actif' : 'Inactif' }}
                                </span>
                            </div>
                        </div>
                        <div class="col-12">
                            <small class="text-muted">Année d'embauche</small>
                            <div>
                                @if($hireYear)
                                    <strong>{{ $hireYear }}</strong>
                                @else
                                    <span class="text-muted">Non renseignée</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- CV -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0">
                        <i class="fas fa-file-alt me-2"></i>
                        CV / Parcours Professionnel
                    </h6>
                </div>
                <div class="card-body">
                    @if($agent->cv ?? false)
                        <div>{{ $agent->cv }}</div>
                    @else
                        <div class="text-center text-muted py-3">
                            <i class="fas fa-file-alt fa-2x mb-2"></i>
                            <div>Aucun CV disponible</div>
                            <small>Le CV sera ajouté lors de la mise à jour du profil</small>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Statistiques et pointages -->
        <div class="col-lg-8">
            <!-- Statistiques mensuelles -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card shadow-sm h-100">
                        <div class="card-body text-center">
                            <div class="display-4 text-warning mb-2">
                                {{ floor($totalDelayMinutes / 60) }}h {{ $totalDelayMinutes % 60 }}min
                            </div>
                            <h6 class="text-muted mb-0">Retard Cumulé (Mois en cours)</h6>
                            <small class="text-muted">
                                {{ now()->translatedFormat('F Y') }}
                            </small>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card shadow-sm h-100">
                        <div class="card-body text-center">
                            <div class="display-4 text-success mb-2">
                                {{ \App\Models\Pointage::where('user_id', $agent->id)->whereMonth('date_pointage', now()->month)->whereYear('date_pointage', now()->year)->count() }}
                            </div>
                            <h6 class="text-muted mb-0">Jours de Présence</h6>
                            <small class="text-muted">
                                {{ now()->translatedFormat('F Y') }}
                            </small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Historique des pointages récents -->
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">
                    <h6 class="mb-0">
                        <i class="fas fa-clock me-2"></i>
                        Historique des Pointages (30 derniers jours)
                    </h6>
                </div>
                <div class="card-body">
                    @php
                        $recentPointages = \App\Models\Pointage::where('user_id', $agent->id)
                            ->where('date_pointage', '>=', now()->subDays(30))
                            ->orderBy('date_pointage', 'desc')
                            ->take(10)
                            ->get();
                    @endphp

                    @if($recentPointages->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead class="table-light">
                                    <tr>
                                        <th>Date</th>
                                        <th>Arrivée</th>
                                        <th>Départ</th>
                                        <th>Retard</th>
                                        <th>Heures</th>
                                        <th>Statut</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentPointages as $pointage)
                                        @php
                                            $delay = 0;
                                            if ($pointage->heure_arrivee) {
                                                $arrivalTime = \Carbon\Carbon::createFromFormat('H:i:s', $pointage->heure_arrivee);
                                                $delayLimit = \Carbon\Carbon::createFromTime(7, 30, 0);
                                                if ($arrivalTime->greaterThan($delayLimit)) {
                                                    $delay = $arrivalTime->diffInMinutes($delayLimit);
                                                }
                                            }
                                            $workedHours = 0;
                                            if ($pointage->heure_arrivee && $pointage->heure_depart) {
                                                $arrivalTime = \Carbon\Carbon::createFromFormat('H:i:s', $pointage->heure_arrivee);
                                                $departureTime = \Carbon\Carbon::createFromFormat('H:i:s', $pointage->heure_depart);
                                                $workedMinutes = $arrivalTime->diffInMinutes($departureTime);
                                                $workedHours = min($workedMinutes / 60, 10);
                                            }
                                        @endphp
                                        <tr>
                                            <td>{{ \Carbon\Carbon::parse($pointage->date_pointage)->format('d/m/Y') }}</td>
                                            <td>{{ $pointage->heure_arrivee ? \Carbon\Carbon::createFromFormat('H:i:s', $pointage->heure_arrivee)->format('H:i') : '-' }}</td>
                                            <td>{{ $pointage->heure_depart ? \Carbon\Carbon::createFromFormat('H:i:s', $pointage->heure_depart)->format('H:i') : '-' }}</td>
                                            <td>
                                                @if($delay > 0)
                                                    <span class="text-danger">{{ $delay }}min</span>
                                                @else
                                                    <span class="text-success">OK</span>
                                                @endif
                                            </td>
                                            <td>{{ $workedHours > 0 ? number_format($workedHours, 1) . 'h' : '-' }}</td>
                                            <td>
                                                <span class="badge bg-{{ $pointage->statut == 'present' ? 'success' : ($pointage->statut == 'retard' ? 'warning' : 'danger') }} small">
                                                    {{ ucfirst($pointage->statut) }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-clock fa-3x mb-3"></i>
                            <div>Aucun pointage récent</div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Contrat et paie -->
            @if($agent->contrat || $agent->salaire || $agent->date_embauche)
                <div class="card shadow-sm mt-4">
                    <div class="card-header bg-warning text-white">
                        <h6 class="mb-0">
                            <i class="fas fa-file-contract me-2"></i>
                            Contrat et Rémunération
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            @if($agent->contrat)
                                <div class="col-md-4">
                                    <small class="text-muted">Type de contrat</small>
                                    <div><strong>{{ $agent->contrat }}</strong></div>
                                </div>
                            @endif
                            @if($agent->date_embauche)
                                <div class="col-md-4">
                                    <small class="text-muted">Date d'embauche</small>
                                    <div>{{ \Carbon\Carbon::parse($agent->date_embauche)->format('d/m/Y') }}</div>
                                </div>
                            @endif
                            @if($agent->salaire)
                                <div class="col-md-4">
                                    <small class="text-muted">Salaire mensuel</small>
                                    <div><strong>{{ number_format($agent->salaire, 0, ',', ' ') }} FCFA</strong></div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

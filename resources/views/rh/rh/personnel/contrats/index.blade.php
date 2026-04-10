@extends('layouts.app')

@section('title', 'Gestion des Contrats | KENAM SERVICES')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="display-6 fw-bold text-dark"><i class="fas fa-file-signature me-3 text-success"></i>Gestion des Contrats</h2>
            <p class="text-muted">Suivi des engagements contractuels du personnel RH</p>
        </div>
        <a href="{{ route('personnel.contrats.create') }}" class="btn btn-success btn-lg shadow-sm">
            <i class="fas fa-plus me-2"></i>Nouveau Contrat
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 mb-4" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-muted small text-uppercase">
                        <tr>
                            <th class="ps-4 py-3">Réf / N°</th>
                            <th>Employé</th>
                            <th>Type / Poste</th>
                            <th>Début / Fin</th>
                            <th>Rémunération</th>
                            <th>Statut</th>
                            <th class="text-end pe-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($contrats as $contrat)
                        <tr>
                            <td class="ps-4">
                                <span class="fw-bold text-dark">{{ $contrat->numero_contrat }}</span>
                                <br><small class="text-muted">Créé le {{ $contrat->created_at->format('d/m/Y') }}</small>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-sm bg-info text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 35px; height: 35px;">
                                        {{ strtoupper(substr($contrat->personnel->nom, 0, 1)) }}
                                    </div>
                                    <div>
                                        <h6 class="mb-0 fw-bold">{{ $contrat->personnel->nom }} {{ $contrat->personnel->prenoms }}</h6>
                                        <small class="text-muted">Mat: {{ $contrat->personnel->matricule }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-{{ $contrat->type_contrat == 'CDI' ? 'success' : 'warning' }} px-3 py-2 mb-1">
                                    {{ $contrat->type_contrat }}
                                </span>
                                <br><small class="fw-bold">{{ $contrat->poste }}</small>
                            </td>
                            <td>
                                <div class="small">
                                    <span class="text-success fw-bold">{{ \Carbon\Carbon::parse($contrat->date_debut)->format('d/m/Y') }}</span>
                                    @if($contrat->date_fin)
                                        <br><span class="text-danger fw-bold">{{ \Carbon\Carbon::parse($contrat->date_fin)->format('d/m/Y') }}</span>
                                    @else
                                        <br><span class="text-muted italic">Indéterminé</span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <h6 class="mb-0 fw-bold text-dark">{{ number_format($contrat->salaire_base, 0, ',', ' ') }} {{ $contrat->devise }}</h6>
                                <small class="text-muted">{{ $contrat->frequence_paiement }}</small>
                            </td>
                            <td>
                                <span class="badge rounded-pill bg-{{ $contrat->statut == 'ACTIF' ? 'success' : ($contrat->statut == 'PROJET' ? 'light text-dark border' : 'secondary') }} px-3">
                                    {{ $contrat->statut }}
                                </span>
                            </td>
                            <td class="text-end pe-4">
                                <div class="dropdown">
                                    <button class="btn btn-light btn-sm rounded-circle d-flex align-items-center justify-content-center" 
                                            type="button" data-bs-toggle="dropdown" style="width: 32px; height: 32px;">
                                        <i class="fas fa-ellipsis-v text-muted"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end shadow border-0 p-2">
                                        <li><a class="dropdown-item rounded-2" href="{{ route('personnel.contrats.show', $contrat->id) }}">
                                            <i class="fas fa-eye me-2 text-info"></i>Détails
                                        </a></li>
                                        <li><a class="dropdown-item rounded-2" href="{{ route('personnel.contrats.edit', $contrat->id) }}">
                                            <i class="fas fa-edit me-2 text-warning"></i>Modifier
                                        </a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li><a class="dropdown-item rounded-2 text-danger" href="#">
                                            <i class="fas fa-trash-alt me-2"></i>Archiver
                                        </a></li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <i class="fas fa-file-contract fa-3x text-light mb-3"></i>
                                <h5 class="text-muted">Aucun contrat enregistré</h5>
                                <p class="small text-muted mb-4">Commencez par créer le premier contrat pour un employé.</p>
                                <a href="{{ route('personnel.contrats.create') }}" class="btn btn-success shadow-sm px-4">
                                    <i class="fas fa-plus me-2"></i>Créer un contrat
                                </a>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
    .table thead th {
        font-weight: 700;
        letter-spacing: 0.5px;
    }
    .avatar-sm {
        font-weight: 700;
        font-size: 14px;
    }
</style>
@endsection

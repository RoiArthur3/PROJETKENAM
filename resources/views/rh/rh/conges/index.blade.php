@extends('layouts.app')

@section('title', 'Gestion des Congés - KENAM SERVICES')

@section('content')
@php
    $colors = ['#4e73df','#1cc88a','#f6c23e','#36b9cc','#e74a3b','#858796','#5a5c69','#fd7e14'];
@endphp
<x-dashboard-layout title="Gestion des Congés" icon="fa-umbrella-beach" subtitle="Demandes et suivi des congés du personnel RH">
    <!-- KPIs -->
    <x-slot name="kpis">
        <x-kpi-card
            title="Total Demandes"
            :value="$totalConges ?? 0"
            icon="fa-calendar-alt"
            color="primary"
            subtitle="Congés demandés"
        />

        <x-kpi-card
            title="En Attente"
            :value="$enAttente ?? 0"
            icon="fa-hourglass-half"
            color="warning"
            subtitle="Validation requise"
        />

        <x-kpi-card
            title="Approuvés"
            :value="$approuves ?? 0"
            icon="fa-check-circle"
            color="success"
            subtitle="Congés validés"
        />

        <x-kpi-card
            title="Refusés"
            :value="$conges->where('statut', 'Refusé')->count() ?? 0"
            icon="fa-times-circle"
            color="danger"
            subtitle="Demandes refusées"
        />
    </x-slot>

    <!-- Filtres et Actions -->
    <div class="row mb-4">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 fw-bold text-primary">
                        <i class="fas fa-filter me-2"></i>Filtres de Recherche
                    </h6>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('rh.conges.index') }}" class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label small">Recherche</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">
                                    <i class="fas fa-search text-muted"></i>
                                </span>
                                <input type="text" name="search" class="form-control"
                                       placeholder="Nom du personnel..." value="{{ request('search') }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small">Statut</label>
                            <select name="statut" class="form-select">
                                <option value="">Tous</option>
                                <option value="En attente" {{ request('statut') == 'En attente' ? 'selected' : '' }}>En attente</option>
                                <option value="Approuvé" {{ request('statut') == 'Approuvé' ? 'selected' : '' }}>Approuvé</option>
                                <option value="Refusé" {{ request('statut') == 'Refusé' ? 'selected' : '' }}>Refusé</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small">Type de congé</label>
                            <select name="type_conge" class="form-select">
                                <option value="">Tous</option>
                                <option value="Congé annuel" {{ request('type_conge') == 'Congé annuel' ? 'selected' : '' }}>Congé annuel</option>
                                <option value="Congé maladie" {{ request('type_conge') == 'Congé maladie' ? 'selected' : '' }}>Congé maladie</option>
                                <option value="Congé maternité" {{ request('type_conge') == 'Congé maternité' ? 'selected' : '' }}>Congé maternité</option>
                                <option value="Congé sans solde" {{ request('type_conge') == 'Congé sans solde' ? 'selected' : '' }}>Congé sans solde</option>
                                <option value="Permission" {{ request('type_conge') == 'Permission' ? 'selected' : '' }}>Permission</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small">&nbsp;</label>
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-search me-1"></i>Filtrer
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card shadow-sm">
                <div class="card-header bg-white py-3">
                    <h6 class="m-0 fw-bold text-primary">
                        <i class="fas fa-plus me-2"></i>Actions
                    </h6>
                </div>
                <div class="card-body">
                    <a href="{{ route('rh.conges.create') }}" class="btn btn-primary w-100 mb-2">
                        <i class="fas fa-plus me-1"></i>Nouvelle Demande
                    </a>
                    <small class="text-muted d-block">
                        Créer une demande de congé pour le personnel RH
                    </small>
                </div>
            </div>
        </div>
    </div>

    <!-- Tableau des demandes de congé -->
    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Date Demande</th>
                            <th>Personnel RH</th>
                            <th>Type</th>
                            <th>Période</th>
                            <th>Durée</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($conges as $conge)
                            <tr>
                                <td>{{ $conge->date_demande->format('d/m/Y') }}</td>
                                <td class="fw-semibold">{{ $conge->personnel ? $conge->personnel->nom . ' ' . $conge->personnel->prenoms : ($conge->agent_nom ?? 'N/A') }}</td>
                                <td><span class="badge bg-secondary">{{ $conge->type_conge }}</span></td>
                                <td>
                                    <small>
                                        Du {{ $conge->date_debut->format('d/m/Y') }}<br>
                                        Au {{ $conge->date_fin->format('d/m/Y') }}
                                    </small>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-info">{{ $conge->nombre_jours }} jour(s)</span>
                                </td>
                                <td>
                                    @php
                                        $statutBadge = [
                                            'En attente' => 'warning',
                                            'Approuvé' => 'success',
                                            'Refusé' => 'danger',
                                        ][$conge->statut] ?? 'secondary';
                                    @endphp
                                    <span class="badge bg-{{ $statutBadge }}">{{ $conge->statut }}</span>
                                </td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-info" title="Voir détails" data-bs-toggle="modal" data-bs-target="#detailModal{{ $conge->id }}">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">
                                    <i class="fas fa-umbrella-beach fa-3x mb-3 d-block"></i>
                                    Aucune demande de congé enregistrée
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-dashboard-layout>

{{-- Modals --}}
@foreach($conges as $conge)
<div class="modal fade" id="detailModal{{ $conge->id }}" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Détails Demande de Congé #{{ $conge->id }}</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <dl class="row">
                            <dt class="col-sm-5">Personnel RH:</dt>
                            <dd class="col-sm-7">{{ $conge->personnel ? $conge->personnel->nom . ' ' . $conge->personnel->prenoms : ($conge->agent_nom ?? 'N/A') }}</dd>
                            <dt class="col-sm-5">Type de congé:</dt>
                            <dd class="col-sm-7"><span class="badge bg-secondary">{{ $conge->type_conge }}</span></dd>
                            <dt class="col-sm-5">Date demande:</dt>
                            <dd class="col-sm-7">{{ $conge->date_demande->format('d/m/Y') }}</dd>
                            <dt class="col-sm-5">Date début:</dt>
                            <dd class="col-sm-7">{{ $conge->date_debut->format('d/m/Y') }}</dd>
                            <dt class="col-sm-5">Date fin:</dt>
                            <dd class="col-sm-7">{{ $conge->date_fin->format('d/m/Y') }}</dd>
                            <dt class="col-sm-5">Durée:</dt>
                            <dd class="col-sm-7"><span class="badge bg-info">{{ $conge->nombre_jours }} jour(s)</span></dd>
                        </dl>
                    </div>
                    <div class="col-md-6">
                        @php
                            $statutBadge = [
                                'En attente' => 'warning',
                                'Approuvé' => 'success',
                                'Refusé' => 'danger',
                            ][$conge->statut] ?? 'secondary';
                        @endphp
                        <dl class="row">
                            <dt class="col-sm-5">Statut:</dt>
                            <dd class="col-sm-7"><span class="badge bg-{{ $statutBadge }}">{{ $conge->statut }}</span></dd>
                            @if($conge->valideur)
                                <dt class="col-sm-5">Validé par:</dt>
                                <dd class="col-sm-7">{{ $conge->valideur->name }}</dd>
                                <dt class="col-sm-5">Date validation:</dt>
                                <dd class="col-sm-7">{{ $conge->date_validation?->format('d/m/Y H:i') }}</dd>
                            @endif
                        </dl>
                    </div>
                </div>
                @if($conge->motif)
                    <hr>
                    <h6 class="fw-bold">Motif:</h6>
                    <p class="text-muted">{{ $conge->motif }}</p>
                @endif
                @if($conge->commentaire_valideur)
                    <hr>
                    <h6 class="fw-bold">Commentaire du valideur:</h6>
                    <p class="text-muted">{{ $conge->commentaire_valideur }}</p>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>
@endforeach
@endsection

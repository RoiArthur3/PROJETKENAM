@extends('layouts.app')

@section('title', 'Détails Client - Commercial | KENAM SERVICES')

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0">
                        <i class="fas fa-user me-2"></i>
                        Fiche Client #{{ $client->id }}
                    </h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">
                            <a href="{{ route('commercial.dashboard') }}">Commercial</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('commercial.clients.index') }}">Clients</a>
                        </li>
                        <li class="breadcrumb-item active">Détails</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">
            <!-- Informations principales -->
            <div class="row">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-info-circle me-2"></i>
                                Informations du client
                            </h3>
                            <div class="card-tools">
                                <a href="{{ route('commercial.clients.edit', $client->id) }}" class="btn btn-tool btn-sm">
                                    <i class="fas fa-edit"></i> Modifier
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label><strong>Code Client:</strong></label>
                                        <p class="form-control-static">{{ $client->code ?? '-' }}</p>
                                    </div>
                                    <div class="form-group">
                                        <label><strong>Raison Sociale:</strong></label>
                                        <p class="form-control-static">{{ $client->raison_sociale ?? '-' }}</p>
                                    </div>
                                    <div class="form-group">
                                        <label><strong>Contact Nom:</strong></label>
                                        <p class="form-control-static">{{ $client->contact_nom ?? '-' }}</p>
                                    </div>
                                    <div class="form-group">
                                        <label><strong>Contact Prénom:</strong></label>
                                        <p class="form-control-static">{{ $client->contact_prenom ?? '-' }}</p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label><strong>Email:</strong></label>
                                        <p class="form-control-static">{{ $client->email ?? '-' }}</p>
                                    </div>
                                    <div class="form-group">
                                        <label><strong>Téléphone:</strong></label>
                                        <p class="form-control-static">{{ $client->telephone ?? '-' }}</p>
                                    </div>
                                    <div class="form-group">
                                        <label><strong>Adresse:</strong></label>
                                        <p class="form-control-static">{{ $client->adresse ?? '-' }}</p>
                                    </div>
                                    <div class="form-group">
                                        <label><strong>Ville:</strong></label>
                                        <p class="form-control-static">{{ $client->ville ?? '-' }}</p>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label><strong>Type:</strong></label>
                                        <p class="form-control-static">
                                            <span class="badge bg-{{ $client->type == 'entreprise' ? 'primary' : ($client->type == 'particulier' ? 'secondary' : 'info') }}">
                                                {{ $client->type ?? 'Non défini' }}
                                            </span>
                                        </p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label><strong>Statut:</strong></label>
                                        <p class="form-control-static">
                                            <span class="badge bg-{{ $client->statut == 'actif' ? 'success' : ($client->statut == 'inactif' ? 'danger' : 'warning') }}">
                                                {{ $client->statut ?? 'Non défini' }}
                                            </span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            @if($client->notes)
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label><strong>Notes:</strong></label>
                                        <p class="form-control-static">{{ $client->notes }}</p>
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-cogs me-2"></i>
                                Actions
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <a href="{{ route('commercial.clients.edit', $client->id) }}" class="btn btn-warning">
                                    <i class="fas fa-edit me-2"></i>Modifier
                                </a>
                                <form method="POST" action="{{ route('commercial.clients.destroy', $client->id) }}" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce client ?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger w-100">
                                        <i class="fas fa-trash me-2"></i>Supprimer
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contrats du client -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-file-contract me-2"></i>
                                Contrats du client ({{ $contrats->count() }})
                            </h3>
                            <div class="card-tools">
                                <a href="{{ route('commercial.contrats.create') }}?client_id={{ $client->id }}" class="btn btn-primary btn-sm">
                                    <i class="fas fa-plus me-1"></i> Nouveau contrat
                                </a>
                            </div>
                        </div>
                        <div class="card-body">
                            @if($contrats->count() > 0)
                                <div class="table-responsive">
                                    <table class="table table-striped">
                                        <thead>
                                            <tr>
                                                <th>Numéro</th>
                                                <th>Objet</th>
                                                <th>Montant TTC</th>
                                                <th>Date début</th>
                                                <th>Date fin</th>
                                                <th>Statut</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($contrats as $contrat)
                                                <tr>
                                                    <td>{{ $contrat->numero ?? '-' }}</td>
                                                    <td>{{ $contrat->objet ?? '-' }}</td>
                                                    <td>{{ number_format($contrat->montant_ttc ?? 0, 0, ',', ' ') }} FCFA</td>
                                                    <td>{{ \Carbon\Carbon::parse($contrat->date_debut)->format('d/m/Y') }}</td>
                                                    <td>{{ \Carbon\Carbon::parse($contrat->date_fin)->format('d/m/Y') }}</td>
                                                    <td>
                                                        <span class="badge bg-{{ $contrat->statut == 'actif' ? 'success' : 'secondary' }}">
                                                            {{ $contrat->statut ?? 'Non défini' }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        <div class="btn-group">
                                                            <a href="{{ route('commercial.contrats.show', $contrat->id) }}" class="btn btn-sm btn-outline-primary">
                                                                <i class="fas fa-eye"></i>
                                                            </a>
                                                            <a href="{{ route('commercial.contrats.edit', $contrat->id) }}" class="btn btn-sm btn-outline-warning">
                                                                <i class="fas fa-edit"></i>
                                                            </a>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="text-center py-4">
                                    <i class="fas fa-file-contract fa-3x text-muted mb-3"></i>
                                    <h5 class="text-muted">Aucun contrat</h5>
                                    <p class="text-muted">
                                        Ce client n'a pas encore de contrat.
                                        <a href="{{ route('commercial.contrats.create') }}?client_id={{ $client->id }}" class="btn btn-primary">
                                            <i class="fas fa-plus me-2"></i>Créer un contrat
                                        </a>
                                    </p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
@endsection

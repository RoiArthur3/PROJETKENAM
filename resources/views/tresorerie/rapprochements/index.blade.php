@extends('layouts.app')

@section('title', 'Rapprochements de caisse')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">Historique des rapprochements</h4>
                    <a href="{{ route('tresorerie.rapprochements.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Nouveau rapprochement
                    </a>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="card-title text-muted">Total des rapprochements</h6>
                                    <h3 class="mb-0">{{ $totalRapprochements }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="card-title text-muted">Rapprochements ce mois</h6>
                                    <h3 class="mb-0">{{ $rapprochementsMois }}</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="card-title text-muted">Écart total</h6>
                                    <h3 class="mb-0 {{ $ecartTotal < 0 ? 'text-danger' : 'text-success' }}">
                                        {{ number_format($ecartTotal, 2, ',', ' ') }} FCFA
                                    </h3>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead class="text-primary">
                                <tr>
                                    <th>Date</th>
                                    <th>Référence</th>
                                    <th>Caisse</th>
                                    <th class="text-right">Solde théorique</th>
                                    <th class="text-right">Solde réel</th>
                                    <th class="text-right">Écart</th>
                                    <th>Statut</th>
                                    <th class="text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($rapprochements as $rapprochement)
                                    <tr>
                                        <td>{{ $rapprochement->date_rapprochement->format('d/m/Y H:i') }}</td>
                                        <td>{{ $rapprochement->reference ?? 'N/A' }}</td>
                                        <td>{{ $rapprochement->caisse->nom ?? 'N/A' }}</td>
                                        <td class="text-right">
                                            {{ number_format($rapprochement->solde_theorique, 2, ',', ' ') }} FCFA
                                        </td>
                                        <td class="text-right">
                                            {{ number_format($rapprochement->solde_reel, 2, ',', ' ') }} FCFA
                                        </td>
                                        <td class="text-right {{ $rapprochement->ecart < 0 ? 'text-danger' : 'text-success' }}">
                                            {{ number_format($rapprochement->ecart, 2, ',', ' ') }} FCFA
                                        </td>
                                        <td>
                                            @php
                                                $badgeClass = [
                                                    'en_cours' => 'warning',
                                                    'valide' => 'success',
                                                    'annule' => 'secondary'
                                                ][$rapprochement->statut] ?? 'secondary';
                                            @endphp
                                            <span class="badge badge-{{ $badgeClass }}">
                                                {{ ucfirst(str_replace('_', ' ', $rapprochement->statut)) }}
                                            </span>
                                        </td>
                                        <td class="text-right">
                                            <a href="{{ route('tresorerie.rapprochements.show', $rapprochement) }}"
                                               class="btn btn-info btn-sm"
                                               title="Voir les détails">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if($rapprochement->statut === 'en_cours')
                                                <a href="{{ route('tresorerie.rapprochements.edit', $rapprochement) }}"
                                                   class="btn btn-warning btn-sm"
                                                   title="Modifier">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <form action="{{ route('tresorerie.rapprochements.destroy', $rapprochement) }}"
                                                      method="POST"
                                                      class="d-inline"
                                                      onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce rapprochement ?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm" title="Supprimer">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center">
                                            Aucun rapprochement trouvé.
                                            <a href="{{ route('tresorerie.rapprochements.create') }}" class="btn btn-link">
                                                Créer un nouveau rapprochement
                                            </a>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($rapprochements->hasPages())
                        <div class="mt-4">
                            {{ $rapprochements->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

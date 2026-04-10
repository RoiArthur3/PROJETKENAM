@extends('layouts.app')

@section('title', 'Gestion des encaissements')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">Historique des encaissements</h4>
                    <a href="{{ route('tresorerie.encaissements.create') }}" class="btn btn-success">
                        <i class="fas fa-plus"></i> Nouvel encaissement
                    </a>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead class="text-primary">
                                <tr>
                                    <th>Date</th>
                                    <th>Référence</th>
                                    <th>Caisse</th>
                                    <th>Montant</th>
                                    <th>Source</th>
                                    <th>Statut</th>
                                    <th class="text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($encaissements as $encaissement)
                                    <tr>
                                        <td>{{ $encaissement->created_at->format('d/m/Y H:i') }}</td>
                                        <td>{{ $encaissement->numero_operation ?? $encaissement->reference ?? 'N/A' }}</td>
                                        <td>{{ $encaissement->caisse->nom ?? $encaissement->caisse->libelle ?? 'N/A' }}</td>
                                        <td class="text-success">
                                            {{ number_format($encaissement->montant, 2, ',', ' ') }} {{ $encaissement->devise ?? 'XOF' }}
                                        </td>
                                        <td>{{ $encaissement->source ?? 'N/A' }}</td>
                                        <td>
                                            @php
                                                $badgeClass = [
                                                    'en_attente' => 'warning',
                                                    'valide' => 'success',
                                                    'rejete' => 'danger'
                                                ][$encaissement->statut] ?? 'secondary';
                                            @endphp
                                            <span class="badge badge-{{ $badgeClass }}">
                                                {{ ucfirst($encaissement->statut) }}
                                            </span>
                                        </td>
                                        <td class="text-right">
                                            @if(\Illuminate\Support\Facades\Route::has('tresorerie.encaissements.show'))
                                                <a href="{{ route('tresorerie.encaissements.show', $encaissement) }}"
                                                   class="btn btn-info btn-sm"
                                                   title="Voir les détails">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            @endif
                                            @if($encaissement->statut === 'en_attente')
                                                @if(\Illuminate\Support\Facades\Route::has('tresorerie.encaissements.edit'))
                                                    <a href="{{ route('tresorerie.encaissements.edit', $encaissement) }}"
                                                       class="btn btn-warning btn-sm"
                                                       title="Modifier">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                @endif

                                                @if(\Illuminate\Support\Facades\Route::has('tresorerie.encaissements.destroy'))
                                                    <form action="{{ route('tresorerie.encaissements.destroy', $encaissement) }}"
                                                          method="POST"
                                                          class="d-inline"
                                                          onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet encaissement ?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-danger btn-sm" title="Supprimer">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center">
                                            Aucun encaissement trouvé.
                                            <a href="{{ route('tresorerie.encaissements.create') }}" class="btn btn-link">
                                                Créer un nouvel encaissement
                                            </a>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($encaissements->hasPages())
                        <div class="mt-4">
                            {{ $encaissements->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

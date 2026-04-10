@extends('layouts.app')

@section('title', 'Gestion des décaissements')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">Historique des décaissements</h4>
                    <a href="{{ route('tresorerie.decaissements.create') }}" class="btn btn-danger">
                        <i class="fas fa-plus"></i> Nouveau décaissement
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
                                    <th>Bénéficiaire</th>
                                    <th>Statut</th>
                                    <th class="text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($decaissements as $decaissement)
                                    <tr>
                                        <td>{{ $decaissement->created_at->format('d/m/Y H:i') }}</td>
                                        <td>{{ $decaissement->numero_operation ?? $decaissement->reference ?? 'N/A' }}</td>
                                        <td>{{ $decaissement->caisse->nom ?? $decaissement->caisse->libelle ?? 'N/A' }}</td>
                                        <td class="text-danger">
                                            {{ number_format($decaissement->montant, 2, ',', ' ') }} {{ $decaissement->devise ?? 'XOF' }}
                                        </td>
                                        <td>{{ $decaissement->beneficiaire ?? 'N/A' }}</td>
                                        <td>
                                            @php
                                                $badgeClass = [
                                                    'en_attente' => 'warning',
                                                    'valide' => 'success',
                                                    'rejete' => 'danger'
                                                ][$decaissement->statut] ?? 'secondary';
                                            @endphp
                                            <span class="badge badge-{{ $badgeClass }}">
                                                {{ ucfirst($decaissement->statut) }}
                                            </span>
                                        </td>
                                        <td class="text-right">
                                            @if(\Illuminate\Support\Facades\Route::has('tresorerie.decaissements.show'))
                                                <a href="{{ route('tresorerie.decaissements.show', $decaissement) }}"
                                                   class="btn btn-info btn-sm"
                                                   title="Voir les détails">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            @endif
                                            @if($decaissement->statut === 'en_attente')
                                                @if(\Illuminate\Support\Facades\Route::has('tresorerie.decaissements.edit'))
                                                    <a href="{{ route('tresorerie.decaissements.edit', $decaissement) }}"
                                                       class="btn btn-warning btn-sm"
                                                       title="Modifier">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                @endif

                                                @if(\Illuminate\Support\Facades\Route::has('tresorerie.decaissements.destroy'))
                                                    <form action="{{ route('tresorerie.decaissements.destroy', $decaissement) }}"
                                                          method="POST"
                                                          class="d-inline"
                                                          onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce décaissement ?')">
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
                                            Aucun décaissement trouvé.
                                            <a href="{{ route('tresorerie.decaissements.create') }}" class="btn btn-link">
                                                Créer un nouveau décaissement
                                            </a>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($decaissements->hasPages())
                        <div class="mt-4">
                            {{ $decaissements->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

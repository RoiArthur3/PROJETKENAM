@extends('layouts.app')

@section('title', 'Gestion des approvisionnements de caisse')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">Historique des approvisionnements</h4>
                    <a href="{{ route('tresorerie.approvisionnements.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Nouvel approvisionnement
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
                                    <th>Demandeur</th>
                                    <th>Statut</th>
                                    <th class="text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($approvisionnements as $approvisionnement)
                                    <tr>
                                        <td>{{ $approvisionnement->created_at->format('d/m/Y H:i') }}</td>
                                        <td>{{ $approvisionnement->numero_operation ?? $approvisionnement->reference ?? 'N/A' }}</td>
                                        <td>{{ $approvisionnement->destination->nom ?? $approvisionnement->destination->libelle ?? 'N/A' }}</td>
                                        <td class="text-success">
                                            {{ number_format($approvisionnement->montant, 2, ',', ' ') }} {{ $approvisionnement->devise ?? 'EUR' }}
                                        </td>
                                        <td>{{ $approvisionnement->demandeur->name ?? 'N/A' }}</td>
                                        <td>
                                            @php
                                                $badgeClass = [
                                                    'en_attente' => 'warning',
                                                    'valide' => 'success',
                                                    'rejete' => 'danger'
                                                ][$approvisionnement->statut] ?? 'secondary';
                                            @endphp
                                            <span class="badge badge-{{ $badgeClass }}">
                                                {{ ucfirst($approvisionnement->statut) }}
                                            </span>
                                        </td>
                                        <td class="text-right">
                                            <a href="{{ route('tresorerie.approvisionnements.show', $approvisionnement) }}"
                                               class="btn btn-info btn-sm"
                                               title="Voir les détails">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            @if($approvisionnement->statut === 'en_attente')
                                                @if(\Illuminate\Support\Facades\Route::has('tresorerie.approvisionnements.edit'))
                                                    <a href="{{ route('tresorerie.approvisionnements.edit', $approvisionnement) }}"
                                                       class="btn btn-warning btn-sm"
                                                       title="Modifier">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                @endif

                                                @if(\Illuminate\Support\Facades\Route::has('tresorerie.approvisionnements.destroy'))
                                                    <form action="{{ route('tresorerie.approvisionnements.destroy', $approvisionnement) }}"
                                                          method="POST"
                                                          class="d-inline"
                                                          onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet approvisionnement ?')">
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
                                            Aucun approvisionnement trouvé.
                                            <a href="{{ route('tresorerie.approvisionnements.create') }}" class="btn btn-link">
                                                Créer un nouvel approvisionnement
                                            </a>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($approvisionnements->hasPages())
                        <div class="mt-4">
                            {{ $approvisionnements->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

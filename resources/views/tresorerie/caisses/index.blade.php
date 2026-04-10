@extends('layouts.app')

@section('title', 'Gestion des caisses')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="card-title">Liste des caisses</h4>
                    <a href="{{ route('tresorerie.caisses.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Nouvelle caisse
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
                                    <th>Nom</th>
                                    <th>Solde actuel</th>
                                    <th>Devise</th>
                                    <th>Dernière mise à jour</th>
                                    <th>Statut</th>
                                    <th class="text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($caisses as $caisse)
                                    <tr>
                                        <td>{{ $caisse->nom ?? $caisse->libelle ?? '-' }}</td>
                                        <td class="text-{{ $caisse->solde_actuel >= 0 ? 'success' : 'danger' }}">
                                            {{ number_format($caisse->solde_actuel, 2, ',', ' ') }}
                                        </td>
                                        <td>{{ $caisse->devise ?? 'EUR' }}</td>
                                        <td>{{ $caisse->updated_at ? $caisse->updated_at->format('d/m/Y H:i') : '-' }}</td>
                                        <td>
                                            <span class="badge badge-{{ $caisse->est_active ? 'success' : 'secondary' }}">
                                                {{ $caisse->est_active ? 'Active' : 'Inactive' }}
                                            </span>
                                        </td>
                                        <td class="text-right">
                                            <a href="{{ route('tresorerie.caisses.show', $caisse) }}"
                                               class="btn btn-info btn-sm"
                                               title="Voir les détails">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('tresorerie.caisses.edit', $caisse) }}"
                                               class="btn btn-warning btn-sm"
                                               title="Modifier">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('tresorerie.caisses.destroy', $caisse) }}"
                                                  method="POST"
                                                  class="d-inline"
                                                  onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette caisse ?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm" title="Supprimer">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">
                                            Aucune caisse n'a été trouvée.
                                            <a href="{{ route('tresorerie.caisses.create') }}" class="btn btn-link">
                                                Créer une nouvelle caisse
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
    </div>
</div>
@endsection

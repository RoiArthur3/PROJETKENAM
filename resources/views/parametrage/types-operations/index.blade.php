@extends('layouts.app')

@section('title', 'Types d\'Opérations - Paramétrage')

@section('content')
<div class="container-fluid">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-cogs me-2 text-primary"></i>Types de requêtes
            </h1>
            <p class="text-muted mb-0">Gérer les types de requêtes disponibles dans le système</p>
        </div>
        <a href="{{ route('types-operations.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Nouveau Type
        </a>
    </div>

    <div class="card shadow">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Liste des Types de requêtes</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Code</th>
                            <th>Libellé</th>
                            <th>Description</th>
                            <th>Couleur</th>
                            <th>Icône</th>
                            <th>Statut</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($types as $type)
                            <tr>
                                <td><code>{{ $type->code }}</code></td>
                                <td class="fw-semibold">{{ $type->libelle }}</td>
                                <td>{{ Str::limit($type->description, 50) }}</td>
                                <td>
                                    @if($type->couleur)
                                        <span class="badge bg-{{ $type->couleur }}">{{ $type->couleur }}</span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    @if($type->icone)
                                        <i class="fas {{ $type->icone }} me-1"></i>{{ $type->icone }}
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    @if($type->actif)
                                        <span class="badge bg-success">Actif</span>
                                    @else
                                        <span class="badge bg-secondary">Inactif</span>
                                    @endif
                                </td>
                                <td class="text-end text-nowrap">
                                    <a href="{{ route('types-operations.show', $type->id) }}"
                                       class="btn btn-sm btn-info" title="Voir">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('types-operations.edit', $type->id) }}"
                                       class="btn btn-sm btn-warning" title="Modifier">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('types-operations.destroy', $type->id) }}"
                                          method="POST" class="d-inline"
                                          onsubmit="return confirm('Supprimer ce type d\'opération ?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" title="Supprimer">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted">Aucun type d'opération défini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

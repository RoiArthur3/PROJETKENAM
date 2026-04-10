@extends('layouts.app')

@section('title', 'Checking - Liste')

@section('content')
<div class="container-fluid">
  <div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3 mb-0">Vérifications (Checking)</h1>
    <a href="{{ route('fleet.checking.create') }}" class="btn btn-primary">
      <i class="fas fa-plus me-2"></i>Nouvelle vérification
    </a>
  </div>

  <div class="card shadow-sm">
    <div class="card-body table-responsive">
      <table class="table table-hover align-middle">
        <thead class="table-light">
          <tr>
            <th>Réf</th>
            <th>Titre</th>
            <th>Type</th>
            <th>Inspecteur</th>
            <th>Objet (Parc)</th>
            <th>Statut</th>
            <th>Planifiée</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
        @forelse($checkings as $c)
          <tr>
            <td>#{{ $c->id }}</td>
            <td>{{ $c->title }}</td>
            <td><span class="badge bg-info">{{ $c->type ?? '—' }}</span></td>
            <td>{{ optional($c->inspector)->name ?? '—' }}</td>
            <td>
              @if($c->checkable && \Illuminate\Support\Str::contains($c->checkable_type, 'Vehicule'))
                <a href="{{ route('fleet.vehicules.show', $c->checkable->id) }}">
                  {{ $c->checkable->immatriculation }} - {{ $c->checkable->marque }} {{ $c->checkable->modele }}
                </a>
                <br>
                <small class="text-muted">Type: {{ $c->checkable->type_materiel ?? 'N/A' }}</small>
              @else
                —
              @endif
            </td>
            <td><span class="badge bg-secondary">{{ $c->formatted_status ?? $c->status }}</span></td>
            <td>{{ optional($c->scheduled_at)->format('Y-m-d H:i') ?? '—' }}</td>
            <td>
              <a href="{{ route('fleet.checking.show', $c->id) }}" class="btn btn-sm btn-outline-primary">Voir</a>
            </td>
          </tr>
        @empty
          <tr><td colspan="8" class="text-center text-muted">Aucune vérification</td></tr>
        @endforelse
        </tbody>
      </table>
      <div class="mt-3">{{ $checkings->links() }}</div>
    </div>
  </div>
</div>
@endsection

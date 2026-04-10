@extends('layouts.app')

@section('title', 'Paramétrages - Centres de Coût')

@section('content')
<div class="container-fluid">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h4 mb-0"><i class="fas fa-coins me-2 text-danger"></i>Centres de Coût</h1>
    <a href="{{ route('admin.settings') }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-arrow-left me-1"></i>Retour</a>
  </div>

  <div class="card shadow-sm">
    <div class="card-body">
      @if (session('status'))
        <div class="alert alert-success py-2">{{ session('status') }}</div>
      @endif
      @if ($errors->any())
        <div class="alert alert-danger py-2"><ul class="mb-0">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
      @endif
      <div class="row g-3 align-items-end mb-3">
        <div class="col-md-8">
          <label class="form-label fw-bold">Nouveau centre</label>
          <form class="row g-2" method="POST" action="{{ route('admin.settings.finances.centres-cout.store') }}">
            @csrf
            <div class="col-md-8"><input type="text" name="name" class="form-control" placeholder="Ex: Parc Auto, Opérations, Projets" required></div>
            <div class="col-md-4"><input type="text" name="code" class="form-control" placeholder="Ex: PARC" required></div>
            <div class="col-12 col-md-3"><button class="btn btn-primary w-100"><i class="fas fa-plus me-2"></i>Ajouter</button></div>
          </form>
        </div>
      </div>

      <div class="table-responsive">
        <table class="table table-hover" id="centresCoutTable">
          <thead class="table-light">
            <tr>
              <th>Code</th>
              <th>Intitulé</th>
              <th>Actif</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            @forelse(($items ?? []) as $it)
              <tr>
                <td class="fw-semibold">{{ $it->code }}</td>
                <td>{{ $it->name }}</td>
                <td>{!! $it->active ? '<span class="badge bg-success">Oui</span>' : '<span class="badge bg-secondary">Non</span>' !!}</td>
                <td>
                  <form method="POST" action="{{ route('admin.settings.finances.centres-cout.destroy', $it->id) }}" onsubmit="return confirm('Supprimer cet élément ?');">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-sm btn-outline-danger"><i class="fas fa-trash"></i></button>
                  </form>
                </td>
              </tr>
            @empty
              <tr><td colspan="4" class="text-muted">Aucun élément</td></tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script>$(function(){ $('#centresCoutTable').DataTable(); });</script>
@endsection

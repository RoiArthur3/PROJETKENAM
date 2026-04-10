@extends('layouts.app')

@section('title', 'Bulletin de Paie')

@section('content')
<div class="container-fluid">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h4 mb-0"><i class="fas fa-file-invoice-dollar me-2 text-primary"></i>Bulletin #{{ $id }}</h1>
    <div class="d-flex gap-2">
      <a href="{{ route('rh.paie') }}" class="btn btn-outline-secondary"><i class="fas fa-arrow-left me-2"></i>Retour à la paie</a>
      <a href="{{ route('rh.paie.bulletins.download', $id) }}" class="btn btn-primary"><i class="fas fa-download me-2"></i>Télécharger</a>
    </div>
  </div>

  <div class="card shadow-sm mb-3">
    <div class="card-body">
      @if (request('edit'))
        <form class="row g-3">
          <div class="col-md-4">
            <label class="form-label">Salaire brut (FCFA)</label>
            <input type="number" class="form-control" value="1500000">
          </div>
          <div class="col-md-4">
            <label class="form-label">Charges (FCFA)</label>
            <input type="number" class="form-control" value="450000">
          </div>
          <div class="col-md-4">
            <label class="form-label">Net à payer (FCFA)</label>
            <input type="number" class="form-control" value="1050000">
          </div>
          <div class="col-12">
            <button type="button" class="btn btn-success"><i class="fas fa-save me-2"></i>Enregistrer (démo)</button>
          </div>
        </form>
      @else
        <div class="row">
          <div class="col-md-4">
            <div class="text-muted small">Salaire brut</div>
            <div class="h5">1 500 000 FCFA</div>
          </div>
          <div class="col-md-4">
            <div class="text-muted small">Charges</div>
            <div class="h5">450 000 FCFA</div>
          </div>
          <div class="col-md-4">
            <div class="text-muted small">Net à payer</div>
            <div class="h5 text-success">1 050 000 FCFA</div>
          </div>
        </div>
      @endif
    </div>
  </div>

  <div class="card shadow-sm">
    <div class="card-header">Détail (démo)</div>
    <div class="card-body">
      <ul class="mb-0">
        <li>Prime transport: 50 000 FCFA</li>
        <li>Prime panier: 30 000 FCFA</li>
        <li>CNPS: 120 000 FCFA</li>
      </ul>
    </div>
  </div>
</div>
@endsection

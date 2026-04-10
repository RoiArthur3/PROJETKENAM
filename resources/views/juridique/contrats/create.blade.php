@extends('layouts.app')
@section('title', 'Nouveau contrat')
@section('content')
<div class="content-wrapper"><div class="card"><div class="card-body">
    <h1 class="h4 mb-4">Nouveau contrat</h1>
    <form method="POST" action="{{ route('juridique.contrats.store') }}">@csrf
        <div class="row g-3">
            <div class="col-md-4"><label class="form-label">Référence</label><input name="reference" class="form-control" value="{{ old('reference') }}"></div>
            <div class="col-md-8"><label class="form-label">Titre</label><input name="titre" class="form-control" value="{{ old('titre') }}" required></div>
            <div class="col-md-4"><label class="form-label">Type</label><input name="type_contrat" class="form-control" value="{{ old('type_contrat') }}"></div>
            <div class="col-md-8"><label class="form-label">Partie contractante</label><input name="partie_contractante" class="form-control" value="{{ old('partie_contractante') }}"></div>
            <div class="col-md-4"><label class="form-label">Date signature</label><input type="date" name="date_signature" class="form-control" value="{{ old('date_signature') }}"></div>
            <div class="col-md-4"><label class="form-label">Date début</label><input type="date" name="date_debut" class="form-control" value="{{ old('date_debut') }}"></div>
            <div class="col-md-4"><label class="form-label">Date fin</label><input type="date" name="date_fin" class="form-control" value="{{ old('date_fin') }}"></div>
            <div class="col-md-4"><label class="form-label">Montant</label><input type="number" step="0.01" name="montant" class="form-control" value="{{ old('montant') }}"></div>
            <div class="col-md-2"><label class="form-label">Devise</label><input name="devise" class="form-control" value="{{ old('devise', 'XOF') }}"></div>
            <div class="col-md-3"><label class="form-label">Statut</label><input name="statut" class="form-control" value="{{ old('statut', 'brouillon') }}"></div>
            <div class="col-12"><label class="form-label">Objet</label><textarea name="objet" class="form-control" rows="3">{{ old('objet') }}</textarea></div>
            <div class="col-12"><label class="form-label">Notes</label><textarea name="notes" class="form-control" rows="3">{{ old('notes') }}</textarea></div>
        </div>
        <div class="mt-4 d-flex gap-2"><button class="btn btn-primary">Enregistrer</button><a href="{{ route('juridique.contrats.index') }}" class="btn btn-secondary">Annuler</a></div>
    </form>
</div></div></div>
@endsection

@extends('layouts.app')

@section('title', 'Validation DG de la demande d\'approvisionnement')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Validation DG - Demande {{ $demande->numero_demande }}</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Montant</label>
                            <input type="number" name="montant" class="form-control" value="{{ old('montant', $demande->montant) }}" min="1" step="0.01" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Commentaire (optionnel)</label>
                            <textarea name="commentaire" class="form-control" rows="3" placeholder="Votre commentaire...">{{ old('commentaire', $demande->commentaire_dg ?? '') }}</textarea>
                        </div>
                        <button type="submit" class="btn btn-success">Approuver et ordonner l'approvisionnement</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

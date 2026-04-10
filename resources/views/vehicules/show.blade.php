@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Détails du véhicule</div>

                <div class="card-body">
                    <div class="mb-3">
                        <strong>Marque:</strong> {{ $vehicule->marque }}
                    </div>

                    <div class="mb-3">
                        <strong>Modèle:</strong> {{ $vehicule->modele }}
                    </div>

                    <div class="mb-3">
                        <strong>Immatriculation:</strong> {{ $vehicule->immatriculation }}
                    </div>

                    <div class="d-flex gap-2">
                        <a href="{{ route('materiel.vehicules.edit', $vehicule) }}" class="btn btn-warning">Modifier</a>
                        <form action="{{ route('materiel.vehicules.destroy', $vehicule) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">Supprimer</button>
                        </form>
                        <a href="{{ route('materiel.vehicules') }}" class="btn btn-secondary">Retour</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

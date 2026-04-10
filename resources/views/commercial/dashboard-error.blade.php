@extends('layouts.app')

@section('title', 'Erreur Dashboard - KENAM SERVICES')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card shadow border-danger">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-exclamation-triangle me-2"></i>Erreur de connexion à la base de données
                    </h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-warning">
                        <i class="fas fa-info-circle me-2"></i>
                        Le tableau de bord commercial ne peut pas afficher les données car la base de données n'est pas accessible.
                    </div>

                    @if(isset($error))
                        <div class="alert alert-danger">
                            <strong>Détail de l'erreur :</strong><br>
                            <code>{{ $error }}</code>
                        </div>
                    @endif

                    <div class="mt-4">
                        <h6>Solutions possibles :</h6>
                        <ul>
                            <li>Vérifiez que le serveur de base de données est démarré</li>
                            <li>Vérifiez les informations de connexion dans le fichier .env</li>
                            <li>Exécutez les migrations : <code>php artisan migrate</code></li>
                        </ul>
                    </div>

                    <div class="mt-4">
                        <a href="{{ route('commercial.dashboard') }}" class="btn btn-outline-primary">
                            <i class="fas fa-vial me-2"></i>Tester la connexion
                        </a>
                        <button onclick="window.location.reload()" class="btn btn-primary ms-2">
                            <i class="fas fa-redo me-2"></i>Réessayer
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

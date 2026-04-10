@extends('layouts.app')

@section('title', 'Accueil')

@section('content')
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-md-6 text-center">
            <div class="card shadow">
                <div class="card-body">
                    <h1 class="card-title">
                        <i class="fas fa-home me-2"></i>
                        Bienvenue sur KENAM Services
                    </h1>
                    <p class="text-muted">
                        Vous êtes connecté avec succès.
                    </p>
                    <a href="{{ route('dashboard') }}" class="btn btn-primary">
                        <i class="fas fa-tachometer-alt me-1"></i>
                        Aller au Tableau de Bord
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

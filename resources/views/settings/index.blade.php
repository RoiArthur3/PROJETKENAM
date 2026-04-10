@extends('layouts.app')

@section('title', 'Paramètres - KENAM SERVICES')

@php
    // Récupérer les services pour l'affichage
    $services = \App\Models\ServiceOperationnel::orderBy('nom')->get();
    // Définir les variables pour éviter les erreurs
    $errors = session()->get('errors', new \Illuminate\Support\MessageBag());
@endphp

@section('content')
<div class="container-fluid">
    <!-- En-tête -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-cog me-2 text-primary"></i>Paramètres Système
            </h1>
            <p class="text-muted mb-0">Configuration et administration du système</p>
        </div>
    </div>

    <!-- Alertes -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Grille des modules -->
    <div class="row">
        <!-- Gestion des utilisateurs -->
        <div class="col-md-3 mb-3">
            <a href="{{ route('settings.users.index') }}" class="card h-100 text-decoration-none text-dark">
                <div class="card-body text-center">
                    <i class="fas fa-users fa-2x mb-3 text-primary"></i>
                    <h5 class="card-title">Utilisateurs</h5>
                    <p class="card-text text-muted">Gestion des comptes utilisateur et rôles</p>
                </div>
            </a>
        </div>

        <!-- Rôles et permissions -->
        <div class="col-md-3 mb-3">
            <a href="{{ route('settings.roles.index') }}" class="card h-100 text-decoration-none text-dark">
                <div class="card-body text-center">
                    <i class="fas fa-user-shield fa-2x mb-3 text-warning"></i>
                    <h5 class="card-title">Rôles</h5>
                    <p class="card-text text-muted">Gestion des rôles et permissions</p>
                </div>
            </a>
        </div>

        <!-- Paramètres généraux -->
        <div class="col-md-3 mb-3">
            <a href="{{ route('settings.general.index') }}" class="card h-100 text-decoration-none text-dark">
                <div class="card-body text-center">
                    <i class="fas fa-sliders-h fa-2x mb-3 text-info"></i>
                    <h5 class="card-title">Paramètres Généraux</h5>
                    <p class="card-text text-muted">Configuration générale de l'application</p>
                </div>
            </a>
        </div>

        <!-- Paramètres de l'entreprise -->
        <div class="col-md-3 mb-3">
            <a href="{{ route('settings.company.index') }}" class="card h-100 text-decoration-none text-dark">
                <div class="card-body text-center">
                    <i class="fas fa-building fa-2x mb-3 text-warning"></i>
                    <h5 class="card-title">Entreprise</h5>
                    <p class="card-text text-muted">Informations et paramètres de l'entreprise</p>
                </div>
            </a>
        </div>

        <!-- Gestionnaire de fichiers -->
        <div class="col-md-3 mb-3">
            <a href="{{ route('settings.file.manager') }}" class="card h-100 text-decoration-none text-dark">
                <div class="card-body text-center">
                    <i class="fas fa-folder-open fa-2x mb-3 text-warning"></i>
                    <h5 class="card-title">Fichiers</h5>
                    <p class="card-text text-muted">Gestion des fichiers et documents</p>
                </div>
            </a>
        </div>

        <!-- Paramètres de notification -->
        <div class="col-md-3 mb-3">
            <a href="{{ route('settings.notifications.index') }}" class="card h-100 text-decoration-none text-dark">
                <div class="card-body text-center">
                    <i class="fas fa-bell fa-2x mb-3 text-danger"></i>
                    <h5 class="card-title">Notifications</h5>
                    <p class="card-text text-muted">Configuration des notifications système</p>
                </div>
            </a>
        </div>

        <!-- Services -->
        <div class="col-md-3 mb-3">
            <a href="/admin/services/create" class="card h-100 text-decoration-none text-dark">
                <div class="card-body text-center">
                    <i class="fas fa-concierge-bell fa-2x mb-3 text-primary"></i>
                    <h5 class="card-title">Services</h5>
                    <p class="card-text text-muted">Gestion des services et chaînes de validation</p>
                </div>
            </a>
        </div>

        <!-- Logs Système -->
        <div class="col-md-3 mb-3">
            <a href="{{ route('settings.logs.index') }}" class="card h-100 text-decoration-none text-dark">
                <div class="card-body text-center">
                    <i class="fas fa-file-alt fa-2x mb-3 text-secondary"></i>
                    <h5 class="card-title">Logs Système</h5>
                    <p class="card-text text-muted">Consultation et gestion des logs système</p>
                </div>
            </a>
        </div>

        <!-- Nettoyage Données -->
        <div class="col-md-3 mb-3">
            <a href="{{ route('settings.cleanup.index') }}" class="card h-100 text-decoration-none text-dark">
                <div class="card-body text-center">
                    <i class="fas fa-broom fa-2x mb-3 text-danger"></i>
                    <h5 class="card-title">Nettoyage Données</h5>
                    <p class="card-text text-muted">Suppression des données par date</p>
                </div>
            </a>
        </div>
    </div>
</div>

@endsection

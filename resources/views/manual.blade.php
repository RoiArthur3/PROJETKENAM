@extends('layouts.app')

@section('title', 'Manuel d\'utilisation | KENAM SERVICES')

@section('content')
<div class="container-fluid py-3">
    <div class="row">
        <div class="col-lg-9">
            <div class="card shadow-sm border-0 mb-3">
                <div class="card-header bg-white d-flex align-items-center">
                    <i class="fas fa-book-open text-success me-2"></i>
                    <h5 class="mb-0">Manuel d'utilisation</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-4">Ce guide présente les principes d'utilisation de la plateforme KENAM SERVICES et les bonnes pratiques pour chaque module.</p>

                    <h6 class="text-success"><i class="fas fa-sign-in-alt me-2"></i>Connexion</h6>
                    <ul class="mb-4">
                        <li>Accédez à la page de connexion et entrez votre email et mot de passe.</li>
                        <li>Option "Se souvenir de moi" pour rester connecté.</li>
                        <li>L'accès au tableau de bord général est réservé aux administrateurs ou aux comptes explicitement autorisés.</li>
                    </ul>

                    <h6 class="text-success"><i class="fas fa-user-shield me-2"></i>Rôles et permissions</h6>
                    <ul class="mb-4">
                        <li>Les accès sont gérés par <strong>rôles</strong> (Spatie Permission).</li>
                        <li>Un rôle regroupe automatiquement les permissions nécessaires à un module.</li>
                        <li>La gestion des rôles se fait via Administration > Utilisateurs.</li>
                    </ul>

                    <h6 class="text-success"><i class="fas fa-layer-group me-2"></i>Navigation générale</h6>
                    <ul class="mb-4">
                        <li>Le menu latéral permet d'accéder aux modules: Opérations, Comptabilité, Parc Auto, Stock, RH, Fournisseurs, etc.</li>
                        <li>Chaque liste utilise un gabarit standard avec KPIs, filtres, tableau et pagination.</li>
                        <li>Les actions (voir, modifier, supprimer, approuver) sont accessibles en bout de ligne.</li>
                    </ul>

                    <h6 class="text-success"><i class="fas fa-scale-balanced me-2"></i>Comptabilité & Facturation</h6>
                    <ol class="mb-4">
                        <li>Définir les filtres (catégorie, statut, dates) puis cliquer Rechercher.</li>
                        <li>Créer une dépense via le bouton "Nouvelle Dépense" et compléter les champs requis.</li>
                        <li>Approuver ou rejeter une dépense depuis la liste (statut En attente).</li>
                        <li>Consulter le <strong>Journal</strong> pour voir les écritures (factures et dépenses) avec pagination.</li>
                        <li>Exporter les listes en CSV si disponible.</li>
                    </ol>

                    <h6 class="text-success"><i class="fas fa-car me-2"></i>Parc Auto</h6>
                    <ul class="mb-4">
                        <li>Accédez au tableau de bord Parc pour visualiser les indicateurs clés.</li>
                        <li>Gérez véhicules, entretiens et documents depuis le module dédié.</li>
                    </ul>

                    <h6 class="text-success"><i class="fas fa-warehouse me-2"></i>Stock & Entrepôt</h6>
                    <ul class="mb-4">
                        <li>Consultez le tableau de bord Stock et les listes de produits, mouvements, entrepôts.</li>
                        <li>Utilisez les filtres et exportez si nécessaire.</li>
                    </ul>

                    <h6 class="text-success"><i class="fas fa-users me-2"></i>Ressources Humaines</h6>
                    <ul class="mb-4">
                        <li>Accédez aux agents, pointages et rapports RH selon vos droits.</li>
                    </ul>

                    <h6 class="text-success"><i class="fas fa-truck-field me-2"></i>Fournisseurs</h6>
                    <ul class="mb-4">
                        <li>Gestion des fournisseurs, contrats, commandes et livraisons.</li>
                    </ul>

                    <h6 class="text-success"><i class="fas fa-life-ring me-2"></i>Support & bonnes pratiques</h6>
                    <ul class="mb-1">
                        <li>Vérifiez vos filtres si une liste est vide.</li>
                        <li>Respectez les formats (dates, montants) et joignez les pièces si requis.</li>
                        <li>En cas d'erreur d'accès, contactez un administrateur pour la mise à jour du rôle.</li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-lg-3">
            <div class="card shadow-sm border-0 mb-3">
                <div class="card-header bg-white d-flex align-items-center">
                    <i class="fas fa-list-ul text-success me-2"></i>
                    <h6 class="mb-0">Sommaire</h6>
                </div>
                <div class="card-body small">
                    <ol class="mb-0">
                        <li>Connexion</li>
                        <li>Rôles et permissions</li>
                        <li>Navigation générale</li>
                        <li>Comptabilité & Facturation</li>
                        <li>Parc Auto</li>
                        <li>Stock & Entrepôt</li>
                        <li>Ressources Humaines</li>
                        <li>Fournisseurs</li>
                        <li>Support & bonnes pratiques</li>
                    </ol>
                </div>
            </div>
            <div class="card shadow-sm border-0">
                <div class="card-body d-flex align-items-center">
                    <img src="{{ asset('images/logo-kenam.png') }}" alt="Logo" style="height:36px" class="me-2">
                    <div>
                        <div class="fw-bold">KENAM SERVICES</div>
                        <div class="text-muted small">Manuel d'utilisation</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

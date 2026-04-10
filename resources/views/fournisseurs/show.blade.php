@extends('layouts.app')

@section('title', 'Fournisseur - Détail | KENAM SERVICES')

@section('content')
<div class="content-wrapper">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-8">
                    <h1 class="m-0 d-flex align-items-center gap-2 flex-wrap">
                        @if($fournisseur->logo_path)
                            <img src="{{ asset('storage/' . $fournisseur->logo_path) }}"
                                 alt="Logo" class="rounded border"
                                 style="max-height:38px;max-width:55px;object-fit:contain;">
                        @else
                            <span class="avatar-initials bg-primary text-white">
                                {{ strtoupper(substr($fournisseur->raison_sociale ?? 'F', 0, 2)) }}
                            </span>
                        @endif
                        {{ $fournisseur->raison_sociale ?? $fournisseur->nom ?? 'Fournisseur sans nom' }}
                        <span class="badge bg-{{ $fournisseur->est_actif ? 'success' : 'secondary' }} fs-6 align-middle">
                            {{ $fournisseur->est_actif ? 'Actif' : 'Inactif' }}
                        </span>
                    </h1>
                    <p class="mb-0 text-muted mt-1 small">
                        @if($fournisseur->categorie ?? null)
                            <span class="me-3"><i class="fas fa-tag me-1"></i>{{ $fournisseur->categorie->nom }}</span>
                        @endif
                        @if($fournisseur->ville ?? null)
                            <span class="me-3"><i class="fas fa-map-marker-alt me-1"></i>{{ $fournisseur->ville }}</span>
                        @endif
                        @if($fournisseur->telephone ?? null)
                            <span><i class="fas fa-phone me-1"></i>{{ $fournisseur->telephone }}</span>
                        @endif
                    </p>
                </div>
                <div class="col-sm-4">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item">
                            <a href="{{ route('fournisseurs.index') }}">Fournisseurs</a>
                        </li>
                        <li class="breadcrumb-item active">{{ $fournisseur->raison_sociale ?? 'Détails' }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <section class="content">
        <div class="container-fluid">

            {{-- ── BARRE D'ACTIONS ── --}}
            <div class="d-flex gap-2 mb-4 flex-wrap">
                <a href="{{ route('fournisseurs.edit', $fournisseur->id) }}" class="btn btn-warning btn-sm">
                    <i class="fas fa-edit me-1"></i> Modifier
                </a>
                <a href="{{ route('fournisseurs.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-arrow-left me-1"></i> Retour à la liste
                </a>
                <button type="button" class="btn btn-outline-info btn-sm" onclick="window.print()">
                    <i class="fas fa-print me-1"></i> Imprimer
                </button>
            </div>

            {{-- ── STATISTIQUES (pleine largeur) ── --}}
            <div class="row g-3 mb-4">
                <div class="col-6 col-md-3">
                    <div class="card text-center border-0 shadow-sm h-100" style="border-left:4px solid #0d6efd!important;">
                        <div class="card-body py-3">
                            <div class="fs-1 fw-bold text-primary">
                                @if(Schema::hasTable('commandes_fournisseurs'))
                                    {{ DB::table('commandes_fournisseurs')->where('fournisseur_id', $fournisseur->id)->count() }}
                                @else 0 @endif
                            </div>
                            <div class="text-muted small mt-1"><i class="fas fa-shopping-cart me-1"></i>Commandes</div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="card text-center border-0 shadow-sm h-100" style="border-left:4px solid #198754!important;">
                        <div class="card-body py-3">
                            <div class="fs-1 fw-bold text-success">
                                @if(Schema::hasTable('factures_fournisseurs'))
                                    {{ DB::table('factures_fournisseurs')->where('fournisseur_id', $fournisseur->id)->count() }}
                                @else 0 @endif
                            </div>
                            <div class="text-muted small mt-1"><i class="fas fa-file-invoice me-1"></i>Factures</div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="card text-center border-0 shadow-sm h-100" style="border-left:4px solid #0dcaf0!important;">
                        <div class="card-body py-3">
                            <div class="fs-1 fw-bold text-info">{{ $materielsFournis->count() }}</div>
                            <div class="text-muted small mt-1"><i class="fas fa-truck me-1"></i>Engins</div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="card text-center border-0 shadow-sm h-100" style="border-left:4px solid #ffc107!important;">
                        <div class="card-body py-3">
                            <div class="fs-1 fw-bold text-warning">{{ $mouvementsPaiements['recu_nombre'] ?? 0 }}</div>
                            <div class="text-muted small mt-1"><i class="fas fa-money-check-alt me-1"></i>Paiements reçus</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ── ENGINS ET DISPONIBILITÉ (pleine largeur, directement sous stats) ── --}}
            <div class="card mb-4 shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center py-3"
                     style="background:linear-gradient(135deg,#e8f4fd,#f0f7ff);">
                    <h5 class="m-0 text-info">
                        <i class="fas fa-truck me-2"></i>Engins proposés par ce fournisseur
                    </h5>
                    <span class="badge bg-info rounded-pill fs-6">{{ $materielsFournis->count() }} engin(s)</span>
                </div>
                <div class="card-body p-0">
                    @if($materielsFournis->isNotEmpty())
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="ps-3">Type</th>
                                        <th>Marque / Modèle</th>
                                        <th>Immatriculation</th>
                                        <th>Statut</th>
                                        <th>Disponibilité</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($materielsFournis as $engin)
                                        @php
                                            $statut     = $engin->statut ?? 'inconnu';
                                            $dispo      = ($statut === 'disponible') || ($engin->disponible ?? false);
                                            $statutColor = match($statut) {
                                                'disponible'              => 'success',
                                                'en_mission','en_service' => 'primary',
                                                'en_panne','hors_service' => 'danger',
                                                'maintenance'             => 'warning',
                                                default                   => 'secondary',
                                            };
                                        @endphp
                                        <tr>
                                            <td class="ps-3">
                                                <span class="badge bg-secondary fw-normal">{{ $engin->type_materiel ?? '-' }}</span>
                                            </td>
                                            <td class="fw-semibold">
                                                {{ trim(($engin->marque ?? '') . ' ' . ($engin->modele ?? '')) ?: '-' }}
                                            </td>
                                            <td><code class="text-dark">{{ $engin->immatriculation ?? '-' }}</code></td>
                                            <td><span class="badge bg-{{ $statutColor }}">{{ ucfirst(str_replace('_', ' ', $statut)) }}</span></td>
                                            <td>
                                                @if($dispo)
                                                    <span class="badge bg-success"><i class="fas fa-check me-1"></i>Disponible</span>
                                                @else
                                                    <span class="badge bg-danger"><i class="fas fa-times me-1"></i>Indisponible</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5 text-muted">
                            <i class="fas fa-truck fa-3x mb-3 d-block opacity-25"></i>
                            <p class="mb-1 fw-semibold">Aucun engin enregistré pour ce fournisseur</p>
                            <small>Les engins apparaissent ici lorsqu'ils sont associés à ce fournisseur<br>
                            dans la flotte (fournisseur_id) ou dans les missions (supplier_id).</small>
                        </div>
                    @endif
                </div>
            </div>

            {{-- ── LAYOUT 2 COLONNES ── --}}
            <div class="row g-4">
                <div class="col-lg-8">
                    <div class="card">
                        <div class="card-header bg-primary text-white">
                            <h3 class="card-title">
                                <i class="fas fa-building me-2"></i>
                                {{ $fournisseur->raison_sociale ?? $fournisseur->nom ?? 'Fournisseur sans nom' }}
                            </h3>
                            <div class="card-tools">
                                <span class="badge bg-{{ $fournisseur->est_actif ?? $fournisseur->statut == 1 ? 'success' : 'secondary' }}">
                                    {{ $fournisseur->est_actif ?? $fournisseur->statut == 1 ? 'Actif' : 'Inactif' }}
                                </span>
                            </div>
                        </div>
                        <div class="card-body">
                            <!-- Informations générales -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h6 class="text-muted mb-3">
                                        <i class="fas fa-info-circle me-2"></i>
                                        Informations générales
                                    </h6>
                                </div>
                                <div class="col-md-6">
                                    <div class="info-box mb-3">
                                        <span class="info-box-icon bg-info">
                                            <i class="fas fa-tag"></i>
                                        </span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Catégorie</span>
                                            <span class="info-box-number">{{ $fournisseur->categorie->nom ?? $fournisseur->type ?? 'Non définie' }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info-box mb-3">
                                        <span class="info-box-icon bg-warning">
                                            <i class="fas fa-briefcase"></i>
                                        </span>
                                        <div class="info-box-content">
                                            <span class="info-box-text">Forme juridique</span>
                                            <span class="info-box-number">{{ $fournisseur->forme_juridique ?? 'Non spécifiée' }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Coordonnées -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h6 class="text-muted mb-3">
                                        <i class="fas fa-phone me-2"></i>
                                        Coordonnées
                                    </h6>
                                </div>
                                <div class="col-md-6">
                                    <div class="small-box bg-light mb-3">
                                        <div class="inner">
                                            <h6 class="text-dark">Email</h6>
                                            <p class="mb-0">
                                                <i class="fas fa-envelope text-primary me-2"></i>
                                                {{ $fournisseur->email ?? 'Non renseigné' }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="small-box bg-light mb-3">
                                        <div class="inner">
                                            <h6 class="text-dark">Téléphone</h6>
                                            <p class="mb-0">
                                                <i class="fas fa-phone text-success me-2"></i>
                                                {{ $fournisseur->telephone ?? 'Non renseigné' }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                @if($fournisseur->site_web)
                                <div class="col-12">
                                    <div class="small-box bg-light mb-3">
                                        <div class="inner">
                                            <h6 class="text-dark">Site web</h6>
                                            <p class="mb-0">
                                                <i class="fas fa-globe text-info me-2"></i>
                                                <a href="{{ $fournisseur->site_web }}" target="_blank">{{ $fournisseur->site_web }}</a>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                @endif
                            </div>

                            <!-- Adresse -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h6 class="text-muted mb-3">
                                        <i class="fas fa-map-marker-alt me-2"></i>
                                        Adresse
                                    </h6>
                                </div>
                                <div class="col-12">
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <p class="mb-2">
                                                <i class="fas fa-home text-primary me-2"></i>
                                                <strong>Adresse :</strong> {{ $fournisseur->adresse ?? 'Non renseignée' }}
                                            </p>
                                            @if($fournisseur->code_postal)
                                            <p class="mb-2">
                                                <i class="fas fa-mail-bulk text-secondary me-2"></i>
                                                <strong>Code postal :</strong> {{ $fournisseur->code_postal }}
                                            </p>
                                            @endif
                                            <p class="mb-2">
                                                <i class="fas fa-city text-info me-2"></i>
                                                <strong>Ville :</strong> {{ $fournisseur->ville ?? 'Non renseignée' }}
                                            </p>
                                            <p class="mb-0">
                                                <i class="fas fa-flag text-success me-2"></i>
                                                <strong>Pays :</strong> {{ $fournisseur->pays ?? 'Non renseigné' }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Informations légales -->
                            @if($fournisseur->siret || $fournisseur->tva_intracom)
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h6 class="text-muted mb-3">
                                        <i class="fas fa-balance-scale me-2"></i>
                                        Informations légales
                                    </h6>
                                </div>
                                @if($fournisseur->siret)
                                <div class="col-md-6">
                                    <div class="small-box bg-light mb-3">
                                        <div class="inner">
                                            <h6 class="text-dark">SIRET</h6>
                                            <p class="mb-0">
                                                <i class="fas fa-id-card text-warning me-2"></i>
                                                {{ $fournisseur->siret }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                @endif
                                @if($fournisseur->tva_intracom)
                                <div class="col-md-6">
                                    <div class="small-box bg-light mb-3">
                                        <div class="inner">
                                            <h6 class="text-dark">TVA Intracom</h6>
                                            <p class="mb-0">
                                                <i class="fas fa-receipt text-info me-2"></i>
                                                {{ $fournisseur->tva_intracom }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                @endif
                            </div>
                            @endif

                            <!-- Champs manuels -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h6 class="text-muted mb-3">
                                        <i class="fas fa-pen me-2"></i>
                                        Suivi manuel
                                    </h6>
                                </div>
                                <div class="col-md-6">
                                    <div class="small-box bg-light mb-3">
                                        <div class="inner">
                                            <h6 class="text-dark">Année contractuelle</h6>
                                            <p class="mb-0">
                                                <i class="fas fa-calendar-alt text-primary me-2"></i>
                                                {{ $fournisseur->annee_contractuelle ?? 'Non renseignée' }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="small-box bg-light mb-3">
                                        <div class="inner">
                                            <h6 class="text-dark">Note</h6>
                                            <p class="mb-0">
                                                <i class="fas fa-star text-warning me-2"></i>
                                                {{ $fournisseur->note_appreciation ?? 'Non renseignée' }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Matériels fournis -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h6 class="text-muted mb-3">
                                        <i class="fas fa-truck me-2"></i>
                                        Différents matériels fournis
                                    </h6>
                                    <div class="table-responsive">
                                        <table class="table table-sm table-striped align-middle">
                                            <thead>
                                                <tr>
                                                    <th>Type</th>
                                                    <th>Marque / Modèle</th>
                                                    <th>Immatriculation</th>
                                                    <th>Disponibilité</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($materielsFournis as $materiel)
                                                    <tr>
                                                        <td>{{ $materiel->type_materiel ?? '-' }}</td>
                                                        <td>{{ trim(($materiel->marque ?? '') . ' ' . ($materiel->modele ?? '')) ?: '-' }}</td>
                                                        <td>{{ $materiel->immatriculation ?? '-' }}</td>
                                                        <td>
                                                            <span class="badge bg-{{ ($materiel->disponible ?? false) ? 'success' : 'secondary' }}">
                                                                {{ ($materiel->disponible ?? false) ? 'Disponible' : 'Indisponible' }}
                                                            </span>
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="4" class="text-muted text-center">Aucun matériel enregistré pour ce fournisseur.</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <!-- Chauffeurs affectés -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h6 class="text-muted mb-3">
                                        <i class="fas fa-user-tie me-2"></i>
                                        Chauffeurs affectés
                                    </h6>
                                    <div class="table-responsive">
                                        <table class="table table-sm table-striped align-middle">
                                            <thead>
                                                <tr>
                                                    <th>Chauffeur</th>
                                                    <th class="text-end">Nombre de missions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse($chauffeursAffectes as $affectation)
                                                    <tr>
                                                        <td>{{ optional($affectation->driver)->name ?? 'Utilisateur supprimé' }}</td>
                                                        <td class="text-end">{{ $affectation->total_missions }}</td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="2" class="text-muted text-center">Aucun chauffeur affecté pour ce fournisseur.</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <!-- Mouvements de paiement -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h6 class="text-muted mb-3">
                                        <i class="fas fa-exchange-alt me-2"></i>
                                        Différents mouvements (paiement reçu / paiement en attente)
                                    </h6>
                                </div>
                                <div class="col-md-6">
                                    <div class="small-box bg-light mb-3">
                                        <div class="inner">
                                            <h6 class="text-dark">Paiements reçus</h6>
                                            <p class="mb-1"><strong>{{ number_format($mouvementsPaiements['recu_montant'] ?? 0, 0, ',', ' ') }} FCFA</strong></p>
                                            <small class="text-muted">{{ $mouvementsPaiements['recu_nombre'] ?? 0 }} mouvement(s)</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="small-box bg-light mb-3">
                                        <div class="inner">
                                            <h6 class="text-dark">Paiements en attente</h6>
                                            <p class="mb-1"><strong>{{ number_format($mouvementsPaiements['attente_montant'] ?? 0, 0, ',', ' ') }} FCFA</strong></p>
                                            <small class="text-muted">{{ $mouvementsPaiements['attente_nombre'] ?? 0 }} mouvement(s)</small>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="table-responsive">
                                        <table class="table table-sm table-striped align-middle">
                                            <thead>
                                                <tr>
                                                    <th>Date</th>
                                                    <th>Référence</th>
                                                    <th class="text-end">Montant</th>
                                                    <th>Statut</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @forelse(($mouvementsPaiements['dernieres_lignes'] ?? collect()) as $mouvement)
                                                    <tr>
                                                        <td>{{ optional($mouvement->date_paiement)->format('d/m/Y') ?? '-' }}</td>
                                                        <td>{{ $mouvement->reference ?? $mouvement->reference_paiement ?? '-' }}</td>
                                                        <td class="text-end">{{ number_format($mouvement->montant ?? 0, 0, ',', ' ') }} FCFA</td>
                                                        <td>
                                                            @if($mouvement->est_encaisse)
                                                                <span class="badge bg-success">Reçu</span>
                                                            @else
                                                                <span class="badge bg-warning text-dark">En attente</span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="4" class="text-muted text-center">Aucun mouvement de paiement trouvé.</td>
                                                    </tr>
                                                @endforelse
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>

                            <!-- Notes -->
                            @if($fournisseur->notes)
                            <div class="row">
                                <div class="col-12">
                                    <h6 class="text-muted mb-3">
                                        <i class="fas fa-sticky-note me-2"></i>
                                        Notes
                                    </h6>
                                    <div class="card bg-light">
                                        <div class="card-body">
                                            <p class="mb-0">{{ $fournisseur->notes }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Sidebar actions -->
                <div class="col-lg-4">
                    <!-- Actions -->
                    <div class="card">
                        <div class="card-header">
                            <h6 class="m-0">
                                <i class="fas fa-cogs me-2"></i>
                                Actions
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="d-grid gap-2">
                                <a href="{{ route('fournisseurs.edit', $fournisseur->id ?? $fournisseur['id'] ?? 1) }}" class="btn btn-warning">
                                    <i class="fas fa-edit me-2"></i>Modifier
                                </a>
                                <a href="{{ route('fournisseurs.index') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-list me-2"></i>Liste des fournisseurs
                                </a>
                                <button type="button" class="btn btn-outline-info" onclick="window.print()">
                                    <i class="fas fa-print me-2"></i>Imprimer
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Statistiques -->
                    <div class="card mt-3">
                        <div class="card-header">
                            <h6 class="m-0">
                                <i class="fas fa-chart-bar me-2"></i>
                                Statistiques
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row text-center g-3">
                                <div class="col-4">
                                    <h4 class="text-primary">
                                        @if(Schema::hasTable('commandes_fournisseurs'))
                                            {{ DB::table('commandes_fournisseurs')->where('fournisseur_id', $fournisseur->id ?? $fournisseur['id'] ?? 1)->count() }}
                                        @else
                                            0
                                        @endif
                                    </h4>
                                    <small class="text-muted">Commandes</small>
                                </div>
                                <div class="col-4">
                                    <h4 class="text-success">
                                        @if(Schema::hasTable('factures_fournisseurs'))
                                            {{ DB::table('factures_fournisseurs')->where('fournisseur_id', $fournisseur->id ?? $fournisseur['id'] ?? 1)->count() }}
                                        @else
                                            0
                                        @endif
                                    </h4>
                                    <small class="text-muted">Factures</small>
                                </div>
                                <div class="col-4">
                                    <h4 class="text-info">{{ $materielsFournis->count() }}</h4>
                                    <small class="text-muted">Engins</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Engins proposés -->
                    <div class="card mt-3">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h6 class="m-0">
                                <i class="fas fa-truck me-2"></i>
                                Engins proposés
                            </h6>
                            <span class="badge bg-info">{{ $materielsFournis->count() }}</span>
                        </div>
                        <div class="card-body p-0">
                            @if($materielsFournis->isNotEmpty())
                                <div class="list-group list-group-flush">
                                    @foreach($materielsFournis->take(8) as $materiel)
                                        <div class="list-group-item">
                                            <div class="d-flex justify-content-between align-items-start">
                                                <div>
                                                    <div class="fw-semibold">
                                                        {{ trim(($materiel->marque ?? '') . ' ' . ($materiel->modele ?? '')) ?: ($materiel->immatriculation ?? 'Engin') }}
                                                    </div>
                                                    <small class="text-muted">
                                                        {{ $materiel->immatriculation ?? 'Immatriculation non renseignée' }}
                                                    </small>
                                                </div>
                                                <span class="badge bg-{{ ($materiel->disponible ?? false) ? 'success' : 'secondary' }}">
                                                    {{ ($materiel->disponible ?? false) ? 'Disponible' : 'Indisponible' }}
                                                </span>
                                            </div>
                                            <div class="mt-1">
                                                <small class="text-muted">Type: {{ $materiel->type_materiel ?? '-' }}</small>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                @if($materielsFournis->count() > 8)
                                    <div class="p-3 border-top bg-light">
                                        <small class="text-muted">
                                            {{ $materielsFournis->count() - 8 }} autre(s) engin(s) visible(s) dans la section détaillée de la page.
                                        </small>
                                    </div>
                                @endif
                            @else
                                <div class="p-3 text-muted">Aucun engin enregistré pour ce fournisseur.</div>
                            @endif
                        </div>
                    </div>

                    <!-- Logo -->
                    @if($fournisseur->logo_path)
                    <div class="card mt-3">
                        <div class="card-header">
                            <h6 class="m-0">
                                <i class="fas fa-image me-2"></i>
                                Logo
                            </h6>
                        </div>
                        <div class="card-body text-center">
                            <img src="{{ asset('storage/' . $fournisseur->logo_path) }}"
                                 alt="Logo {{ $fournisseur->raison_sociale }}"
                                 class="img-fluid"
                                 style="max-height: 100px;">
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </section>
</div>
@endsection

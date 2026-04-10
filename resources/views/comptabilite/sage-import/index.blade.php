@extends('layouts.app')

@section('title', 'Import SAGE i7')

@section('content')
<div class="container-fluid">

    {{-- En-tête --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1"><i class="fas fa-file-import me-2 text-primary"></i>Import SAGE i7</h1>
            <p class="text-muted mb-0">Importez vos données depuis SAGE 100 i7 sans écraser l'existant.</p>
        </div>
        <a href="{{ route('comptabilite.dashboard') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Retour
        </a>
    </div>

    {{-- Guide rapide --}}
    <div class="alert alert-info d-flex gap-3 align-items-start mb-4">
        <i class="fas fa-lightbulb fa-lg mt-1 text-info"></i>
        <div>
            <strong>Comment exporter depuis SAGE 100 i7?</strong><br>
            <span class="small">
                Menu <strong>Fichier → Exporter→ Exporter des données</strong>
                (ou <strong>Outils → Import/Export → Export paramétrable</strong>).
                Choisir format <strong>CSV, séparateur point-virgule (;), encodage ANSI</strong>.
                Cocher <em>Inclure les noms de colonnes</em>.
                Enregistrer le fichier puis l'importer ici.
            </span>
        </div>
    </div>

    {{-- Onglets --}}
    <ul class="nav nav-tabs mb-4" id="importTabs" role="tablist">
        <li class="nav-item">
            <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-clients" type="button">
                <i class="fas fa-users me-1"></i>Clients
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-fournisseurs" type="button">
                <i class="fas fa-truck me-1"></i>Fournisseurs
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-factures" type="button">
                <i class="fas fa-file-invoice me-1"></i>Factures
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-ecritures" type="button">
                <i class="fas fa-book-open me-1"></i>Écritures
            </button>
        </li>
    </ul>

    <div class="tab-content">

        {{-- ── CLIENTS ── --}}
        <div class="tab-pane fade show active" id="tab-clients">
            @if(session('success_clients'))
                <div class="alert alert-success"><i class="fas fa-check-circle me-2"></i>{{ session('success_clients') }}</div>
            @endif
            @if(session('error_clients'))
                <div class="alert alert-danger">{{ session('error_clients') }}</div>
            @endif
            @if(session('errors_clients') && count(session('errors_clients')))
                <div class="alert alert-warning">
                    <strong>Erreurs sur certaines lignes:</strong>
                    <ul class="mb-0 mt-1">
                        @foreach(array_slice(session('errors_clients'), 0, 10) as $err)
                            <li class="small">{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="row g-4">
                <div class="col-lg-7">
                    <div class="card shadow-sm">
                        <div class="card-header bg-white"><h5 class="mb-0">Importer les clients</h5></div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('comptabilite.sage-import.clients') }}" enctype="multipart/form-data">
                                @csrf
                                @include('comptabilite.sage-import.partials.form-fields')
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-upload me-2"></i>Importer les clients
                                </button>
                                <a href="{{ route('comptabilite.sage-import.template', 'clients') }}" class="btn btn-outline-secondary ms-2">
                                    <i class="fas fa-download me-1"></i>Télécharger modèle CSV
                                </a>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-lg-5">
                    @include('comptabilite.sage-import.partials.mapping-info', [
                        'title' => 'Colonnes SAGE reconnues',
                        'rows' => [
                            ['CT_Num', 'code_client'],
                            ['CT_Intitule', 'nom_complet'],
                            ['CT_Adresse', 'adresse'],
                            ['CT_Ville', 'ville'],
                            ['CT_Pays', 'pays'],
                            ['CT_Telephone', 'telephone'],
                            ['CT_Email', 'email'],
                            ['CT_Identifiant', 'raison_sociale'],
                            ['CT_NumPayeur', 'numero_contribuable'],
                        ]
                    ])
                </div>
            </div>
        </div>

        {{-- ── FOURNISSEURS ── --}}
        <div class="tab-pane fade" id="tab-fournisseurs">
            @if(session('success_fournisseurs'))
                <div class="alert alert-success"><i class="fas fa-check-circle me-2"></i>{{ session('success_fournisseurs') }}</div>
            @endif
            @if(session('error_fournisseurs'))
                <div class="alert alert-danger">{{ session('error_fournisseurs') }}</div>
            @endif
            @if(session('errors_fournisseurs') && count(session('errors_fournisseurs')))
                <div class="alert alert-warning">
                    <strong>Erreurs sur certaines lignes:</strong>
                    <ul class="mb-0 mt-1">
                        @foreach(array_slice(session('errors_fournisseurs'), 0, 10) as $err)
                            <li class="small">{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="row g-4">
                <div class="col-lg-7">
                    <div class="card shadow-sm">
                        <div class="card-header bg-white"><h5 class="mb-0">Importer les fournisseurs</h5></div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('comptabilite.sage-import.fournisseurs') }}" enctype="multipart/form-data">
                                @csrf
                                @include('comptabilite.sage-import.partials.form-fields')
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-upload me-2"></i>Importer les fournisseurs
                                </button>
                                <a href="{{ route('comptabilite.sage-import.template', 'fournisseurs') }}" class="btn btn-outline-secondary ms-2">
                                    <i class="fas fa-download me-1"></i>Télécharger modèle CSV
                                </a>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-lg-5">
                    @include('comptabilite.sage-import.partials.mapping-info', [
                        'title' => 'Colonnes SAGE reconnues',
                        'rows' => [
                            ['CT_Num', 'reference'],
                            ['CT_Intitule', 'raison_sociale'],
                            ['CT_Adresse', 'adresse'],
                            ['CT_CodePostal', 'code_postal'],
                            ['CT_Ville', 'ville'],
                            ['CT_Pays', 'pays'],
                            ['CT_Telephone', 'telephone'],
                            ['CT_Email', 'email'],
                            ['CT_Siret', 'siret'],
                        ]
                    ])
                </div>
            </div>
        </div>

        {{-- ── FACTURES ── --}}
        <div class="tab-pane fade" id="tab-factures">
            @if(session('success_factures'))
                <div class="alert alert-success"><i class="fas fa-check-circle me-2"></i>{{ session('success_factures') }}</div>
            @endif
            @if(session('error_factures'))
                <div class="alert alert-danger">{{ session('error_factures') }}</div>
            @endif
            @if(session('errors_factures') && count(session('errors_factures')))
                <div class="alert alert-warning">
                    <strong>Erreurs sur certaines lignes:</strong>
                    <ul class="mb-0 mt-1">
                        @foreach(array_slice(session('errors_factures'), 0, 10) as $err)
                            <li class="small">{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="row g-4">
                <div class="col-lg-7">
                    <div class="card shadow-sm">
                        <div class="card-header bg-white"><h5 class="mb-0">Importer les factures</h5></div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('comptabilite.sage-import.factures') }}" enctype="multipart/form-data">
                                @csrf
                                @include('comptabilite.sage-import.partials.form-fields')
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-upload me-2"></i>Importer les factures
                                </button>
                                <a href="{{ route('comptabilite.sage-import.template', 'factures') }}" class="btn btn-outline-secondary ms-2">
                                    <i class="fas fa-download me-1"></i>Télécharger modèle CSV
                                </a>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-lg-5">
                    @include('comptabilite.sage-import.partials.mapping-info', [
                        'title' => 'Colonnes SAGE reconnues',
                        'rows' => [
                            ['DO_Piece', 'numero_facture'],
                            ['DO_Date', 'date_facturation'],
                            ['CT_Num', 'client_id'],
                            ['CT_Intitule', 'client_nom'],
                            ['DL_Design', 'designation'],
                            ['DO_TotalHT', 'montant_ht'],
                            ['DO_TotalTVA', 'montant_tva'],
                            ['DO_TotalTTC', 'montant_ttc'],
                            ['DO_Statut', 'statut'],
                            ['DO_DateEcheance', 'date_echeance'],
                        ]
                    ])
                </div>
            </div>
        </div>

        {{-- ── ÉCRITURES ── --}}
        <div class="tab-pane fade" id="tab-ecritures">
            @if(session('success_ecritures'))
                <div class="alert alert-success"><i class="fas fa-check-circle me-2"></i>{{ session('success_ecritures') }}</div>
            @endif
            @if(session('error_ecritures'))
                <div class="alert alert-danger">{{ session('error_ecritures') }}</div>
            @endif
            @if(session('errors_ecritures') && count(session('errors_ecritures')))
                <div class="alert alert-warning">
                    <strong>Erreurs sur certaines lignes:</strong>
                    <ul class="mb-0 mt-1">
                        @foreach(array_slice(session('errors_ecritures'), 0, 10) as $err)
                            <li class="small">{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="row g-4">
                <div class="col-lg-7">
                    <div class="card shadow-sm">
                        <div class="card-header bg-white"><h5 class="mb-0">Importer les écritures comptables</h5></div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('comptabilite.sage-import.ecritures') }}" enctype="multipart/form-data">
                                @csrf
                                @include('comptabilite.sage-import.partials.form-fields')
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-upload me-2"></i>Importer les écritures
                                </button>
                                <a href="{{ route('comptabilite.sage-import.template', 'ecritures') }}" class="btn btn-outline-secondary ms-2">
                                    <i class="fas fa-download me-1"></i>Télécharger modèle CSV
                                </a>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-lg-5">
                    @include('comptabilite.sage-import.partials.mapping-info', [
                        'title' => 'Colonnes SAGE reconnues',
                        'rows' => [
                            ['JO_Num', 'journal_id'],
                            ['EC_Date', 'date'],
                            ['EC_Reference', 'reference'],
                            ['EC_Piece', 'piece_comptable'],
                            ['EC_Libelle', 'libelle'],
                            ['EC_CompteDebit', 'compte_debit'],
                            ['EC_CompteCredit', 'compte_credit'],
                            ['EC_Montant', 'montant'],
                        ]
                    ])
                </div>
            </div>
        </div>

    </div>{{-- /tab-content --}}
</div>
@endsection

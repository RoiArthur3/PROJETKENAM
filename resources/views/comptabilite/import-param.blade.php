@extends('layouts.app')

@section('title', 'Import Excel Compta')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-0">
                    <i class="fas fa-file-upload me-2 text-primary"></i>
                    Import Excel Compta
                </h1>
                <p class="text-muted mb-0">Uploader les fichiers Excel (param compta, creances, dettes, caisse) en ligne.</p>
            </div>
            <a href="{{ route('comptabilite.dashboard') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Retour Comptabilite
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h6 class="mb-0">Fichier a importer</h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('comptabilite.import-param-compta.store') }}" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label">Type de fichier</label>
                            <select name="import_type" class="form-select" required>
                                <option value="param_compta" {{ session('import_param_type') === 'param_compta' ? 'selected' : '' }}>PARAM COMPTA</option>
                                <option value="creances_clients" {{ session('import_param_type') === 'creances_clients' ? 'selected' : '' }}>CREANCES CLIENTS</option>
                                <option value="dettes_fournisseurs" {{ session('import_param_type') === 'dettes_fournisseurs' ? 'selected' : '' }}>DETTES FOURNISSEURS</option>
                                <option value="caisse_logistique" {{ session('import_param_type') === 'caisse_logistique' ? 'selected' : '' }}>CAISSE LOGISTIQUE</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Classeur Excel</label>
                            <input type="file" name="param_compta_excel" class="form-control" accept=".xlsx,.xls" required>
                            <div class="form-text">Formats autorises: xlsx, xls</div>
                        </div>

                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" value="1" id="dry_run" name="dry_run" checked>
                            <label class="form-check-label" for="dry_run">
                                Mode simulation (dry-run): analyser sans enregistrer
                            </label>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-play me-2"></i>Lancer l'import
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h6 class="mb-0">Perimetre</h6>
                </div>
                <div class="card-body">
                    <ul class="mb-0">
                        <li>PARAM COMPTA: comptes, journaux, tiers, analytique</li>
                        <li>CREANCES CLIENTS: clients + factures (reglements detectes)</li>
                        <li>DETTES FOURNISSEURS: fournisseurs (dettes/paiements detectes)</li>
                        <li>CAISSE LOGISTIQUE: chargement et analyse des mouvements</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    @if(session('import_param_stats'))
        @php($stats = session('import_param_stats'))
        <div class="card shadow-sm mb-4">
            <div class="card-header">
                <h6 class="mb-0">Resultat import</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm table-bordered">
                        <thead>
                            <tr>
                                <th>Mesure</th>
                                <th>Valeur</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr><td>Mode simulation</td><td>{{ !empty($stats['dry_run']) ? 'Oui' : 'Non' }}</td></tr>
                            @foreach($stats as $key => $value)
                                @if(!in_array($key, ['dry_run', 'warnings', 'source']))
                                    <tr>
                                        <td>{{ ucwords(str_replace('_', ' ', $key)) }}</td>
                                        <td>{{ is_array($value) ? json_encode($value) : $value }}</td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

    @if(session('import_param_warnings') && count(session('import_param_warnings')) > 0)
        <div class="card shadow-sm">
            <div class="card-header">
                <h6 class="mb-0">Alertes</h6>
            </div>
            <div class="card-body">
                <ul class="mb-0">
                    @foreach(session('import_param_warnings') as $warning)
                        <li>{{ $warning }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif
</div>
@endsection

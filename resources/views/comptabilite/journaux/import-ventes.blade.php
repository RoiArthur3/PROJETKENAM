@extends('layouts.app')

@section('title', 'Import Journal des Ventes')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1"><i class="fas fa-file-import me-2 text-primary"></i>Import Journaux des Ventes</h1>
            <p class="text-muted mb-0">Charge plusieurs fichiers CSV/XLS/XLSX en une seule fois.</p>
        </div>
        <a href="{{ route('comptabilite.ecritures.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i>Retour Grand Journal
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
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

    <div class="card shadow-sm">
        <div class="card-body">
            <form method="POST" action="{{ route('comptabilite.ecritures.import-ventes.store') }}" enctype="multipart/form-data" class="row g-3">
                @csrf
                <div class="col-12">
                    <label class="form-label">Fichiers journaux des ventes</label>
                    <input type="file" name="files[]" class="form-control" multiple required accept=".csv,.xls,.xlsx">
                    <div class="form-text">Tu peux sélectionner plusieurs fichiers en même temps.</div>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Nom de feuille (optionnel)</label>
                    <input type="text" name="sheet" class="form-control" value="{{ old('sheet') }}" placeholder="Ex: Feuil1">
                </div>

                <div class="col-md-6 d-flex align-items-end">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="1" id="dry_run" name="dry_run" {{ old('dry_run') ? 'checked' : '' }}>
                        <label class="form-check-label" for="dry_run">
                            Dry run (analyse sans écriture en base)
                        </label>
                    </div>
                </div>

                <div class="col-12">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-upload me-2"></i>Lancer l'import
                    </button>
                </div>
            </form>
        </div>
    </div>

    @if(session('import_results'))
        <div class="card shadow-sm mt-4">
            <div class="card-header bg-white">
                <h5 class="mb-0">Résultats de l'import</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Fichier</th>
                                <th>Statut</th>
                                <th>Message</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach(session('import_results') as $result)
                                <tr>
                                    <td>{{ $result['file'] }}</td>
                                    <td>
                                        <span class="badge {{ $result['success'] ? 'bg-success' : 'bg-danger' }}">
                                            {{ $result['success'] ? 'OK' : 'Erreur' }}
                                        </span>
                                    </td>
                                    <td><small>{{ $result['message'] }}</small></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif
</div>
@endsection

@extends('layouts.app')

@section('title', 'Import des fournisseurs')

@section('content')
<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Importer des fournisseurs</h1>

    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Import Excel / CSV</h6>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('warning'))
                <div class="alert alert-warning">
                    {{ session('warning') }}
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif

            @if(session('import_errors'))
                <div class="alert alert-danger">
                    <h5>Erreurs détectées :</h5>
                    <ul>
                        @foreach(session('import_errors') as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('fournisseurs.import') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="mb-3">
                    <label for="mode_import" class="form-label">Mode d'import</label>
                    <select name="mode_import" id="mode_import" class="form-select" required>
                        <option value="creer">Créer uniquement (ignorer les existants)</option>
                        <option value="maj">Mettre à jour uniquement (ignorer les nouveaux)</option>
                        <option value="creer_maj">Créer et mettre à jour</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="fichier" class="form-label">Fichier Excel (.xlsx, .xls) ou CSV</label>
                    <input type="file" class="form-control" id="fichier" name="fichier" accept=".xlsx,.xls,.csv" required>
                </div>

                <div class="mb-3">
                    <p class="text-muted">
                        Téléchargez le modèle pour vous assurer que votre fichier respecte le format attendu.
                        <a href="{{ route('fournisseurs.export') }}" class="btn btn-link">Télécharger le modèle (Export actuel)</a>
                    </p>
                </div>

                <button type="submit" class="btn btn-primary">Importer</button>
                <a href="{{ route('fournisseurs.index') }}" class="btn btn-secondary">Annuler</a>
            </form>
        </div>
    </div>
</div>
@endsection

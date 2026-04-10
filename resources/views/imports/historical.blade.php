@extends('layouts.app')

@section('title', 'Import Historique Excel')

@section('content')
<div class="container-fluid py-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h1 class="h4 mb-0">Import Historique Excel</h1>
        <div class="d-flex gap-2">
            <a href="{{ route('imports.audit') }}" class="btn btn-outline-info btn-sm">
                <i class="fas fa-chart-bar me-1"></i>Rapport d'audit
            </a>
            <a href="{{ url()->previous() }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left me-1"></i>Retour
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('warning'))
        <div class="alert alert-warning">{{ session('warning') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @if(session('import_errors'))
        <div class="alert alert-danger">
            <strong>Erreurs detectees:</strong>
            <ul class="mb-0 mt-2">
                @foreach(session('import_errors') as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if(session('header_preview'))
        @php($preview = session('header_preview'))
        <div class="card border-{{ $preview['is_valid'] ? 'success' : 'warning' }} mb-3">
            <div class="card-header bg-white">
                <h2 class="h6 mb-0">Resultat du pre-controle des en-tetes</h2>
            </div>
            <div class="card-body">
                <p class="mb-2">
                    <strong>Entite:</strong> {{ $preview['entity'] }} |
                    <strong>Source:</strong> {{ $preview['source'] }} |
                    <strong>Conforme:</strong> {{ $preview['is_valid'] ? 'Oui' : 'Non' }}
                </p>
                <div class="row g-3">
                    <div class="col-md-6">
                        <h3 class="h6">Attendu</h3>
                        <ol class="mb-0">
                            @foreach($preview['expected'] as $column)
                                <li><code>{{ $column }}</code></li>
                            @endforeach
                        </ol>
                    </div>
                    <div class="col-md-6">
                        <h3 class="h6">Trouve</h3>
                        <ol class="mb-0">
                            @foreach($preview['found'] as $column)
                                <li><code>{{ $column }}</code></li>
                            @endforeach
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-header bg-white">
            <h2 class="h6 mb-0">Charger les donnees passees depuis Excel/CSV</h2>
        </div>
        <div class="card-body">
            <form action="{{ route('imports.historique.store') }}" method="POST" enctype="multipart/form-data" class="row g-3">
                @csrf

                <div class="col-md-4">
                    <label class="form-label">Entite cible</label>
                    <select name="entity" id="entity" class="form-select" required>
                        @foreach($entities as $key => $label)
                            <option value="{{ $key }}" @selected(old('entity', $defaultEntity) === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Mode d'import</label>
                    <select name="mode_import" class="form-select" required>
                        <option value="creer">Creer uniquement</option>
                        <option value="maj">Mettre a jour uniquement</option>
                        <option value="creer_maj" selected>Creer + Mettre a jour</option>
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Fichier</label>
                    <input type="file" name="fichier" class="form-control" accept=".xlsx,.xls,.csv" required>
                </div>

                <div class="col-12 d-flex align-items-center gap-2">
                    <button type="submit" name="action" value="import" class="btn btn-primary">
                        <i class="fas fa-file-import me-1"></i>Importer
                    </button>

                    <button type="submit" name="action" value="preview" class="btn btn-outline-primary">
                        <i class="fas fa-check-circle me-1"></i>Pre-controler
                    </button>

                    <a id="template-link" href="{{ route('imports.historique.template', ['entity' => old('entity', $defaultEntity)]) }}" class="btn btn-outline-success">
                        <i class="fas fa-download me-1"></i>Telecharger le modele
                    </a>
                </div>
            </form>

            <div class="alert alert-info mt-3 mb-0">
                <strong>Filtre intelligent des listes:</strong> apres import, les pages de liste compatibles proposent une recherche instantanee
                par mots-cles, phrase exacte avec "guillemets", et exclusion avec "-mot".
            </div>

            <div class="alert alert-warning mt-3 mb-0">
                <strong>Validation stricte active:</strong> l'import est bloque si les en-tetes du fichier ne respectent pas exactement
                l'ordre des colonnes attendu pour l'entite selectionnee.
            </div>

            <hr class="my-4">

            <h3 class="h6">Importer directement depuis les fichiers du dossier doc</h3>
            <form action="{{ route('imports.historique.store') }}" method="POST" class="row g-3 mt-1">
                @csrf

                <div class="col-md-4">
                    <label class="form-label">Entite cible</label>
                    <select name="entity" class="form-select" required>
                        @foreach($entities as $key => $label)
                            <option value="{{ $key }}" @selected(old('entity', $defaultEntity) === $key)>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Mode d'import</label>
                    <select name="mode_import" class="form-select" required>
                        <option value="creer">Creer uniquement</option>
                        <option value="maj">Mettre a jour uniquement</option>
                        <option value="creer_maj" selected>Creer + Mettre a jour</option>
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label">Fichier du dossier doc</label>
                    <select name="doc_file" class="form-select" required>
                        @forelse($availableFiles as $key => $source)
                            <option value="{{ $key }}" @selected(old('doc_file') === $key)>{{ $source['label'] }}</option>
                        @empty
                            <option value="" disabled selected>Aucun fichier Excel/CSV detecte</option>
                        @endforelse
                    </select>
                </div>

                <div class="col-12 d-flex align-items-center gap-2">
                    <button type="submit" name="action" value="import" class="btn btn-dark" @disabled(empty($availableFiles))>
                        <i class="fas fa-folder-open me-1"></i>Importer depuis doc
                    </button>

                    <button type="submit" name="action" value="preview" class="btn btn-outline-dark" @disabled(empty($availableFiles))>
                        <i class="fas fa-check-circle me-1"></i>Pre-controler depuis doc
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm mt-3">
        <div class="card-header bg-white">
            <h2 class="h6 mb-0">Champs dynamiques pour peupler la BDD</h2>
        </div>
        <div class="card-body">
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label for="blueprint-entity" class="form-label">Jeu de donnees</label>
                    <select id="blueprint-entity" class="form-select">
                        @foreach($importBlueprints as $key => $blueprint)
                            <option value="{{ $key }}">{{ $blueprint['label'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Cible</label>
                    <div id="blueprint-target" class="form-control bg-light"></div>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-sm table-bordered align-middle mb-2">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 80px;">Ordre</th>
                            <th>Colonne fichier</th>
                            <th style="width: 110px;">Obligatoire</th>
                            <th>Description</th>
                        </tr>
                    </thead>
                    <tbody id="blueprint-columns"></tbody>
                </table>
            </div>

            <div id="blueprint-sheets-wrapper" class="mt-3 d-none">
                <h3 class="h6">Feuilles attendues (plan comptable)</h3>
                <div class="table-responsive">
                    <table class="table table-sm table-striped mb-0">
                        <thead>
                            <tr>
                                <th>Feuille</th>
                                <th>Colonnes a respecter</th>
                            </tr>
                        </thead>
                        <tbody id="blueprint-sheets"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const entitySelect = document.getElementById('entity');
    const templateLink = document.getElementById('template-link');
    if (!entitySelect || !templateLink) return;

    const routeBase = @json(url('/imports/historique/template'));
    const refreshLink = () => {
        templateLink.setAttribute('href', routeBase + '/' + encodeURIComponent(entitySelect.value));
    };

    entitySelect.addEventListener('change', refreshLink);
    refreshLink();

    const blueprints = @json($importBlueprints);
    const blueprintSelect = document.getElementById('blueprint-entity');
    const blueprintTarget = document.getElementById('blueprint-target');
    const blueprintColumns = document.getElementById('blueprint-columns');
    const sheetsWrapper = document.getElementById('blueprint-sheets-wrapper');
    const sheetsBody = document.getElementById('blueprint-sheets');

    const renderBlueprint = () => {
        if (!blueprintSelect || !blueprintColumns || !blueprintTarget || !sheetsWrapper || !sheetsBody) {
            return;
        }

        const selected = blueprints[blueprintSelect.value];
        if (!selected) {
            blueprintTarget.textContent = '';
            blueprintColumns.innerHTML = '';
            sheetsBody.innerHTML = '';
            sheetsWrapper.classList.add('d-none');
            return;
        }

        blueprintTarget.textContent = 'Table: ' + selected.target_table + ' | Page: ' + selected.target_page;

        blueprintColumns.innerHTML = '';
        selected.ordered_columns.forEach((column, index) => {
            const tr = document.createElement('tr');
            tr.innerHTML =
                '<td>' + (index + 1) + '</td>' +
                '<td><code>' + column.name + '</code></td>' +
                '<td>' + (column.required ? 'Oui' : 'Non') + '</td>' +
                '<td>' + (column.description || '') + '</td>';
            blueprintColumns.appendChild(tr);
        });

        const sheets = Array.isArray(selected.sheets) ? selected.sheets : [];
        sheetsBody.innerHTML = '';
        if (sheets.length === 0) {
            sheetsWrapper.classList.add('d-none');
            return;
        }

        sheetsWrapper.classList.remove('d-none');
        sheets.forEach((sheet) => {
            const tr = document.createElement('tr');
            tr.innerHTML =
                '<td><code>' + sheet.name + '</code></td>' +
                '<td>' + (Array.isArray(sheet.required_columns) ? sheet.required_columns.join(', ') : '') + '</td>';
            sheetsBody.appendChild(tr);
        });
    };

    if (blueprintSelect) {
        blueprintSelect.addEventListener('change', renderBlueprint);
        renderBlueprint();
    }
});
</script>
@endsection

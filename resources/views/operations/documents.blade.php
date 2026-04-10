@extends('layouts.app')

@section('title', 'Opérations - Documents')

@section('content')
<div class="container-fluid">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    
    @php
        $storage = \Illuminate\Support\Facades\Storage::disk('public');
        $operationDirs = $storage->directories('operations');
        $operationIds = collect($operationDirs)
            ->map(fn($d) => basename($d))
            ->filter(fn($b) => ctype_digit($b))
            ->values();

        $filterOp = request('op');
        $filterQ = trim((string)request('q', ''));
        $filterFrom = request('from');
        $filterTo = request('to');
        $onlyWithFiles = request('only') == '1';

        if ($filterOp && ctype_digit((string)$filterOp)) {
            $operationIds = $operationIds->filter(fn($id) => (int)$id === (int)$filterOp)->values();
        }
    @endphp

    <div class="card shadow mb-3">
        <div class="card-body">
            <form class="row g-2" method="GET">
                <div class="col-sm-2">
                    <label class="form-label small">Opération #</label>
                    <input type="number" class="form-control form-control-sm" name="op" value="{{ request('op') }}" placeholder="ID">
                </div>
                <div class="col-sm-3">
                    <label class="form-label small">Nom de fichier contient</label>
                    <input type="text" class="form-control form-control-sm" name="q" value="{{ request('q') }}" placeholder="ex: facture">
                </div>
                <div class="col-sm-2">
                    <label class="form-label small">Du</label>
                    <input type="date" class="form-control form-control-sm" name="from" value="{{ request('from') }}">
                </div>
                <div class="col-sm-2">
                    <label class="form-label small">Au</label>
                    <input type="date" class="form-control form-control-sm" name="to" value="{{ request('to') }}">
                </div>
                <div class="col-sm-3">
                    <label class="form-label small">&nbsp;</label>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="only" value="1" id="onlyWithFiles" {{ request('only')=='1' ? 'checked' : '' }}>
                        <label class="form-check-label small" for="onlyWithFiles">Afficher seulement les opérations ayant des fichiers</label>
                    </div>
                </div>
                <div class="col-sm-3 d-flex align-items-end gap-2">
                    <button class="btn btn-sm btn-primary" type="submit"><i class="fas fa-filter me-1"></i>Filtrer</button>
                    <a class="btn btn-sm btn-outline-secondary" href="{{ route('operations.documents') }}">Réinitialiser</a>
                </div>
            </form>
        </div>
    </div>

    @php
        $opsFiltered = collect();
        foreach ($operationIds as $oid) {
            $files = $storage->files("operations/{$oid}/fichiers");
            $files = array_values(array_filter($files, function($file) use ($storage, $filterQ, $filterFrom, $filterTo) {
                $name = basename($file);
                if ($filterQ && stripos($name, $filterQ) === false) return false;
                $ts = $storage->lastModified($file);
                if ($filterFrom && (date('Y-m-d', $ts) < $filterFrom)) return false;
                if ($filterTo && (date('Y-m-d', $ts) > $filterTo)) return false;
                return true;
            }));
            if (!$onlyWithFiles || count($files) > 0) {
                $opsFiltered->push(['id' => $oid, 'files' => $files]);
            }
        }
        $perPage = 9;
        $page = max(1, (int)request('page', 1));
        $total = $opsFiltered->count();
        $pages = max(1, (int)ceil($total / $perPage));
        if ($page > $pages) { $page = $pages; }
        $slice = $opsFiltered->slice(($page-1)*$perPage, $perPage);
        $queryBase = request()->except('page');
    @endphp

    @if($opsFiltered->isEmpty())
        <div class="card shadow">
            <div class="card-body text-center text-muted py-5">
                <i class="fas fa-inbox fa-3x mb-3"></i>
                <div>Aucun document trouvé. Les fichiers envoyés lors de la création d'une opération apparaîtront ici.</div>
            </div>
        </div>
    @else
        <div class="row g-3">
            @foreach($slice as $op)
                @php
                    $opId = $op['id'];
                    $files = $op['files'];
                    $count = count($files);
                @endphp
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="card shadow-sm h-100">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <div>
                                <span class="badge bg-primary me-2">#{{ str_pad($opId, 5, '0', STR_PAD_LEFT) }}</span>
                                <strong>Fichiers</strong>
                            </div>
                            <span class="badge bg-secondary">{{ $count }}</span>
                        </div>
                        <div class="card-body">
                            @if($count === 0)
                                <div class="text-muted small">Aucun fichier pour cette opération.</div>
                            @else
                                <ul class="list-unstyled mb-0">
                                    @foreach($files as $file)
                                        @php
                                            $name = basename($file);
                                            $size = $storage->size($file);
                                            $updated = optional(now()->setTimestamp($storage->lastModified($file)))->format('d/m/Y H:i');
                                            $url = asset('storage/'.$file);
                                        @endphp
                                        <li class="d-flex align-items-center justify-content-between mb-2">
                                            <div class="me-2 text-truncate" title="{{ $name }}">
                                                <i class="far fa-file me-2 text-primary"></i>{{ \Illuminate\Support\Str::limit($name, 28) }}
                                                <div class="text-muted small">{{ number_format($size/1024, 1) }} KB · {{ $updated }}</div>
                                            </div>
                                            <div class="ms-2">
                                                <a href="{{ $url }}" class="btn btn-sm btn-outline-primary" download title="Télécharger">
                                                    <i class="fas fa-download"></i>
                                                </a>
                                                <a href="{{ $url }}" target="_blank" class="btn btn-sm btn-outline-secondary" title="Voir">
                                                    <i class="fas fa-external-link-alt"></i>
                                                </a>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                        <div class="card-footer">
                            <form class="d-flex align-items-center gap-2" method="POST" action="{{ route('operations.files.store', ['operation' => $opId]) }}" enctype="multipart/form-data">
                                @csrf
                                <input class="form-control form-control-sm" type="file" name="fichiers[]" multiple required>
                                <button class="btn btn-sm btn-primary" type="submit">
                                    <i class="fas fa-upload me-1"></i>Uploader
                                </button>
                                <a href="{{ route('operations.show', ['operation' => $opId]) }}" class="btn btn-sm btn-outline-dark ms-auto">
                                    <i class="fas fa-eye me-1"></i>Détails
                                </a>
                            </form>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        
        @if($pages > 1)
            <nav class="mt-4">
                <ul class="pagination justify-content-center">
                    <li class="page-item {{ $page<=1 ? 'disabled' : '' }}">
                        <a class="page-link" href="?{{ http_build_query(array_merge($queryBase, ['page' => $page-1])) }}" tabindex="-1">Précédent</a>
                    </li>
                    @for($p=1; $p<=$pages; $p++)
                        <li class="page-item {{ $p==$page ? 'active' : '' }}">
                            <a class="page-link" href="?{{ http_build_query(array_merge($queryBase, ['page' => $p])) }}">{{ $p }}</a>
                        </li>
                    @endfor
                    <li class="page-item {{ $page>=$pages ? 'disabled' : '' }}">
                        <a class="page-link" href="?{{ http_build_query(array_merge($queryBase, ['page' => $page+1])) }}">Suivant</a>
                    </li>
                </ul>
            </nav>
        @endif
    @endif
</div>
@endsection

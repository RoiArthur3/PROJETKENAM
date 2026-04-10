@extends('layouts.app')

@section('title', 'Audit Post-Import')

@section('content')
<div class="container-fluid py-3">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h4 mb-0">Rapport d'audit post-import</h1>
            <small class="text-muted">Etat des tables apres import historique &mdash; {{ now()->format('d/m/Y H:i') }}</small>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('imports.historique.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left me-1"></i>Retour import
            </a>
            <a href="{{ route('imports.audit') }}" class="btn btn-outline-primary btn-sm">
                <i class="fas fa-sync-alt me-1"></i>Actualiser
            </a>
        </div>
    </div>

    {{-- Totaux --}}
    <div class="row g-3 mb-4">
        @foreach($stats as $table => $s)
            @if(!$s['exists']) @continue @endif
            <div class="col-6 col-md-3">
                <div class="card h-100 border-0 shadow-sm">
                    <div class="card-body py-3 px-3">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <div class="fw-semibold text-muted small">{{ $s['label'] }}</div>
                                <div class="fs-4 fw-bold">{{ number_format($s['count']) }}</div>
                                @if($s['sum'] !== null)
                                    <div class="small text-success">{{ number_format($s['sum'], 2, ',', ' ') }} XAF</div>
                                @endif
                            </div>
                            <span class="badge bg-primary-subtle text-primary rounded-pill fs-6">
                                <i class="fas fa-database fa-sm"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Integrite --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white fw-semibold">
            <i class="fas fa-shield-alt me-2 text-primary"></i>Controle d'integrite
        </div>
        <div class="card-body pb-2">
            @foreach($integrity as $check)
                <div class="alert alert-{{ $check['type'] }} py-2 mb-2">
                    @if($check['type'] === 'success')
                        <i class="fas fa-check-circle me-1"></i>
                    @elseif($check['type'] === 'danger')
                        <i class="fas fa-exclamation-circle me-1"></i>
                    @elseif($check['type'] === 'warning')
                        <i class="fas fa-exclamation-triangle me-1"></i>
                    @else
                        <i class="fas fa-info-circle me-1"></i>
                    @endif
                    {{ $check['message'] }}
                </div>
            @endforeach
        </div>
    </div>

    {{-- Detail par table --}}
    @foreach($stats as $table => $s)
        @if(!$s['exists'])
            <div class="alert alert-light border mb-2">
                <i class="fas fa-times-circle text-danger me-1"></i>
                Table <code>{{ $table }}</code> absente de la base de donnees.
            </div>
            @continue
        @endif

        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <span class="fw-semibold">
                    <i class="fas fa-table me-2 text-secondary"></i>{{ $s['label'] }}
                    <span class="badge bg-secondary ms-2">{{ number_format($s['count']) }} enreg.</span>
                </span>
                @if($s['sum'] !== null)
                    <span class="badge bg-success">Total : {{ number_format($s['sum'], 2, ',', ' ') }} XAF</span>
                @endif
            </div>
            @if($s['recent']->isNotEmpty())
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-sm table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>#ID</th>
                                @if($s['ref_col']) <th>Référence</th> @endif
                                @if($s['name_col'] && $s['name_col'] !== $s['ref_col']) <th>Libellé</th> @endif
                                @if($s['date_col']) <th>Date</th> @endif
                                @if($s['amount_col']) <th class="text-end">Montant</th> @endif
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($s['recent'] as $row)
                            @php($r = (array) $row)
                            <tr>
                                <td class="text-muted small">{{ $r['id'] ?? '—' }}</td>
                                @if($s['ref_col'])
                                    <td><code class="small">{{ $r[$s['ref_col']] ?? '—' }}</code></td>
                                @endif
                                @if($s['name_col'] && $s['name_col'] !== $s['ref_col'])
                                    <td class="small">{{ \Illuminate\Support\Str::limit($r[$s['name_col']] ?? '—', 50) }}</td>
                                @endif
                                @if($s['date_col'])
                                    <td class="small text-muted">{{ $r[$s['date_col']] ?? '—' }}</td>
                                @endif
                                @if($s['amount_col'])
                                    <td class="text-end small">
                                        {{ isset($r[$s['amount_col']]) ? number_format((float)$r[$s['amount_col']], 2, ',', ' ') : '—' }}
                                    </td>
                                @endif
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="px-3 py-2 text-end">
                    <small class="text-muted">5 derniers enregistrements triés par ID desc.</small>
                </div>
            </div>
            @else
                <div class="card-body text-muted small">Aucun enregistrement dans cette table.</div>
            @endif
        </div>
    @endforeach
</div>
@endsection

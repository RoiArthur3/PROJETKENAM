@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="fas fa-book me-2"></i>
                        Plan Comptable SYSCOHADA — Côte d'Ivoire
                    </h4>
                    <span class="badge bg-warning text-dark">SYSCOHADA Révisé</span>
                </div>

                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>SYSCOHADA</strong> (Système Comptable Ouest-Africain Harmonisé) est le référentiel comptable
                        obligatoire en Côte d'Ivoire. Il organise les comptes en 8 classes.
                    </div>

                    <!-- Recherche -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-search"></i></span>
                                <input type="text" id="search-compte" class="form-control"
                                       placeholder="Rechercher un compte (numéro ou libellé)...">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="btn-group" id="filtre-classes">
                                <button type="button" class="btn btn-outline-dark btn-sm active" data-classe="all">Toutes</button>
                                @foreach($classes as $cl)
                                <button type="button" class="btn btn-outline-dark btn-sm" data-classe="{{ $cl }}">Classe {{ $cl }}</button>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    @foreach($classes as $cl)
                    <div class="classe-section mb-4" data-classe="{{ $cl }}">
                        <h5 class="text-dark bg-light p-2 rounded border-start border-dark border-3">
                            <i class="fas fa-folder me-2"></i>
                            Classe {{ $cl }} — {{ $descriptions_classes[$cl] ?? '' }}
                        </h5>
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <th style="width:120px">N° Compte</th>
                                        <th>Intitulé du compte</th>
                                        <th style="width:120px">Type</th>
                                        <th>Description</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($comptes->where('classe', $cl) as $compte)
                                    <tr class="compte-row" data-classe="{{ $cl }}"
                                        data-search="{{ strtolower($compte->numero_compte . ' ' . $compte->nom_compte) }}">
                                        <td class="font-monospace fw-bold text-primary">{{ $compte->numero_compte }}</td>
                                        <td>{{ $compte->nom_compte }}</td>
                                        <td>
                                            @php
                                                $badge = match($compte->type_compte) {
                                                    'Actif'    => 'bg-success',
                                                    'Passif'   => 'bg-warning text-dark',
                                                    'Charges'  => 'bg-danger',
                                                    'Produits' => 'bg-info',
                                                    default    => 'bg-secondary',
                                                };
                                            @endphp
                                            <span class="badge {{ $badge }}">{{ $compte->type_compte }}</span>
                                        </td>
                                        <td class="text-muted small">{{ $compte->description }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @endforeach

                    @if($comptes->isEmpty())
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Le plan comptable SYSCOHADA n'est pas encore chargé.
                        Exécutez la migration : <code>php artisan migrate</code>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Filtre par classe
document.getElementById('filtre-classes')?.addEventListener('click', function(e) {
    const btn = e.target.closest('[data-classe]');
    if (!btn) return;
    document.querySelectorAll('#filtre-classes .btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
    const cl = btn.dataset.classe;
    document.querySelectorAll('.classe-section').forEach(s => {
        s.style.display = (cl === 'all' || s.dataset.classe === cl) ? '' : 'none';
    });
});

// Recherche texte
document.getElementById('search-compte')?.addEventListener('input', function() {
    const q = this.value.toLowerCase().trim();
    document.querySelectorAll('.compte-row').forEach(row => {
        row.style.display = (!q || row.dataset.search.includes(q)) ? '' : 'none';
    });
});
</script>
@endpush
@endsection

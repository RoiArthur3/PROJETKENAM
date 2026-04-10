@extends('layouts.app')

@section('title', 'Journaux Comptables')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1"><i class="fas fa-books me-2 text-primary"></i>Journaux Comptables</h1>
            <p class="text-muted mb-0">Création et gestion des journaux utilisés par le grand journal.</p>
        </div>
        <a href="{{ route('comptabilite.rapports.grand-journal') }}" class="btn btn-outline-primary">
            <i class="fas fa-book-open me-2"></i>Voir le grand journal
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Créer un journal</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('comptabilite.journaux.store') }}" class="row g-3">
                        @csrf
                        <div class="col-md-6">
                            <label class="form-label">Code</label>
                            <input type="text" name="code" class="form-control @error('code') is-invalid @enderror" value="{{ old('code') }}" placeholder="Ex: OD2" required>
                            @error('code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Type</label>
                            <select name="type" class="form-select @error('type') is-invalid @enderror" required>
                                @foreach(['achat' => 'Achats', 'vente' => 'Ventes', 'banque' => 'Banque', 'caisse' => 'Caisse', 'od' => 'OD', 'paie' => 'Paie', 'fiscal' => 'Fiscal', 'autre' => 'Autre'] as $value => $label)
                                    <option value="{{ $value }}" {{ old('type') === $value ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label">Libellé</label>
                            <input type="text" name="libelle" class="form-control @error('libelle') is-invalid @enderror" value="{{ old('libelle') }}" required>
                            @error('libelle')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label">Description</label>
                            <textarea name="description" rows="3" class="form-control @error('description') is-invalid @enderror">{{ old('description') }}</textarea>
                            @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Couleur</label>
                            <input type="text" name="couleur" class="form-control" value="{{ old('couleur', '#0d6efd') }}" placeholder="#0d6efd">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Icône</label>
                            <input type="text" name="icone" class="form-control" value="{{ old('icone', 'fas fa-book') }}" placeholder="fas fa-book">
                        </div>
                        <div class="col-12 form-check ms-1">
                            <input type="checkbox" name="actif" id="actif" class="form-check-input" checked>
                            <label for="actif" class="form-check-label">Journal actif</label>
                        </div>
                        <div class="col-12 d-grid">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-plus me-2"></i>Créer le journal
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Liste des journaux</h5>
                    <span class="badge bg-light text-dark border">{{ $journaux->count() }} journaux</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Code</th>
                                    <th>Libellé</th>
                                    <th>Type</th>
                                    <th>Description</th>
                                    <th>Écritures</th>
                                        <th>Statut</th>
                                        <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($journaux as $journal)
                                    <tr>
                                        <td><span class="badge" style="background: {{ $journal->couleur ?: '#6c757d' }}">{{ $journal->code }}</span></td>
                                        <td>
                                            <div class="fw-semibold">{{ $journal->libelle }}</div>
                                            <div class="small text-muted"><i class="{{ $journal->icone ?: 'fas fa-book' }} me-1"></i>{{ $journal->icone ?: 'fas fa-book' }}</div>
                                        </td>
                                        <td class="text-capitalize">{{ $journal->type }}</td>
                                        <td>{{ $journal->description ?: '—' }}</td>
                                        <td>{{ number_format($journal->ecritures_count, 0, ',', ' ') }}</td>
                                        <td>
                                            <span class="badge {{ $journal->actif ? 'bg-success' : 'bg-secondary' }}">{{ $journal->actif ? 'Actif' : 'Inactif' }}</span>
                                            @if($journal->systeme)
                                                <span class="badge bg-light text-dark border">Système</span>
                                            @endif
                                        </td>
                                            <td>
                                                <button class="btn btn-sm btn-outline-primary" type="button" data-bs-toggle="collapse" data-bs-target="#edit-journal-{{ $journal->id }}" aria-expanded="false" aria-controls="edit-journal-{{ $journal->id }}">
                                                    <i class="fas fa-edit me-1"></i>Modifier
                                                </button>
                                            </td>
                                    </tr>
                                        <tr class="collapse bg-light" id="edit-journal-{{ $journal->id }}">
                                            <td colspan="7">
                                                <form method="POST" action="{{ route('comptabilite.journaux.update', $journal) }}" class="row g-2 align-items-end">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="col-md-2">
                                                        <label class="form-label small">Libellé</label>
                                                        <input type="text" name="libelle" class="form-control form-control-sm" value="{{ old('libelle', $journal->libelle) }}" required>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <label class="form-label small">Type</label>
                                                        <select name="type" class="form-select form-select-sm" required>
                                                            @foreach(['achat' => 'Achats', 'vente' => 'Ventes', 'banque' => 'Banque', 'caisse' => 'Caisse', 'od' => 'OD', 'paie' => 'Paie', 'fiscal' => 'Fiscal', 'autre' => 'Autre'] as $value => $label)
                                                                <option value="{{ $value }}" {{ $journal->type === $value ? 'selected' : '' }}>{{ $label }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <label class="form-label small">Description</label>
                                                        <input type="text" name="description" class="form-control form-control-sm" value="{{ old('description', $journal->description) }}">
                                                    </div>
                                                    <div class="col-md-2">
                                                        <label class="form-label small">Couleur</label>
                                                        <input type="text" name="couleur" class="form-control form-control-sm" value="{{ old('couleur', $journal->couleur) }}" placeholder="#0d6efd">
                                                    </div>
                                                    <div class="col-md-2">
                                                        <label class="form-label small">Icône</label>
                                                        <input type="text" name="icone" class="form-control form-control-sm" value="{{ old('icone', $journal->icone) }}" placeholder="fas fa-book">
                                                    </div>
                                                    <div class="col-md-1">
                                                        <div class="form-check mt-4">
                                                            <input class="form-check-input" type="checkbox" value="1" id="actif-inline-{{ $journal->id }}" name="actif" {{ $journal->actif ? 'checked' : '' }}>
                                                            <label class="form-check-label small" for="actif-inline-{{ $journal->id }}">Actif</label>
                                                        </div>
                                                    </div>
                                                    <div class="col-12 d-flex justify-content-end mt-2">
                                                        <button type="submit" class="btn btn-sm btn-primary">
                                                            <i class="fas fa-save me-1"></i>Enregistrer
                                                        </button>
                                                    </div>
                                                </form>
                                            </td>
                                        </tr>
                                @empty
                                    <tr>
                                            <td colspan="7" class="text-center text-muted py-4">Aucun journal disponible.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
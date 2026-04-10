@extends('layouts.app')

@section('title', 'Modifier un Type d\'Opération')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-edit me-2"></i>Modifier le Type d'Opération
                    </h6>
                    <a href="{{ route('admin.types-operations.index') }}" class="btn btn-sm btn-secondary">
                        <i class="fas fa-arrow-left me-1"></i>Retour
                    </a>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('admin.types-operations.update', $typeOperation) }}">
                        @csrf
                        @method('PUT')

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="code" class="form-label fw-bold">Code *</label>
                                <input type="text" name="code" id="code"
                                       class="form-control @error('code') is-invalid @enderror"
                                       value="{{ old('code', $typeOperation->code) }}" required>
                                @error('code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="libelle" class="form-label fw-bold">Libellé *</label>
                                <input type="text" name="libelle" id="libelle"
                                       class="form-control @error('libelle') is-invalid @enderror"
                                       value="{{ old('libelle', $typeOperation->libelle) }}" required>
                                @error('libelle')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label fw-bold">Description</label>
                            <textarea name="description" id="description" rows="3"
                                      class="form-control @error('description') is-invalid @enderror">{{ old('description', $typeOperation->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="couleur" class="form-label fw-bold">Couleur (Badge)</label>
                                <select name="couleur" id="couleur" class="form-select">
                                    <option value="">— Aucune —</option>
                                    <option value="primary" {{ old('couleur', $typeOperation->couleur) == 'primary' ? 'selected' : '' }}>Primary (Bleu)</option>
                                    <option value="success" {{ old('couleur', $typeOperation->couleur) == 'success' ? 'selected' : '' }}>Success (Vert)</option>
                                    <option value="danger" {{ old('couleur', $typeOperation->couleur) == 'danger' ? 'selected' : '' }}>Danger (Rouge)</option>
                                    <option value="warning" {{ old('couleur', $typeOperation->couleur) == 'warning' ? 'selected' : '' }}>Warning (Jaune)</option>
                                    <option value="info" {{ old('couleur', $typeOperation->couleur) == 'info' ? 'selected' : '' }}>Info (Cyan)</option>
                                    <option value="secondary" {{ old('couleur', $typeOperation->couleur) == 'secondary' ? 'selected' : '' }}>Secondary (Gris)</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="icone" class="form-label fw-bold">Icône (FontAwesome)</label>
                                <input type="text" name="icone" id="icone"
                                       class="form-control"
                                       value="{{ old('icone', $typeOperation->icone) }}"
                                       placeholder="Ex: fa-truck, fa-box, fa-file">
                                <small class="text-muted">Voir <a href="https://fontawesome.com/icons" target="_blank">FontAwesome</a></small>
                            </div>
                        </div>

                        <div class="mb-4">
                            <div class="form-check">
                                <input type="checkbox" name="actif" id="actif" value="1"
                                       class="form-check-input" {{ old('actif', $typeOperation->actif) ? 'checked' : '' }}>
                                <label for="actif" class="form-check-label">Type actif</label>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('admin.types-operations.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-1"></i>Annuler
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i>Mettre à jour
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

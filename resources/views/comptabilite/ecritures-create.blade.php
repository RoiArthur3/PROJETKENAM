@extends('layouts.app')

@section('title', $ecriture ? 'Modifier une écriture' : 'Nouvelle écriture comptable')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">{{ $ecriture ? 'Modifier une écriture comptable' : 'Nouvelle écriture comptable' }}</h1>
            <p class="text-muted mb-0">Saisie manuelle dans le grand journal.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('comptabilite.journaux.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-books me-2"></i>Journaux
            </a>
            <a href="{{ route('comptabilite.ecritures.index') }}" class="btn btn-outline-primary">
                <i class="fas fa-arrow-left me-2"></i>Retour au grand journal
            </a>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <form method="POST" action="{{ $ecriture ? route('comptabilite.ecritures.update', $ecriture) : route('comptabilite.ecritures.store') }}" class="row g-3" enctype="multipart/form-data">
                @csrf
                @if($ecriture)
                    @method('PUT')
                @endif

                <div class="col-md-3">
                    <label class="form-label">Date</label>
                    <input type="date" name="date" class="form-control @error('date') is-invalid @enderror" value="{{ old('date', optional($ecriture?->date)->format('Y-m-d') ?? now()->format('Y-m-d')) }}" required>
                    @error('date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-3">
                    <label class="form-label">Référence</label>
                    <input type="text" name="reference" class="form-control @error('reference') is-invalid @enderror" value="{{ old('reference', $ecriture?->reference) }}" required>
                    @error('reference')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-3">
                    <label class="form-label">Journal</label>
                    <select name="journal_id" class="form-select @error('journal_id') is-invalid @enderror" required>
                        <option value="">Sélectionner</option>
                        @foreach($journaux as $journal)
                            <option value="{{ $journal->id }}" {{ (string) old('journal_id', $ecriture?->journal_id) === (string) $journal->id ? 'selected' : '' }}>
                                {{ $journal->code }} - {{ $journal->libelle }}
                            </option>
                        @endforeach
                    </select>
                    @error('journal_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-3">
                    <label class="form-label">Pièce comptable</label>
                    <input type="text" name="piece_comptable" class="form-control @error('piece_comptable') is-invalid @enderror" value="{{ old('piece_comptable', $ecriture?->piece_comptable) }}">
                    @error('piece_comptable')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-12">
                    <label class="form-label">Libellé</label>
                    <input type="text" name="libelle" class="form-control @error('libelle') is-invalid @enderror" value="{{ old('libelle', $ecriture?->libelle) }}" required>
                    @error('libelle')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label">Compte débit</label>
                    <input type="text" name="compte_debit" class="form-control @error('compte_debit') is-invalid @enderror" value="{{ old('compte_debit', $ecriture?->compte_debit) }}" placeholder="Ex: 411000" required>
                    @error('compte_debit')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label">Compte crédit</label>
                    <input type="text" name="compte_credit" class="form-control @error('compte_credit') is-invalid @enderror" value="{{ old('compte_credit', $ecriture?->compte_credit) }}" placeholder="Ex: 707000" required>
                    @error('compte_credit')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                    <label class="form-label">Montant</label>
                    <input type="number" name="montant" min="0.01" step="0.01" class="form-control @error('montant') is-invalid @enderror" value="{{ old('montant', $ecriture?->montant) }}" required>
                    @error('montant')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-12">
                    <label class="form-label">Description</label>
                    <textarea name="description" rows="4" class="form-control @error('description') is-invalid @enderror">{{ old('description', $ecriture?->description) }}</textarea>
                    @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-12">
                    <div class="alert alert-info mb-0">
                        Le formulaire enregistre une écriture simplifiée en partie double avec un compte débité, un compte crédité et un montant unique.
                    </div>
                </div>

                {{-- Pièce jointe obligatoire --}}
                <div class="col-12">
                    <label class="form-label fw-semibold">
                        <i class="fas fa-paperclip me-1 text-danger"></i>
                        Pièce justificative <span class="text-danger">*</span>
                    </label>

                    @if($ecriture && $ecriture->piece_jointe)
                        <div class="mb-2 p-2 bg-light rounded border d-flex align-items-center gap-2">
                            <i class="fas fa-file-alt text-primary fs-5"></i>
                            <div class="flex-grow-1">
                                <div class="fw-semibold small">{{ $ecriture->piece_jointe_nom ?? basename($ecriture->piece_jointe) }}</div>
                                <a href="{{ Storage::url($ecriture->piece_jointe) }}" target="_blank" class="small text-primary">
                                    <i class="fas fa-eye me-1"></i>Voir le fichier actuel
                                </a>
                            </div>
                            <span class="badge bg-success">Déjà uploadé</span>
                        </div>
                        <label class="form-label small text-muted">Remplacer par un nouveau fichier (optionnel) :</label>
                    @endif

                    <input type="file"
                        name="piece_jointe"
                        class="form-control @error('piece_jointe') is-invalid @enderror"
                        accept=".pdf,.jpg,.jpeg,.png,.webp"
                        {{ $ecriture ? '' : 'required' }}>
                    <div class="form-text text-muted">
                        Formats acceptés : PDF, JPG, PNG, WEBP. Taille max : 5 Mo.
                    </div>
                    @error('piece_jointe')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-12 d-flex justify-content-end gap-2">
                    <a href="{{ route('comptabilite.ecritures.index') }}" class="btn btn-outline-secondary">Annuler</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>{{ $ecriture ? 'Mettre à jour' : 'Enregistrer' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
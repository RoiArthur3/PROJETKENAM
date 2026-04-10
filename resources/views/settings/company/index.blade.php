@extends('layouts.app')

@section('title', 'Paramètres de l\'Entreprise - KENAM SERVICES')

@section('content')
<div class="container-fluid">
    <!-- En-tête -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-building me-2 text-primary"></i>Paramètres de l'Entreprise
            </h1>
            <p class="text-muted mb-0">Informations et configuration de l'entreprise</p>
        </div>
    </div>

    <!-- Alertes -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Informations générales</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('settings.company.update') }}" method="POST">
                        @csrf

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="name" class="form-label">Nom de l'entreprise <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                       id="name" name="name" value="{{ old('name', $company['name']) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="siret" class="form-label">Numéro SIRET <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('siret') is-invalid @enderror"
                                       id="siret" name="siret" value="{{ old('siret', $company['siret']) }}" required>
                                @error('siret')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="address" class="form-label">Adresse <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('address') is-invalid @enderror"
                                      id="address" name="address" rows="3" required>{{ old('address', $company['address']) }}</textarea>
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="phone" class="form-label">Téléphone <span class="text-danger">*</span></label>
                                <input type="tel" class="form-control @error('phone') is-invalid @enderror"
                                       id="phone" name="phone" value="{{ old('phone', $company['phone']) }}" required>
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror"
                                       id="email" name="email" value="{{ old('email', $company['email']) }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Logo de l'entreprise</label>
                            <div class="border rounded p-3">
                                <div class="mb-3">
                                    <input type="file" class="form-control" id="logo" name="logo" accept="image/*">
                                    <div class="form-text">Formats acceptés : JPG, PNG, GIF. Taille max : 2MB</div>
                                </div>
                                @if(isset($company['logo']) && $company['logo'])
                                    <div class="text-center">
                                        <img src="{{ asset('storage/' . $company['logo']) }}" alt="Logo actuel" class="img-fluid" style="max-height: 100px;">
                                        <div class="mt-2">
                                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="removeLogo()">
                                                <i class="fas fa-trash me-1"></i>Supprimer le logo
                                            </button>
                                        </div>
                                    </div>
                                @else
                                    <div class="text-center text-muted">
                                        <i class="fas fa-image fa-3x mb-2"></i>
                                        <p>Aucun logo défini</p>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i>Enregistrer les modifications
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <!-- Informations fiscales -->
            <div class="card mb-4">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-calculator me-2"></i>Informations fiscales
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="tax_number" class="form-label">Numéro de TVA</label>
                        <input type="text" class="form-control" id="tax_number" name="tax_number"
                               value="{{ old('tax_number', $company['tax_number'] ?? '') }}"
                               placeholder="FR 123 456 789">
                    </div>

                    <div class="mb-3">
                        <label for="rcs_number" class="form-label">Numéro RCS</label>
                        <input type="text" class="form-control" id="rcs_number" name="rcs_number"
                               value="{{ old('rcs_number', $company['rcs_number'] ?? '') }}"
                               placeholder="123 456 789 RCS Paris">
                    </div>

                    <div class="mb-3">
                        <label for="capital" class="form-label">Capital social</label>
                        <input type="text" class="form-control" id="capital" name="capital"
                               value="{{ old('capital', $company['capital'] ?? '') }}"
                               placeholder="10 000 €">
                    </div>
                </div>
            </div>

            <!-- Coordonnées bancaires -->
            <div class="card">
                <div class="card-header">
                    <h6 class="mb-0">
                        <i class="fas fa-university me-2"></i>Coordonnées bancaires
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="bank_name" class="form-label">Nom de la banque</label>
                        <input type="text" class="form-control" id="bank_name" name="bank_name"
                               value="{{ old('bank_name', $company['bank_name'] ?? '') }}">
                    </div>

                    <div class="mb-3">
                        <label for="iban" class="form-label">IBAN</label>
                        <input type="text" class="form-control" id="iban" name="iban"
                               value="{{ old('iban', $company['iban'] ?? '') }}"
                               placeholder="FR76 1234 5678 9012 3456 7890 123">
                    </div>

                    <div class="mb-3">
                        <label for="bic" class="form-label">BIC/SWIFT</label>
                        <input type="text" class="form-control" id="bic" name="bic"
                               value="{{ old('bic', $company['bic'] ?? '') }}"
                               placeholder="BNPAFRPP">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function removeLogo() {
    if (confirm('Êtes-vous sûr de vouloir supprimer le logo actuel ?')) {
        // TODO: Implémenter la suppression du logo
        alert('Logo supprimé avec succès');
    }
}
</script>
@endsection

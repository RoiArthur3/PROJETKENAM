@extends('layouts.app')

@section('title', 'Modifier les informations de connexion | KENAM SERVICES')

@section('content')
<div class="container-fluid">
    <div class="card shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-user-edit me-2"></i>Modifier les informations de connexion</h5>
            <a href="{{ route('admin.comptes.permissions', ['user_id' => $user->id]) }}" class="btn btn-sm btn-light">
                <i class="fas fa-arrow-left me-1"></i>Retour aux permissions
            </a>
        </div>
        <div class="card-body">
            <div class="alert alert-info mb-4">
                <i class="fas fa-info-circle me-2"></i>
                <strong>Modification des informations de connexion</strong> pour <strong>{{ $user->name }}</strong>.
                <ul class="mb-0 mt-2">
                    <li>L'utilisateur recevra une notification par email en cas de changement d'adresse email.</li>
                    <li>Si vous modifiez le mot de passe, l'utilisateur devra se reconnecter avec le nouveau mot de passe.</li>
                </ul>
            </div>

            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('admin.comptes.update-credentials', $user) }}" class="row g-3">
                @csrf
                @method('PUT')

                <div class="col-12">
                    <h6 class="text-muted mb-3"><i class="fas fa-user-shield me-2"></i>Informations de connexion</h6>
                </div>

                <div class="col-md-6">
                    <label class="form-label small">Nom complet</label>
                    <input type="text" class="form-control" value="{{ $user->name }}" disabled>
                </div>

                <div class="col-md-6">
                    <label class="form-label small">Rôle</label>
                    <input type="text" class="form-control" value="{{ ucfirst($user->role) }}" disabled>
                </div>

                <div class="col-md-6">
                    <label class="form-label small">Email <span class="text-danger">*</span></label>
                    <input type="email" class="form-control @error('email') is-invalid @enderror"
                           name="email" value="{{ old('email', $user->email) }}" required
                           placeholder="Ex: user@kenamservices.net">
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label small">Téléphone <span class="text-danger">*</span></label>
                    <input type="tel" class="form-control @error('phone') is-invalid @enderror"
                           name="phone" value="{{ old('phone', $user->phone) }}" required
                           placeholder="Ex: +225 07 00 00 00 00">
                    @error('phone')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-12 mt-4">
                    <h6 class="text-muted mb-3"><i class="fas fa-key me-2"></i>Modification du mot de passe</h6>
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Laissez ces champs vides pour conserver le mot de passe actuel.
                    </div>
                </div>

                <div class="col-md-6">
                    <label class="form-label small">Nouveau mot de passe</label>
                    <input type="password" class="form-control @error('password') is-invalid @enderror"
                           name="password" placeholder="Laisser vide pour ne pas changer">
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label small">Confirmer le mot de passe</label>
                    <input type="password" class="form-control"
                           name="password_confirmation" placeholder="Confirmer le nouveau mot de passe">
                </div>

                <div class="col-12 mt-4">
                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('admin.comptes.permissions', ['user_id' => $user->id]) }}"
                           class="btn btn-light">
                            <i class="fas fa-times me-1"></i> Annuler
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i> Enregistrer les modifications
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Désactiver la soumission du formulaire si aucun changement n'a été effectué
    const form = document.querySelector('form');
    const initialData = new FormData(form);

    form.addEventListener('submit', function(e) {
        const currentData = new FormData(form);
        let hasChanges = false;

        // Vérifier les champs modifiés
        for (let [key, value] of currentData.entries()) {
            if (key === 'password' || key === 'password_confirmation') {
                // Pour les champs de mot de passe, on vérifie s'ils sont remplis
                if (value) {
                    hasChanges = true;
                    break;
                }
            } else if (initialData.get(key) !== value) {
                hasChanges = true;
                break;
            }
        }

        if (!hasChanges) {
            e.preventDefault();
            alert('Aucune modification détectée.');
        } else if (!confirm('Êtes-vous sûr de vouloir mettre à jour les informations de connexion ?')) {
            e.preventDefault();
        }
    });
});
</script>
@endpush
@endsection

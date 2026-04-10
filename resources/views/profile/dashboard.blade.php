@extends('layouts.app')

@section('title', 'Mon Profil')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <div>
                <h1 class="h3 mb-0">
                    <i class="fas fa-id-card text-primary me-2"></i>Mon Profil
                </h1>
                <p class="text-muted mb-0">Informations du compte connecte, employe et acces rapides.</p>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <div class="row g-4">
        <div class="col-lg-4">
            <div class="card shadow-sm h-100">
                <div class="card-body text-center">
                    @if($personnel && $personnel->photo_profil)
                        <img src="{{ asset('storage/' . $personnel->photo_profil) }}" alt="Photo profil"
                             class="rounded-circle mb-3"
                             style="width: 140px; height: 140px; object-fit: cover; border: 3px solid #e9ecef;">
                    @else
                        <div class="rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                             style="width: 140px; height: 140px; background: #f1f3f5; color: #6c757d; font-size: 3rem;">
                            <i class="fas fa-user"></i>
                        </div>
                    @endif

                    <h5 class="mb-1">{{ $user->name }}</h5>
                    <p class="text-muted mb-2">{{ $user->email ?: 'Email non renseigne' }}</p>
                    <span class="badge bg-primary text-uppercase">{{ $user->role }}</span>

                    @if($personnel)
                        <hr>
                        <form action="{{ route('profile.photo.update') }}" method="POST" enctype="multipart/form-data" class="text-start">
                            @csrf
                            <label for="photo_profil" class="form-label fw-semibold">Mettre a jour la photo</label>
                            <input type="file" name="photo_profil" id="photo_profil" class="form-control mb-2" accept="image/*" required>
                            <button type="submit" class="btn btn-sm btn-outline-primary w-100">
                                <i class="fas fa-upload me-1"></i>Mettre a jour
                            </button>
                        </form>
                    @else
                        <div class="alert alert-warning mt-3 mb-0 text-start">
                            Aucun dossier employe n'est lie a ce compte.
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fas fa-user-check text-success me-2"></i>Informations Utilisateur</h5>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <small class="text-muted d-block">Nom</small>
                            <strong>{{ $user->name }}</strong>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted d-block">Email</small>
                            <strong>{{ $user->email ?: 'Non renseigne' }}</strong>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted d-block">Telephone</small>
                            <strong>{{ $user->phone ?: ($user->telephone ?: 'Non renseigne') }}</strong>
                        </div>
                        <div class="col-md-6">
                            <small class="text-muted d-block">Role</small>
                            <strong class="text-uppercase">{{ $user->role }}</strong>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-id-badge text-info me-2"></i>Informations Employe</h5>
                    @if($personnel)
                        <a href="{{ route('rh.personnel.show', $personnel->id) }}" class="btn btn-sm btn-outline-info">
                            <i class="fas fa-external-link-alt me-1"></i>Voir la fiche RH
                        </a>
                    @endif
                </div>
                <div class="card-body">
                    @if($personnel)
                        <div class="row g-3">
                            <div class="col-md-6">
                                <small class="text-muted d-block">Matricule</small>
                                <strong>{{ $personnel->matricule ?: 'N/A' }}</strong>
                            </div>
                            <div class="col-md-6">
                                <small class="text-muted d-block">Nom complet</small>
                                <strong>{{ $personnel->nom_complet }}</strong>
                            </div>
                            <div class="col-md-6">
                                <small class="text-muted d-block">Poste</small>
                                <strong>{{ $personnel->poste ?: 'N/A' }}</strong>
                            </div>
                            <div class="col-md-6">
                                <small class="text-muted d-block">Service</small>
                                <strong>{{ $personnel->service ?: 'N/A' }}</strong>
                            </div>
                            <div class="col-md-6">
                                <small class="text-muted d-block">Date embauche</small>
                                <strong>{{ $personnel->date_embauche ? $personnel->date_embauche->format('d/m/Y') : 'N/A' }}</strong>
                            </div>
                            <div class="col-md-6">
                                <small class="text-muted d-block">Salaire de base</small>
                                <strong>{{ $personnel->salaire_base_formatte }}</strong>
                            </div>
                        </div>
                    @else
                        <p class="text-muted mb-0">Aucune information employe disponible pour ce compte.</p>
                    @endif
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-file-invoice-dollar text-warning me-2"></i>Derniere Fiche de Paie</h5>
                    @if($personnel)
                        <a href="{{ route('rh.personnel.paies', $personnel->id) }}" class="btn btn-sm btn-outline-warning">
                            <i class="fas fa-list me-1"></i>Toutes mes paies
                        </a>
                    @endif
                </div>
                <div class="card-body">
                    @if($personnel && $lastPaie)
                        <div class="row g-3 align-items-center">
                            <div class="col-md-3">
                                <small class="text-muted d-block">Periode</small>
                                <strong>{{ \Carbon\Carbon::parse($lastPaie->periode)->format('m/Y') }}</strong>
                            </div>
                            <div class="col-md-3">
                                <small class="text-muted d-block">Salaire net</small>
                                <strong>{{ number_format((float) $lastPaie->salaire_net, 0, ',', ' ') }} FCFA</strong>
                            </div>
                            <div class="col-md-3">
                                <small class="text-muted d-block">Statut</small>
                                <span class="badge {{ ($lastPaie->statut ?? '') === 'PAYE' ? 'bg-success' : 'bg-secondary' }}">{{ $lastPaie->statut ?? 'N/A' }}</span>
                            </div>
                            <div class="col-md-3 text-md-end">
                                <a href="{{ route('rh.personnel.paies.bulletin-pdf', [$personnel->id, $lastPaie->id]) }}" class="btn btn-outline-primary btn-sm" target="_blank">
                                    <i class="fas fa-file-pdf me-1"></i>Bulletin PDF
                                </a>
                            </div>
                        </div>
                    @else
                        <p class="text-muted mb-0">Aucune fiche de paie disponible pour le moment.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm mt-4">
        <div class="card-header bg-white">
            <h5 class="mb-0"><i class="fas fa-bolt text-primary me-2"></i>Raccourcis vers mes acces</h5>
        </div>
        <div class="card-body">
            <div class="row g-3">
                @forelse($shortcuts as $shortcut)
                    <div class="col-md-4 col-lg-3">
                        <a href="{{ $shortcut['url'] }}" class="btn btn-outline-primary w-100 d-flex align-items-center justify-content-center gap-2 py-3">
                            <i class="{{ $shortcut['icon'] }}"></i>
                            <span>{{ $shortcut['label'] }}</span>
                        </a>
                    </div>
                @empty
                    <div class="col-12">
                        <p class="text-muted mb-0">Aucun acces module detecte.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection

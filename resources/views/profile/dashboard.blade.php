@extends('layouts.app')

@section('title', 'Mon Profil - KENAM SERVICES')

@push('styles')
<style>
/* Variables CSS personnalisées */
:root {
    --primary-gradient: linear-gradient(135deg, #16a34a, #15803d);
    --secondary-gradient: linear-gradient(135deg, #64748b, #475569);
    --success-gradient: linear-gradient(135deg, #22c55e, #16a34a);
    --warning-gradient: linear-gradient(135deg, #f59e0b, #d97706);
    --danger-gradient: linear-gradient(135deg, #ef4444, #dc2626);
    --info-gradient: linear-gradient(135deg, #06b6d4, #0891b2);
}

/* Avatar moderne */
.profile-avatar {
    width: 160px;
    height: 160px;
    border-radius: 50%;
    object-fit: cover;
    border: 5px solid transparent;
    background: linear-gradient(white, white) padding-box,
                var(--primary-gradient) border-box;
    box-shadow: 0 12px 32px rgba(22, 163, 74, 0.25);
    transition: all 0.4s ease;
}

.profile-avatar:hover {
    transform: scale(1.05);
    box-shadow: 0 16px 40px rgba(22, 163, 74, 0.35);
}

.profile-avatar-placeholder {
    width: 160px;
    height: 160px;
    border-radius: 50%;
    background: var(--primary-gradient);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 3.5rem;
    color: white;
    border: 5px solid white;
    margin: 0 auto;
    box-shadow: 0 12px 32px rgba(22, 163, 74, 0.25);
    transition: all 0.4s ease;
}

.profile-avatar-placeholder:hover {
    transform: scale(1.05);
    box-shadow: 0 16px 40px rgba(22, 163, 74, 0.35);
}

/* Cartes modernes */
.modern-card {
    background: white;
    border-radius: 20px;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.08);
    border: 1px solid rgba(22, 163, 74, 0.1);
    overflow: hidden;
    transition: all 0.4s ease;
    position: relative;
}

.modern-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: var(--primary-gradient);
}

.modern-card:hover {
    box-shadow: 0 12px 48px rgba(0, 0, 0, 0.12);
    transform: translateY(-4px);
}

.modern-card-header {
    padding: 2rem 2rem 1rem;
    background: linear-gradient(135deg, rgba(22, 163, 74, 0.05), rgba(255, 255, 255, 0.8));
    border-bottom: 1px solid rgba(22, 163, 74, 0.1);
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.modern-card-title {
    margin: 0;
    font-weight: 700;
    color: #1f2937;
    font-size: 1.25rem;
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.modern-card-body {
    padding: 2rem;
}

.modern-input {
    border: 2px solid #e5e7eb;
    border-radius: 16px;
    padding: 1rem 1.25rem;
    transition: all 0.4s ease;
    font-size: 0.95rem;
    background: white;
}

.modern-input:focus {
    border-color: #16a34a;
    box-shadow: 0 0 0 4px rgba(22, 163, 74, 0.1);
    outline: none;
    transform: translateY(-1px);
}

.btn-modern {
    border-radius: 16px;
    padding: 1rem 2rem;
    font-weight: 600;
    border: none;
    transition: all 0.4s ease;
    font-size: 0.9rem;
    display: inline-flex;
    align-items: center;
    gap: 0.75rem;
    position: relative;
    overflow: hidden;
}

.btn-modern::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
    transition: left 0.6s ease;
}

.btn-modern:hover::before {
    left: 100%;
}

.btn-primary-modern {
    background: var(--primary-gradient);
    color: white;
    box-shadow: 0 4px 16px rgba(22, 163, 74, 0.25);
}

.btn-primary-modern:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(22, 163, 74, 0.35);
}

.btn-outline-modern {
    background: white;
    color: #16a34a;
    border: 2px solid #16a34a;
    box-shadow: 0 2px 8px rgba(22, 163, 74, 0.1);
}

.btn-outline-modern:hover {
    background: var(--primary-gradient);
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(22, 163, 74, 0.35);
}

.badge-modern {
    padding: 0.75rem 1.25rem;
    border-radius: 25px;
    font-size: 0.75rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.75px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.badge-superadmin {
    background: var(--danger-gradient);
    color: white;
}

.badge-admin {
    background: var(--success-gradient);
    color: white;
}

.badge-user {
    background: var(--secondary-gradient);
    color: white;
}

.modern-hr {
    border: none;
    height: 2px;
    background: linear-gradient(90deg, transparent, rgba(22, 163, 74, 0.2), transparent);
    margin: 2rem 0;
    border-radius: 1px;
}

.info-item {
    margin-bottom: 1.5rem;
    padding: 1rem;
    background: linear-gradient(135deg, rgba(22, 163, 74, 0.02), rgba(255, 255, 255, 0.8));
    border-radius: 12px;
    border: 1px solid rgba(22, 163, 74, 0.05);
    transition: all 0.3s ease;
}

.info-item:hover {
    background: linear-gradient(135deg, rgba(22, 163, 74, 0.05), rgba(255, 255, 255, 0.9));
    transform: translateX(4px);
}

.info-label {
    display: block;
    font-size: 0.85rem;
    color: #6b7280;
    margin-bottom: 0.5rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.info-value {
    font-size: 1.1rem;
    color: #1f2937;
    font-weight: 700;
}

.data-source-badge.profile {
    background: var(--info-gradient);
    color: white;
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
    box-shadow: 0 2px 8px rgba(6, 182, 212, 0.25);
}

/* Dashboard header amélioré */
.dashboard-header {
    background: linear-gradient(135deg, rgba(22, 163, 74, 0.05), rgba(255, 255, 255, 0.9));
    border-radius: 20px;
    padding: 2.5rem;
    margin-bottom: 2rem;
    border: 1px solid rgba(22, 163, 74, 0.1);
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.05);
}

.dashboard-header h1 {
    color: #1f2937;
    font-weight: 700;
    font-size: 2rem;
    margin-bottom: 0.5rem;
}

.subtitle {
    color: #6b7280;
    font-size: 1.1rem;
    font-weight: 400;
}

/* Animations pour les raccourcis */
.shortcut-card {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 2rem 1.5rem;
    background: linear-gradient(135deg, rgba(22, 163, 74, 0.02), white);
    border: 2px solid rgba(22, 163, 74, 0.1);
    border-radius: 20px;
    text-decoration: none;
    color: #374151;
    transition: all 0.4s ease;
    min-height: 140px;
    justify-content: center;
    position: relative;
    overflow: hidden;
}

.shortcut-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: var(--primary-gradient);
    transform: scaleX(0);
    transition: transform 0.4s ease;
}

.shortcut-card:hover::before {
    transform: scaleX(1);
}

.shortcut-card:hover {
    background: var(--primary-gradient);
    color: white;
    transform: translateY(-6px);
    box-shadow: 0 12px 32px rgba(22, 163, 74, 0.35);
    border-color: transparent;
}

.shortcut-icon {
    font-size: 2.5rem;
    margin-bottom: 1rem;
    transition: all 0.4s ease;
}

.shortcut-card:hover .shortcut-icon {
    transform: scale(1.2) rotate(5deg);
}

.shortcut-label {
    font-weight: 600;
    font-size: 0.95rem;
    text-align: center;
    transition: all 0.3s ease;
}

.shortcut-card:hover .shortcut-label {
    transform: scale(1.05);
}

.empty-state {
    text-align: center;
    padding: 4rem;
    color: #9ca3af;
}

.empty-state i {
    font-size: 4rem;
    margin-bottom: 1.5rem;
    display: block;
    opacity: 0.5;
}

.empty-state p {
    margin: 0;
    font-size: 1.2rem;
    font-weight: 500;
}

/* Animations de chargement */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.modern-card {
    animation: fadeInUp 0.6s ease-out;
}

.modern-card:nth-child(2) {
    animation-delay: 0.1s;
}

.modern-card:nth-child(3) {
    animation-delay: 0.2s;
}

/* Responsive improvements */
@media (max-width: 768px) {
    .dashboard-header {
        padding: 1.5rem;
    }

    .dashboard-header h1 {
        font-size: 1.5rem;
    }

    .modern-card-header {
        padding: 1.5rem 1.5rem 1rem;
        flex-direction: column;
        gap: 1rem;
        text-align: center;
    }

    .modern-card-body {
        padding: 1.5rem;
    }

    .profile-avatar,
    .profile-avatar-placeholder {
        width: 120px;
        height: 120px;
        font-size: 2.5rem;
    }
}
</style>
@endpush

@section('content')
<div class="dashboard-container">
    <div class="dashboard-content">

        <!-- MODERN DASHBOARD HEADER -->
        <div class="dashboard-header">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 class="mb-2">
                        <i class="fas fa-id-card me-3"></i>Mon Profil
                    </h1>
                    <p class="subtitle mb-0">
                        Informations du compte connecté, accès rapides et gestion des données personnelles
                    </p>
                </div>
                <div class="col-md-4 text-end">
                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('profile.edit') }}" class="btn btn-light btn-sm">
                            <i class="fas fa-edit me-1"></i>Modifier le profil
                        </a>
                        <button class="btn btn-light btn-sm" onclick="location.reload()">
                            <i class="fas fa-sync-alt me-1"></i>Actualiser
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-end align-items-center mb-4">
            <span class="data-source-badge profile">
                <i class="fas fa-user me-1"></i>Profil Utilisateur
            </span>
            <span class="ms-2 text-muted">
                <i class="fas fa-clock me-1"></i>{{ now()->format('d/m/Y H:i') }}
            </span>
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
            <div class="modern-card h-100">
                <div class="modern-card-header">
                    <h5 class="modern-card-title">
                        <i class="fas fa-user-circle me-2"></i>Photo de Profil
                    </h5>
                </div>
                <div class="modern-card-body text-center">
                    @if($user->photo_profil)
                        <img src="{{ asset('storage/' . $user->photo_profil) }}" alt="Photo profil"
                             class="profile-avatar mb-3">
                    @else
                        <div class="profile-avatar-placeholder mb-3">
                            <i class="fas fa-user"></i>
                        </div>
                    @endif

                    <h5 class="mb-1">{{ $user->name }}</h5>
                    <p class="text-muted mb-2">{{ $user->email ?: 'Email non renseigné' }}</p>
                    <span class="badge-modern badge-{{ $user->role === 'superadmin' ? 'superadmin' : ($user->role === 'admin' ? 'admin' : 'user') }}">
                        {{ strtoupper($user->role) }}
                    </span>

                    <hr class="modern-hr">
                    <form action="{{ route('profile.photo.update') }}" method="POST" enctype="multipart/form-data" class="text-start">
                        @csrf
                        <label for="photo_profil" class="form-label fw-semibold">Mettre à jour la photo</label>
                        <input type="file" name="photo_profil" id="photo_profil" class="form-control modern-input mb-2" accept="image/*" required>
                        <button type="submit" class="btn-modern btn-primary-modern w-100">
                            <i class="fas fa-upload me-1"></i>Mettre à jour
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="modern-card mb-4">
                <div class="modern-card-header">
                    <h5 class="modern-card-title">
                        <i class="fas fa-user-check me-2"></i>Informations Utilisateur
                    </h5>
                    <div>
                        <a href="{{ route('profile.edit') }}" class="btn-modern btn-outline-modern">
                            <i class="fas fa-edit me-1"></i>Modifier
                        </a>
                    </div>
                </div>
                <div class="modern-card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="info-item">
                                <label class="info-label">Nom</label>
                                <div class="info-value">{{ $user->name }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-item">
                                <label class="info-label">Email</label>
                                <div class="info-value">{{ $user->email ?: 'Non renseigné' }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-item">
                                <label class="info-label">Téléphone</label>
                                <div class="info-value">{{ $user->phone ?: ($user->telephone ?: 'Non renseigné') }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-item">
                                <label class="info-label">Rôle</label>
                                <div class="info-value">
                                    <span class="badge-modern badge-{{ $user->role === 'superadmin' ? 'superadmin' : ($user->role === 'admin' ? 'admin' : 'user') }}">
                                        {{ strtoupper($user->role) }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modern-card mb-4">
                <div class="modern-card-header">
                    <h5 class="modern-card-title">
                        <i class="fas fa-id-badge me-2"></i>Informations Employé
                    </h5>
                    @if($personnel)
                        <a href="{{ route('rh.personnel.show', $personnel->id) }}" class="btn-modern btn-outline-modern">
                            <i class="fas fa-external-link-alt me-1"></i>Voir la fiche RH
                        </a>
                    @endif
                </div>
                <div class="modern-card-body">
                    @if($personnel)
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="info-item">
                                    <label class="info-label">Matricule</label>
                                    <div class="info-value">{{ $personnel->matricule ?: 'N/A' }}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-item">
                                    <label class="info-label">Nom complet</label>
                                    <div class="info-value">{{ $personnel->nom_complet }}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-item">
                                    <label class="info-label">Poste</label>
                                    <div class="info-value">{{ $personnel->poste ?: 'N/A' }}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-item">
                                    <label class="info-label">Service</label>
                                    <div class="info-value">{{ $personnel->service ?: 'N/A' }}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-item">
                                    <label class="info-label">Date d'embauche</label>
                                    <div class="info-value">{{ $personnel->date_embauche ? $personnel->date_embauche->format('d/m/Y') : 'N/A' }}</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-item">
                                    <label class="info-label">Salaire de base</label>
                                    <div class="info-value">{{ $personnel->salaire_base_formatte }}</div>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="empty-state">
                            <i class="fas fa-user-tie"></i>
                            <p>Aucune information employé disponible pour ce compte.</p>
                        </div>
                    @endif
                </div>
            </div>

            <div class="modern-card">
                <div class="modern-card-header">
                    <h5 class="modern-card-title">
                        <i class="fas fa-file-invoice-dollar me-2"></i>Dernière Fiche de Paie
                    </h5>
                    @if($personnel)
                        <a href="{{ route('rh.personnel.paies', $personnel->id) }}" class="btn-modern btn-outline-modern">
                            <i class="fas fa-list me-1"></i>Toutes mes paies
                        </a>
                    @endif
                </div>
                <div class="modern-card-body">
                    @if($personnel && $lastPaie)
                        <div class="row g-3 align-items-center">
                            <div class="col-md-3">
                                <div class="info-item">
                                    <label class="info-label">Période</label>
                                    <div class="info-value">{{ \Carbon\Carbon::parse($lastPaie->periode)->format('m/Y') }}</div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="info-item">
                                    <label class="info-label">Salaire net</label>
                                    <div class="info-value">{{ number_format((float) $lastPaie->salaire_net, 0, ',', ' ') }} FCFA</div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="info-item">
                                    <label class="info-label">Statut</label>
                                    <div class="info-value">
                                        <span class="badge-modern {{ ($lastPaie->statut ?? '') === 'PAYE' ? 'badge-admin' : 'badge-user' }}">
                                            {{ $lastPaie->statut ?? 'N/A' }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 text-md-end">
                                <a href="{{ route('rh.personnel.paies.bulletin-pdf', [$personnel->id, $lastPaie->id]) }}" class="btn-modern btn-outline-modern" target="_blank">
                                    <i class="fas fa-file-pdf me-1"></i>Bulletin PDF
                                </a>
                            </div>
                        </div>
                    @else
                        <div class="empty-state">
                            <i class="fas fa-file-invoice-dollar"></i>
                            <p>Aucune fiche de paie disponible pour le moment.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="modern-card mt-4">
        <div class="modern-card-header">
            <h5 class="modern-card-title">
                <i class="fas fa-bolt me-2"></i>Raccourcis vers mes accès
            </h5>
        </div>
        <div class="modern-card-body">
            <div class="row g-3">
                @forelse($shortcuts as $shortcut)
                    <div class="col-md-4 col-lg-3">
                        <a href="{{ $shortcut['url'] }}" class="shortcut-card">
                            <div class="shortcut-icon">
                                <i class="{{ $shortcut['icon'] }}"></i>
                            </div>
                            <div class="shortcut-label">{{ $shortcut['label'] }}</div>
                        </a>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="empty-state">
                            <i class="fas fa-lock"></i>
                            <p>Aucun accès module détecté</p>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    </div>
@endsection

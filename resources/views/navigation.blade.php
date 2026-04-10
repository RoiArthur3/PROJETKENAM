<!-- Header Navigation -->
<nav class="navbar-custom">
    <div class="header-content">

        <div class="header-title">
            @yield('header-title', 'Tableau de bord')
        </div>

        <div class="header-actions">
            @if(auth()->check())
                <!-- Cloche de Notification -->
                <a href="{{ route('validations.pending') }}" class="nav-button position-relative" title="Validations en attente">
                    <i class="fas fa-bell {{ ($pendingValidationsCount ?? 0) > 0 ? 'text-warning' : '' }}"></i>
                    @if(($pendingValidationsCount ?? 0) > 0)
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.65rem; margin-top: 5px; margin-left: -5px;">
                            {{ $pendingValidationsCount }}
                            <span class="visually-hidden">notifications en attente</span>
                        </span>
                    @endif
                    <span class="d-none d-md-inline ms-1">Notifications</span>
                </a>

                <!-- Bouton Paramètres (visible uniquement pour superadmin et admin) -->
                @if(auth()->user() && in_array(auth()->user()->role, ['superadmin', 'admin']))
                <a href="/parametrage" class="nav-button">
                    <i class="fas fa-cog"></i>
                    <span>Paramètres</span>
                    <span class="ms-2 fw-bold">{{ auth()->user()->name }}</span>
                </a>
                @endif

                <!-- Profil Utilisateur & Déconnexion -->
                <div class="d-flex flex-column align-items-end ms-3 justify-content-center border-start ps-3" style="border-color: rgba(255,255,255,0.2) !important;">
                    <div class="text-white fw-bold mb-1" style="font-size: 0.95rem; line-height: 1;">
                        <i class="fas fa-user-circle me-1"></i> {{ auth()->user()->name }}
                    </div>
                    <a href="#" class="text-white-50 text-decoration-none d-flex align-items-center" 
                       onclick="event.preventDefault(); document.getElementById('logout-form').submit();"
                       style="font-size: 0.8rem; transition: color 0.2s;"
                       onmouseover="this.classList.remove('text-white-50'); this.classList.add('text-white')" 
                       onmouseout="this.classList.remove('text-white'); this.classList.add('text-white-50')">
                        <i class="fas fa-power-off me-1" style="font-size: 0.75rem;"></i> Déconnexion
                    </a>
                </div>

                <form id="logout-form" action="/logout" method="POST" style="display: none;">
                    @csrf
                </form>
            @endif
        </div>
    </div>
</nav>

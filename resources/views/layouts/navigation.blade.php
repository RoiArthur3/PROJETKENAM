<!-- Header Navigation -->
<nav class="navbar-custom">
    <div class="header-content">

        <div class="header-title">
            @yield('header-title', 'Tableau de bord')
        </div>

        <div class="header-actions">
            @if(auth()->check())
                @php($canAccessValidations = auth()->user()->canAccessModule('validations'))
                <!-- Cloche de Notification -->
                @if($canAccessValidations)
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
                @endif

                <!-- Bouton Paramètres (visible uniquement pour superadmin) -->
                @php($userRole = strtolower((string) (auth()->user()->role ?? '')))
                @if($userRole === 'superadmin')
                <a href="/parametrage" class="nav-button">
                    <i class="fas fa-cog"></i>
                    <span>Paramètres</span>
                </a>
                @endif

                <!-- Profil & Déconnexion -->
                <a href="{{ route('profile.dashboard') }}" class="nav-button" title="Mon profil">
                    <i class="fas fa-user-circle"></i>
                    <span>{{ auth()->user()->name }}</span>
                </a>

                <a href="#" class="nav-button" title="Déconnexion"
                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="fas fa-power-off"></i>
                    <span class="d-none d-md-inline">Déconnexion</span>
                </a>

                <form id="logout-form" action="/logout" method="POST" style="display: none;">
                    @csrf
                </form>
            @endif
        </div>
    </div>
</nav>

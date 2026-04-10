<!-- Sidebar pour services -->
<div id="app-sidebar" style="position: fixed; left: 0; top: 0; width: 280px; height: 100vh; background: linear-gradient(180deg, #f0fdf4 0%, #dcfce7 100%); border-right: 2px solid #16a34a; box-shadow: 2px 0 8px rgba(0, 0, 0, 0.1); z-index: 1100; overflow-y: auto;">

    <!-- Header du menu -->
    <div style="padding: 16px 20px; border-bottom: 1px solid #bbf7d0; background: #ffffff; position: sticky; top: 0; z-index: 1200;">
        <a href="{{ route('services.dashboard') }}" style="display: inline-flex; align-items: center; text-decoration:none; gap:8px;">
            <img src="{{ asset('images/logo-kenam.png') }}" alt="KENAM SERVICES" style="max-height:32px; object-fit:contain;">
            <span style="font-weight: 800; font-size: 18px; letter-spacing: 1px; color: #16a34a; text-transform: uppercase;">KENAM SERVICES</span>
        </a>
    </div>

    <!-- Navigation items -->
    <div style="padding: 16px 0;">

        <!-- Dashboard du service -->
        <div class="menu-section">
            <a href="{{ route('services.dashboard') }}" class="menu-parent active" style="display:flex;align-items:center;padding:14px 20px;color:#166534;text-decoration:none;font-weight:600;border-left:4px solid #16a34a;background:rgba(22,101,52,0.1);">
                <i class="fas fa-tachometer-alt" style="width:20px;margin-right:12px;color:#16a34a;"></i>
                <span>Tableau de bord</span>
            </a>
        </div>

        <!-- Profil du service -->
        <div class="menu-section">
            <a href="{{ route('services.profile') }}" class="menu-parent" style="display:flex;align-items:center;padding:14px 20px;color:#166534;text-decoration:none;font-weight:600;border-left:4px solid transparent;">
                <i class="fas fa-user-cog" style="width:20px;margin-right:12px;color:#16a34a;"></i>
                <span>Mon Profil</span>
            </a>
        </div>

        <!-- Requêtes -->
        <div class="menu-section">
            <button class="menu-parent toggle" data-target="#sm-requetes" style="width:100%;display:flex;align-items:center;justify-content:space-between;padding:14px 20px;background:transparent;border:none;color:#166534;font-weight:600;border-bottom:1px solid #dcfce7;cursor:pointer;">
                <div style="display:flex;align-items:center;">
                    <i class="fas fa-file-alt" style="width:20px;margin-right:12px;color:#16a34a;"></i>
                    <span>Requêtes</span>
                </div>
                <i class="fas fa-chevron-right" style="transition:transform .3s;"></i>
            </button>
            <div id="sm-requetes" class="submenu" style="max-height:0;overflow:hidden;transition:max-height .3s ease;">
                <a href="/requetes/create" class="submenu-link">Nouvelle requête</a>
                <a href="/requetes" class="submenu-link">Liste des requêtes</a>
                <a href="/suivi-validation" class="submenu-link">Suivi & Validation</a>
            </div>
        </div>

        <!-- Informations du service -->
        <div class="menu-section">
            <button class="menu-parent toggle" data-target="#sm-info" style="width:100%;display:flex;align-items:center;justify-content:space-between;padding:14px 20px;background:transparent;border:none;color:#166534;font-weight:600;border-bottom:1px solid #dcfce7;cursor:pointer;">
                <div style="display:flex;align-items:center;">
                    <i class="fas fa-info-circle" style="width:20px;margin-right:12px;color:#16a34a;"></i>
                    <span>Informations</span>
                </div>
                <i class="fas fa-chevron-right" style="transition:transform .3s;"></i>
            </button>
            <div id="sm-info" class="submenu" style="max-height:0;overflow:hidden;transition:max-height .3s ease;">
                <div style="padding: 10px 20px; color: #6b7280; font-size: 0.875rem;">
                    <div style="margin-bottom: 8px;">
                        <strong>Service:</strong> {{ session('authenticated_service')->nom ?? 'Non défini' }}
                    </div>
                    <div style="margin-bottom: 8px;">
                        <strong>Email:</strong> {{ session('authenticated_service')->email ?? 'Non défini' }}
                    </div>
                    @if(session('authenticated_service')->phone)
                    <div style="margin-bottom: 8px;">
                        <strong>Téléphone:</strong> {{ session('authenticated_service')->phone }}
                    </div>
                    @endif
                    <div>
                        <strong>Statut:</strong>
                        @if(session('authenticated_service')->actif)
                            <span class="badge bg-success">Actif</span>
                        @else
                            <span class="badge bg-secondary">Inactif</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<style>
.menu-section {
    margin-bottom: 8px;
}

.menu-parent {
    transition: all 0.3s ease;
}

.menu-parent:hover {
    background: rgba(22, 101, 52, 0.1);
    border-left-color: #16a34a;
}

.menu-parent.menu-active {
    background: rgba(22, 101, 52, 0.1);
    border-left-color: #16a34a;
}

.submenu {
    background: rgba(255, 255, 255, 0.5);
}

.submenu-link {
    display: block;
    padding: 10px 20px 10px 52px;
    color: #6b7280;
    text-decoration: none;
    font-size: 0.875rem;
    transition: all 0.3s ease;
}

.submenu-link:hover {
    background: rgba(22, 101, 52, 0.05);
    color: #166534;
}

.submenu-link.active {
    background: rgba(22, 101, 52, 0.1);
    color: #166534;
    font-weight: 500;
}

.submenu.open {
    max-height: 500px !important;
}

.toggle .fa-chevron-right {
    transition: transform 0.3s ease;
}

.toggle.menu-active .fa-chevron-right {
    transform: rotate(90deg);
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Gestion des menus déroulants
    const toggles = document.querySelectorAll('.toggle');

    toggles.forEach(toggle => {
        toggle.addEventListener('click', function() {
            const targetId = this.dataset.target;
            const target = document.getElementById(targetId);
            const chevron = this.querySelector('.fa-chevron-right');

            if (target) {
                target.classList.toggle('open');
                this.classList.toggle('menu-active');

                if (chevron) {
                    chevron.style.transform = target.classList.contains('open') ? 'rotate(90deg)' : 'rotate(0deg)';
                }
            }
        });
    });

    // Ouvrir automatiquement le menu de la page actuelle
    const currentPath = window.location.pathname;
    if (currentPath.includes('/requetes')) {
        const requetesMenu = document.getElementById('sm-requetes');
        const requetesToggle = document.querySelector('[data-target="#sm-requetes"]');
        if (requetesMenu && requetesToggle) {
            requetesMenu.classList.add('open');
            requetesToggle.classList.add('menu-active');
            requetesToggle.querySelector('.fa-chevron-right').style.transform = 'rotate(90deg)';
        }
    }
});
</script>

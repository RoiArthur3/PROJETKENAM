<!-- Sidebar Moderne KENAM - Ordre Logique -->
<div id="app-sidebar" class="sidebar-desktop">
    <!-- Header -->
    <div class="sidebar-header">
        @php
            $sidebarHomeUrl = auth()->check() && auth()->user()
                ? auth()->user()->getFirstAccessibleModuleUrl()
                : '/operations/dashboard';
        @endphp
        <a href="{{ $sidebarHomeUrl }}">
            <img src="{{ asset('images/logo-kenam.png') }}" alt="KENAM SERVICES" class="sidebar-logo">
        </a>
    </div>

    <!-- Menu -->
    <div class="sidebar-menu">
        <!-- 1. Dashboard -->
        @if(auth()->check() && auth()->user() && auth()->user()->canAccessModule('dashboard'))
        <a href="{{ route('dashboard') }}" class="menu-item active" data-tooltip="Tableau de bord">
            <div class="menu-icon">
                <i class="fas fa-tachometer-alt"></i>
            </div>
            <span class="menu-text">Tableau de bord</span>
        </a>
        @endif

        <!-- 2. Opérations (Cœur du métier) -->
        @if(auth()->check() && auth()->user() && auth()->user()->canAccessModule('operations'))
        <div class="menu-item has-submenu" data-tooltip="Opérations" data-module="operations">
            <div class="menu-icon">
                <i class="fas fa-cogs"></i>
            </div>
            <span class="menu-text">Opérations</span>
            <div class="submenu-arrow">
                <i class="fas fa-chevron-right"></i>
            </div>
        </div>

        <!-- Sous-modules Opérations -->
        <div class="floating-submenu" id="submenu-operations">
            <a href="/operations" class="submenu-link">
                <i class="fas fa-list"></i>
                <span>Liste des opérations</span>
            </a>
            <a href="/operations/create" class="submenu-link">
                <i class="fas fa-plus"></i>
                <span>Nouvelle opération</span>
            </a>
            <a href="/operations/dashboard" class="submenu-link">
                <i class="fas fa-chart-line"></i>
                <span>Dashboard</span>
            </a>
        </div>
        @endif

        <!-- 3. Suivi et validations -->
        @if(auth()->check() && auth()->user() && auth()->user()->canAccessModule('validations'))
        <div class="menu-item has-submenu" data-tooltip="Suivi et validations" data-module="validations">
            <div class="menu-icon">
                <i class="fas fa-clipboard-check"></i>
            </div>
            <span class="menu-text">Suivi et validations</span>
            <div class="submenu-arrow">
                <i class="fas fa-chevron-right"></i>
            </div>
        </div>

        <!-- Sous-modules Validations -->
        <div class="floating-submenu" id="submenu-validations">
            <a href="/validations/pending" class="submenu-link">
                <i class="fas fa-clock"></i>
                <span>En attente</span>
            </a>
            <a href="/validations/approved" class="submenu-link">
                <i class="fas fa-check"></i>
                <span>Approuvées</span>
            </a>
            <a href="/validations/rejected" class="submenu-link">
                <i class="fas fa-times"></i>
                <span>Rejetées</span>
            </a>
        </div>
        @endif

        <!-- 4. Parc auto (Logistique) -->
        @if(auth()->check() && auth()->user() && auth()->user()->canAccessModule('fleet'))
        <div class="menu-item has-submenu" data-tooltip="Parc auto" data-module="fleet">
            <div class="menu-icon">
                <i class="fas fa-truck"></i>
            </div>
            <span class="menu-text">Parc auto</span>
            <div class="submenu-arrow">
                <i class="fas fa-chevron-right"></i>
            </div>
        </div>

        <!-- Sous-modules Parc auto -->
        <div class="floating-submenu" id="submenu-fleet">
            <a href="{{ route('materiel.cost-control.engin.list') }}" class="submenu-link">
                <i class="fas fa-tachometer-alt"></i>
                <span>Liste des pointages</span>
            </a>
            <a href="/fleet/vehicules" class="submenu-link">
                <i class="fas fa-truck"></i>
                <span>Véhicules</span>
            </a>
            <a href="/fleet/maintenance" class="submenu-link">
                <i class="fas fa-wrench"></i>
                <span>Maintenance</span>
            </a>
        </div>
        @endif

        <!-- 5. Entrepôt (Stock) -->
        @if(auth()->check() && auth()->user() && auth()->user()->canAccessModule('warehouse'))
        <div class="menu-item has-submenu" data-tooltip="Entrepôt" data-module="warehouse">
            <div class="menu-icon">
                <i class="fas fa-warehouse"></i>
            </div>
            <span class="menu-text">Entrepôt</span>
            <div class="submenu-arrow">
                <i class="fas fa-chevron-right"></i>
            </div>
        </div>

        <!-- Sous-modules Entrepôt -->
        <div class="floating-submenu" id="submenu-warehouse">
            <a href="/warehouse/dashboard" class="submenu-link">
                <i class="fas fa-tachometer-alt"></i>
                <span>Dashboard</span>
            </a>
            <a href="/warehouse/stock" class="submenu-link">
                <i class="fas fa-boxes"></i>
                <span>Stock</span>
            </a>
            <a href="/warehouse/entrees" class="submenu-link">
                <i class="fas fa-sign-in-alt"></i>
                <span>Entrées</span>
            </a>
            <a href="/warehouse/sorties" class="submenu-link">
                <i class="fas fa-sign-out-alt"></i>
                <span>Sorties</span>
            </a>
        </div>
        @endif

        <!-- 6. Fournisseurs -->
        @if(auth()->check() && auth()->user() && auth()->user()->canAccessModule('achat'))
        <div class="menu-item has-submenu" data-tooltip="Achat" data-module="achat">
            <div class="menu-icon">
                <i class="fas fa-shopping-cart"></i>
            </div>
            <span class="menu-text">Achat</span>
            <div class="submenu-arrow">
                <i class="fas fa-chevron-right"></i>
            </div>
        </div>

        <!-- Sous-modules Fournisseurs -->
        <div class="floating-submenu" id="submenu-achat">
            <a href="{{ route('achat.index') }}" class="submenu-link">
                <i class="fas fa-tachometer-alt"></i>
                <span>Liste des achats</span>
            </a>
            <a href="{{ route('achat.create') }}" class="submenu-link">
                <i class="fas fa-plus"></i>
                <span>Nouvel achat</span>
            </a>
            <a href="/fournisseurs/list" class="submenu-link">
                <i class="fas fa-industry"></i>
                <span>Liste fournisseurs</span>
            </a>
        </div>
        @endif

        <!-- 7. RH -->
        @if(auth()->check() && auth()->user() && auth()->user()->canAccessModule('rh'))
        <div class="menu-item has-submenu" data-tooltip="RH" data-module="rh">
            <div class="menu-icon">
                <i class="fas fa-users-cog"></i>
            </div>
            <span class="menu-text">RH</span>
            <div class="submenu-arrow">
                <i class="fas fa-chevron-right"></i>
            </div>
        </div>

        <!-- Sous-modules RH -->
        <div class="floating-submenu" id="submenu-rh">
            <a href="/rh/dashboard" class="submenu-link">
                <i class="fas fa-tachometer-alt"></i>
                <span>Tableau de bord</span>
            </a>
            <a href="/rh/agents" class="submenu-link">
                <i class="fas fa-users"></i>
                <span>Agents</span>
            </a>
            <a href="/rh/conges" class="submenu-link">
                <i class="fas fa-calendar-alt"></i>
                <span>Congés</span>
            </a>
        </div>
        @endif

        <!-- 8. Commercial -->
        @if(auth()->check() && auth()->user() && auth()->user()->canAccessModule('commercial'))
        <div class="menu-item has-submenu" data-tooltip="Commercial" data-module="commercial">
            <div class="menu-icon">
                <i class="fas fa-briefcase"></i>
            </div>
            <span class="menu-text">Commercial</span>
            <div class="submenu-arrow">
                <i class="fas fa-chevron-right"></i>
            </div>
        </div>

        <!-- Sous-modules Commercial -->
        <div class="floating-submenu" id="submenu-commercial">
            <a href="/commercial/dashboard" class="submenu-link">
                <i class="fas fa-tachometer-alt"></i>
                <span>Dashboard</span>
            </a>
            <a href="/commercial/commandes" class="submenu-link">
                <i class="fas fa-clipboard-list"></i>
                <span>Commandes</span>
            </a>
            <a href="/commercial/clients" class="submenu-link">
                <i class="fas fa-users"></i>
                <span>Clients</span>
            </a>
            <a href="/commercial/prospects" class="submenu-link">
                <i class="fas fa-user-plus"></i>
                <span>Prospects</span>
            </a>
            <a href="/commercial/devis" class="submenu-link">
                <i class="fas fa-file-invoice"></i>
                <span>Devis</span>
            </a>
        </div>
        @endif

        <!-- 9. Comptabilité -->
        @if(auth()->check() && auth()->user() && auth()->user()->canAccessModule('comptabilite'))
        <div class="menu-item has-submenu" data-tooltip="Comptabilité" data-module="comptabilite">
            <div class="menu-icon">
                <i class="fas fa-calculator"></i>
            </div>
            <span class="menu-text">Comptabilité</span>
            <div class="submenu-arrow">
                <i class="fas fa-chevron-right"></i>
            </div>
        </div>

        <!-- Sous-modules Comptabilité -->
        <div class="floating-submenu" id="submenu-comptabilite">
            <a href="{{ route('comptabilite.dashboard') }}" class="submenu-link">
                <i class="fas fa-tachometer-alt"></i>
                <span>Dashboard</span>
            </a>
            <a href="{{ route('comptabilite.factures.index') }}" class="submenu-link">
                <i class="fas fa-file-invoice"></i>
                <span>Factures fournisseurs</span>
            </a>
            <a href="{{ route('comptabilite.depenses.index') }}" class="submenu-link">
                <i class="fas fa-money-bill-wave"></i>
                <span>Dépenses</span>
            </a>
            <a href="{{ route('comptabilite.recettes.index') }}" class="submenu-link">
                <i class="fas fa-hand-holding-usd"></i>
                <span>Recettes</span>
            </a>
        </div>
        @endif

        <!-- 10. Trésorerie -->
        @if(auth()->check() && auth()->user() && auth()->user()->canAccessModule('tresorerie'))
        <div class="menu-item has-submenu" data-tooltip="Trésorerie" data-module="tresorerie">
            <div class="menu-icon">
                <i class="fas fa-coins"></i>
            </div>
            <span class="menu-text">Trésorerie</span>
            <div class="submenu-arrow">
                <i class="fas fa-chevron-right"></i>
            </div>
        </div>

        <!-- Sous-modules Trésorerie -->
        <div class="floating-submenu" id="submenu-tresorerie">
            <a href="{{ route('tresorerie.dashboard') }}" class="submenu-link">
                <i class="fas fa-tachometer-alt"></i>
                <span>Dashboard Trésorerie</span>
            </a>
            <a href="{{ route('tresorerie.caisses.index') }}" class="submenu-link">
                <i class="fas fa-cash-register"></i>
                <span>Caisses</span>
            </a>
            <a href="{{ route('tresorerie.approvisionnement-demandes.index') }}" class="submenu-link">
                <i class="fas fa-file-invoice-dollar"></i>
                <span>Demandes d'approvisionnement</span>
            </a>
            <a href="{{ route('validations.to-pay') }}" class="submenu-link">
                <i class="fas fa-stamp"></i>
                <span>BON POUR ACCORD</span>
            </a>
            <a href="{{ route('tresorerie.banque') }}" class="submenu-link">
                <i class="fas fa-university"></i>
                <span>Banque</span>
            </a>
            <a href="{{ route('tresorerie.avances') }}" class="submenu-link">
                <i class="fas fa-hand-holding-usd"></i>
                <span>Acomptes</span>
            </a>
            <a href="{{ route('tresorerie.depenses.index') }}" class="submenu-link">
                <i class="fas fa-money-bill-wave"></i>
                <span>Dépenses</span>
            </a>
        </div>
        @endif

        <!-- 11. Reporting -->
        @if(auth()->check() && auth()->user() && auth()->user()->canAccessModule('reporting'))
        <div class="menu-item has-submenu" data-tooltip="Reporting" data-module="reporting">
            <div class="menu-icon">
                <i class="fas fa-chart-line"></i>
            </div>
            <span class="menu-text">Reporting</span>
            <div class="submenu-arrow">
                <i class="fas fa-chevron-right"></i>
            </div>
        </div>

        <!-- Sous-modules Reporting -->
        <div class="floating-submenu" id="submenu-reporting">
            <a href="/reporting/dashboard" class="submenu-link">
                <i class="fas fa-tachometer-alt"></i>
                <span>Dashboard</span>
            </a>
            <a href="/reporting/financier" class="submenu-link">
                <i class="fas fa-dollar-sign"></i>
                <span>Financier</span>
            </a>
            <a href="/reporting/operations" class="submenu-link">
                <i class="fas fa-cogs"></i>
                <span>Opérations</span>
            </a>
        </div>
        @endif

        <!-- Footer du menu -->
        <div class="menu-footer">
            <!-- Footer vide -->
        </div>
    </div>
</div>

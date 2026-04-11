<style>
    #app-sidebar .floating-submenu {
        display: grid !important;
        grid-template-columns: repeat(4, minmax(180px, 1fr)) !important;
        gap: 10px !important;
        padding: 10px !important;
        align-items: stretch;
    }

    #app-sidebar .floating-submenu .submenu-link {
        display: flex !important;
        align-items: center !important;
        gap: 10px !important;
        min-height: 48px;
        padding: 10px 12px !important;
        text-decoration: none !important;
        color: #e9fff2 !important;
        background: #0b3d2e !important;
        border: 1px solid #145a32;
        font-size: 14px !important;
        line-height: 1.25;
        white-space: normal !important;
        border-radius: 6px;
        transition: all 0.2s ease;
    }

    #app-sidebar .floating-submenu .submenu-link:hover {
        background: #145a32 !important;
        color: #ffffff !important;
        border-color: #1b6e3f;
    }

    #app-sidebar .floating-submenu .submenu-link i {
        width: 18px !important;
        font-size: 14px !important;
        text-align: center;
        flex-shrink: 0;
    }

    #app-sidebar .floating-submenu .submenu-link span {
        font-size: 14px !important;
        font-weight: 500 !important;
    }

    #app-sidebar .floating-submenu .submenu-divider {
        grid-column: 1 / -1;
        width: 100%;
    }

    #app-sidebar .floating-submenu .submenu-title {
        grid-column: 1 / -1;
        font-size: 12px;
        font-weight: 700;
        letter-spacing: 0.4px;
        text-transform: uppercase;
        color: #6c757d;
        margin: 4px 0 0;
    }

    #app-sidebar .menu-group-title {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 10px 12px 6px;
        margin-top: 6px;
        color: #6c757d;
        font-size: 12px;
        font-weight: 800;
        letter-spacing: 0.5px;
        text-transform: uppercase;
        border-top: 1px solid #eef1f4;
    }

    @media (max-width: 1600px) {
        #app-sidebar .floating-submenu {
            grid-template-columns: repeat(4, minmax(180px, 1fr)) !important;
        }
    }

    @media (max-width: 1200px) {
        #app-sidebar .floating-submenu {
            grid-template-columns: repeat(2, minmax(170px, 1fr)) !important;
        }
    }

    @media (max-width: 768px) {
        #app-sidebar .floating-submenu {
            grid-template-columns: 1fr !important;
        }

        #app-sidebar .floating-submenu .submenu-link,
        #app-sidebar .floating-submenu .submenu-link span {
            font-size: 15px !important;
        }
    }
</style>

<!-- Sidebar Moderne KENAM -->
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
        @php
            // Vue unifiée: admin et modérateur utilisent le même affichage métier que superadmin.
            $useGroupedByMetierView = true;
        @endphp

        <!-- 1. Dashboard -->
        @if(auth()->check() && auth()->user() && auth()->user()->canAccessModule('dashboard'))
        <a href="{{ route('dashboard') }}" class="menu-item active" data-tooltip="Tableau de bord">
            <div class="menu-icon">
                <i class="fas fa-tachometer-alt"></i>
            </div>
            <span class="menu-text">Tableau de bord</span>
        </a>
        @endif

        @if(auth()->check() && auth()->user() && in_array(auth()->user()->role, ['admin', 'superadmin'], true) && Route::has('imports.historique.index'))
        <a href="{{ route('imports.historique.index') }}" class="menu-item" data-tooltip="Import Historique Excel">
            <div class="menu-icon">
                <i class="fas fa-file-import"></i>
            </div>
            <span class="menu-text">Import Historique</span>
        </a>
        @endif

        @if($useGroupedByMetierView)
        <!-- Regroupement par métier pour tous les profils -->
        <div class="menu-group-title">
            <i class="fas fa-sitemap"></i>
            <span>Direction Générale</span>
        </div>

        <div class="menu-item has-submenu" data-tooltip="Reporting" data-module="dg-reporting">
            <div class="menu-icon"><i class="fas fa-chart-line"></i></div>
            <span class="menu-text">Reporting</span>
            <div class="submenu-arrow"><i class="fas fa-chevron-right"></i></div>
        </div>
        <div class="floating-submenu" id="submenu-dg-reporting">
            <a href="/reporting/dashboard" class="submenu-link"><i class="fas fa-tachometer-alt"></i><span>Dashboard Reporting</span></a>
            <a href="/reporting/financier" class="submenu-link"><i class="fas fa-dollar-sign"></i><span>Financier</span></a>
            <a href="/reporting/operations" class="submenu-link"><i class="fas fa-cogs"></i><span>Reporting Opérations</span></a>
            <a href="/reporting/performance" class="submenu-link"><i class="fas fa-gauge-high"></i><span>Performance</span></a>
        </div>

        @if(auth()->user()->canAccessModule('validations'))
        @if(auth()->user()->canAccessModule('operations'))
        <a href="{{ route('operations.create') }}" class="menu-item" data-tooltip="Nouvelle opération">
            <div class="menu-icon"><i class="fas fa-plus-circle"></i></div>
            <span class="menu-text">Nouvelle opération</span>
        </a>
        @endif
        <div class="menu-item has-submenu" data-tooltip="Suivi et validations" data-module="dg-validations">
            <div class="menu-icon"><i class="fas fa-tasks"></i></div>
            <span class="menu-text">Suivi et validations</span>
            <div class="submenu-arrow"><i class="fas fa-chevron-right"></i></div>
        </div>
        <div class="floating-submenu" id="submenu-dg-validations">
            @php
                $currentRole = \Illuminate\Support\Str::lower((string) auth()->user()->role);
                $strictValidationSubmoduleCheck = in_array($currentRole, ['admin', 'moderator', 'moderateur'], true);

                $validationLinks = [
                    ['key' => 'validations_pending', 'label' => 'En attente', 'icon' => 'fas fa-clock', 'url' => '/validations/pending'],
                    ['key' => 'validations_approved', 'label' => 'Approuvées', 'icon' => 'fas fa-check', 'url' => '/validations/approved'],
                    ['key' => 'validations_rejected', 'label' => 'Rejetées', 'icon' => 'fas fa-times', 'url' => '/validations/rejected'],
                    ['key' => 'validations_history', 'label' => 'Historique', 'icon' => 'fas fa-history', 'route' => 'validations.history'],
                    ['key' => 'validations_to_pay', 'label' => 'BON POUR ACCORD', 'icon' => 'fas fa-stamp', 'route' => 'validations.to-pay'],
                    ['key' => 'validations_caisse_execution', 'label' => 'Bon pour exécution', 'icon' => 'fas fa-cash-register', 'route' => 'validations.caisse-execution'],
                    ['key' => 'validations_paid', 'label' => 'Payées', 'icon' => 'fas fa-coins', 'route' => 'validations.paid'],
                ];
            @endphp

            @foreach($validationLinks as $validationLink)
                @php
                    $canSeeLink = !$strictValidationSubmoduleCheck || auth()->user()->canAccessSubmodule($validationLink['key']);
                    $hasRoute = isset($validationLink['route']) ? Route::has($validationLink['route']) : true;
                    $href = '#';

                    if ($canSeeLink && $hasRoute) {
                        $href = isset($validationLink['route'])
                            ? route($validationLink['route'])
                            : url($validationLink['url']);
                    }
                @endphp

                @if($canSeeLink && $hasRoute)
                <a href="{{ $href }}" class="submenu-link"><i class="{{ $validationLink['icon'] }}"></i><span>{{ $validationLink['label'] }}</span></a>
                @endif
            @endforeach
        </div>
        @endif

        @if(auth()->user()->canAccessModule('juridique'))
        <div class="menu-item has-submenu" data-tooltip="Juridique" data-module="dg-juridique">
            <div class="menu-icon"><i class="fas fa-gavel"></i></div>
            <span class="menu-text">Juridique</span>
            <div class="submenu-arrow"><i class="fas fa-chevron-right"></i></div>
        </div>
        <div class="floating-submenu" id="submenu-dg-juridique">
            @if(Route::has('juridique.dashboard'))
            <a href="{{ route('juridique.dashboard') }}" class="submenu-link"><i class="fas fa-tachometer-alt"></i><span>Dashboard Juridique</span></a>
            @else
            <a href="/juridique" class="submenu-link"><i class="fas fa-gavel"></i><span>Juridique</span></a>
            @endif
            @if(Route::has('juridique.contrats.index'))
            <a href="{{ route('juridique.contrats.index') }}" class="submenu-link"><i class="fas fa-file-contract"></i><span>Contrats</span></a>
            @endif
            @if(Route::has('juridique.documents.index'))
            <a href="{{ route('juridique.documents.index') }}" class="submenu-link"><i class="fas fa-folder-open"></i><span>Documents</span></a>
            @endif
            @if(Route::has('juridique.echeances.index'))
            <a href="{{ route('juridique.echeances.index') }}" class="submenu-link"><i class="fas fa-calendar-alt"></i><span>Échéances</span></a>
            @endif
        </div>
        @endif

        @if(auth()->user()->canAccessModule('comptabilite'))
        <div class="menu-item has-submenu" data-tooltip="Comptabilité" data-module="dg-comptabilite">
            <div class="menu-icon"><i class="fas fa-calculator"></i></div>
            <span class="menu-text">Comptabilité</span>
            <div class="submenu-arrow"><i class="fas fa-chevron-right"></i></div>
        </div>
        <div class="floating-submenu" id="submenu-dg-comptabilite">
            <a href="{{ route('comptabilite.dashboard') }}" class="submenu-link"><i class="fas fa-tachometer-alt"></i><span>Dashboard Compta</span></a>
            <a href="{{ route('comptabilite.factures.index') }}" class="submenu-link"><i class="fas fa-file-invoice"></i><span>Factures</span></a>
            <a href="{{ route('comptabilite.depenses.index') }}" class="submenu-link"><i class="fas fa-money-bill-wave"></i><span>Dépenses</span></a>
            <a href="{{ route('comptabilite.recettes.index') }}" class="submenu-link"><i class="fas fa-hand-holding-usd"></i><span>Recettes</span></a>
            <a href="{{ route('comptabilite.rapports.grand-journal') }}" class="submenu-link"><i class="fas fa-book-open"></i><span>Grand Journal</span></a>
            <a href="{{ route('comptabilite.rapports.bilan') }}" class="submenu-link"><i class="fas fa-balance-scale"></i><span>Bilan</span></a>
            <a href="{{ route('comptabilite.rapports.compte-resultat') }}" class="submenu-link"><i class="fas fa-chart-line"></i><span>Compte de Résultat</span></a>
            <a href="{{ route('comptabilite.approvisionnement-demandes.pending') }}" class="submenu-link"><i class="fas fa-clipboard-check"></i><span>Demandes d'approvisionnement</span></a>
        </div>
        @endif

        @if(auth()->user()->canAccessModule('tresorerie'))
        <div class="menu-item has-submenu" data-tooltip="Trésorerie" data-module="dg-tresorerie">
            <div class="menu-icon"><i class="fas fa-coins"></i></div>
            <span class="menu-text">Trésorerie</span>
            <div class="submenu-arrow"><i class="fas fa-chevron-right"></i></div>
        </div>
        <div class="floating-submenu" id="submenu-dg-tresorerie">
            <a href="{{ route('tresorerie.dashboard') }}" class="submenu-link"><i class="fas fa-tachometer-alt"></i><span>Dashboard Trésorerie</span></a>
            @php $tresorerieSubmodules = config('submodules.tresorerie.submodules', []); @endphp
            @foreach($tresorerieSubmodules as $key => $submodule)
                @if(in_array($key, ['tres_caisses', 'tres_appro', 'tres_appro_dem', 'tres_bon_accord', 'tres_encaisse', 'tres_decaisse', 'tres_a_payer', 'tres_soldes']))
                    <a href="{{ route($submodule['route']) }}" class="submenu-link"><i class="{{ $submodule['icon'] }}"></i><span>{{ $submodule['name'] }}</span></a>
                @endif
            @endforeach
            <a href="{{ route('tresorerie.banque') }}" class="submenu-link"><i class="fas fa-university"></i><span>Banque</span></a>
            <a href="{{ route('tresorerie.avances') }}" class="submenu-link"><i class="fas fa-hand-holding-usd"></i><span>Acomptes</span></a>
        </div>
        @endif

        @php
            $canAccessOperationsModule = auth()->user()->canAccessModule('operations');
            $canAccessCommercialModule = auth()->user()->canAccessModule('commercial');
            $canAccessFournisseursModule = auth()->user()->canAccessModule('achat');
            $hasCommerceOpsGroup = $canAccessOperationsModule || $canAccessCommercialModule || $canAccessFournisseursModule;
            $commerceOpsLabel = $canAccessOperationsModule
                ? (($canAccessCommercialModule || $canAccessFournisseursModule) ? 'Commerce & Opérations' : 'Opérations')
                : 'Commerce';
            $commerceOpsIcon = $canAccessOperationsModule && !($canAccessCommercialModule || $canAccessFournisseursModule)
                ? 'fas fa-cogs'
                : 'fas fa-briefcase';
        @endphp
        @if($hasCommerceOpsGroup)
        <div class="menu-item has-submenu" data-tooltip="{{ $commerceOpsLabel }}" data-module="commerce_ops">
            <div class="menu-icon">
                <i class="{{ $commerceOpsIcon }}"></i>
            </div>
            <span class="menu-text">{{ $commerceOpsLabel }}</span>
            <div class="submenu-arrow">
                <i class="fas fa-chevron-right"></i>
            </div>
        </div>
        <div class="floating-submenu" id="submenu-commerce_ops">
            @if($canAccessOperationsModule)
            <div class="submenu-title">Opérations</div>
            <a href="{{ route('operations.index') }}" class="submenu-link"><i class="fas fa-list"></i><span>Liste des opérations</span></a>
            @endif

            @if($canAccessCommercialModule)
            <div class="submenu-title">Commercial</div>
            <a href="/commercial/dashboard" class="submenu-link"><i class="fas fa-tachometer-alt"></i><span>Dashboard</span></a>
            <a href="/commercial/clients" class="submenu-link"><i class="fas fa-users"></i><span>Clients</span></a>
            <a href="/commercial/devis" class="submenu-link"><i class="fas fa-file-invoice"></i><span>Devis</span></a>
            <a href="/commercial/bon-livraison" class="submenu-link"><i class="fas fa-truck"></i><span>Bons de Livraison</span></a>
            @endif
        @endif

        @if($canAccessFournisseursModule)
        <div class="menu-item has-submenu" data-tooltip="Achat" data-module="achat">
            <div class="menu-icon">
                <i class="fas fa-shopping-cart"></i>
            </div>
            <span class="menu-text">Achat</span>
            <div class="submenu-arrow">
                <i class="fas fa-chevron-right"></i>
            </div>
        </div>
        <div class="floating-submenu" id="submenu-achat">
            <a href="{{ route('achat.index') }}" class="submenu-link"><i class="fas fa-tachometer-alt"></i><span>Liste des achats</span></a>
            <a href="{{ route('achat.create') }}" class="submenu-link"><i class="fas fa-plus"></i><span>Nouvel achat</span></a>
            <a href="/fournisseurs/list" class="submenu-link"><i class="fas fa-industry"></i><span>Fournisseurs</span></a>
        </div>
        @endif

        @php
            $isAdminRole = auth()->user()->hasRole('admin');
            $isModeratorRole = auth()->user()->hasRole('moderator') || auth()->user()->hasRole('moderateur');
            $canAccessMaterielModule = auth()->user()->canAccessModule('materiel');
            $canAccessCostControlModule = auth()->user()->canAccessModule('cost_control');
            $canShowAssurances = $isAdminRole
                ? $canAccessMaterielModule
                : auth()->user()->canAccessSubmodule('materiel_assurances');
            $canShowVisitesTechniques = $isAdminRole
                ? $canAccessMaterielModule
                : auth()->user()->canAccessSubmodule('materiel_visites_techniques');
            $canShowListePointages = $isAdminRole
                ? ($canAccessMaterielModule || $canAccessCostControlModule)
                : auth()->user()->canAccessSubmodule('cost_control_list_pointages');
            $hasLogistiqueGroup = auth()->user()->canAccessModule('materiel')
                || auth()->user()->canAccessModule('cost_control')
                || auth()->user()->canAccessModule('warehouse')
                || auth()->user()->canAccessModule('magasin')
                || auth()->user()->canAccessModule('entrepots')
                || auth()->user()->canAccessModule('achat');
        @endphp
        @if($hasLogistiqueGroup)
        <div class="menu-group-title">
            <i class="fas fa-tools"></i>
            <span>ATELIER</span>
        </div>

        @if($canAccessMaterielModule || $canAccessCostControlModule || $canShowAssurances || $canShowVisitesTechniques || $canShowListePointages)
        <div class="menu-item has-submenu" data-tooltip="ATELIER" data-module="logistique-atelier">
            <div class="menu-icon"><i class="fas fa-tools"></i></div>
            <span class="menu-text">ATELIER</span>
            <div class="submenu-arrow"><i class="fas fa-chevron-right"></i></div>
        </div>
        <div class="floating-submenu" id="submenu-logistique-atelier">
            <a href="{{ route('materiel.cost-control.home') }}" class="submenu-link"><i class="fas fa-tachometer-alt"></i><span>Dashboard Atelier</span></a>
            <a href="/fournisseurs/commandes" class="submenu-link"><i class="fas fa-shopping-cart"></i><span>Achats Atelier</span></a>
            @if($canShowListePointages)
            <a href="{{ route('materiel.cost-control.engin.list') }}" class="submenu-link"><i class="fas fa-list"></i><span>Liste de Pointage</span></a>
            @endif
            @if($canShowAssurances)
            <a href="{{ route('materiel.assurances.index') }}" class="submenu-link"><i class="fas fa-shield-alt"></i><span>Assurances</span></a>
            @endif
            @if($canShowVisitesTechniques)
            <a href="{{ route('materiel.visites.index') }}" class="submenu-link"><i class="fas fa-clipboard-check"></i><span>Visites techniques</span></a>
            @endif
            <a href="/fournisseurs/list" class="submenu-link"><i class="fas fa-truck"></i><span>Fournisseurs</span></a>
            <a href="/materiel/vehicules" class="submenu-link"><i class="fas fa-truck"></i><span>Engins</span></a>
            <a href="/materiel/carburant" class="submenu-link"><i class="fas fa-gas-pump"></i><span>Carburant</span></a>
            <a href="{{ route('materiel.cost-control.home') }}" class="submenu-link"><i class="fas fa-stopwatch"></i><span>Cost Control (Pointage engin)</span></a>
            {{-- Bouton Camion Plateau supprimé --}}
            <a href="/materiel/maintenance" class="submenu-link"><i class="fas fa-wrench"></i><span>Maintenance</span></a>
            <a href="{{ route('fleet.affectations.index') }}" class="submenu-link"><i class="fas fa-user-tag"></i><span>Affectations</span></a>
            <a href="{{ route('materiel.missions.index') }}" class="submenu-link"><i class="fas fa-map-location-dot"></i><span>Liste des missions</span></a>
            <a href="{{ route('materiel.missions.create') }}" class="submenu-link"><i class="fas fa-plus-circle"></i><span>Nouvelle mission</span></a>
            <a href="{{ route('materiel.missions.export') }}" class="submenu-link"><i class="fas fa-file-export"></i><span>Exporter missions</span></a>
        </div>
        @endif


        @if(auth()->user()->canAccessModule('magasin'))
        <div class="menu-item has-submenu" data-tooltip="Magasin" data-module="logistique-magasin">
            <div class="menu-icon"><i class="fas fa-store"></i></div>
            <span class="menu-text">Magasin</span>
            <div class="submenu-arrow"><i class="fas fa-chevron-right"></i></div>
        </div>
        <div class="floating-submenu" id="submenu-logistique-magasin">
            <a href="{{ route('shop') }}" class="submenu-link"><i class="fas fa-shopping-cart"></i><span>Boutique</span></a>
            <a href="/magasin/dashboard" class="submenu-link"><i class="fas fa-tachometer-alt"></i><span>Dashboard</span></a>
            <a href="/magasin/inventaire" class="submenu-link"><i class="fas fa-boxes"></i><span>Inventaire</span></a>
            <a href="/magasin/entrees" class="submenu-link"><i class="fas fa-sign-in-alt"></i><span>Entrées</span></a>
            <a href="/magasin/sorties" class="submenu-link"><i class="fas fa-sign-out-alt"></i><span>Sorties</span></a>
            <a href="/magasin/rapports" class="submenu-link"><i class="fas fa-chart-bar"></i><span>Rapports</span></a>
        </div>
        @endif

        @if(auth()->user()->canAccessModule('entrepots'))
        <div class="menu-item has-submenu" data-tooltip="Entrepôts" data-module="logistique-entrepots">
            <div class="menu-icon"><i class="fas fa-building"></i></div>
            <span class="menu-text">Entrepôts</span>
            <div class="submenu-arrow"><i class="fas fa-chevron-right"></i></div>
        </div>
        <div class="floating-submenu" id="submenu-logistique-entrepots">
            <a href="/entrepots/dashboard" class="submenu-link"><i class="fas fa-tachometer-alt"></i><span>Dashboard</span></a>
            <a href="/entrepots/list" class="submenu-link"><i class="fas fa-list"></i><span>Liste</span></a>
            <a href="/entrepots/stock" class="submenu-link"><i class="fas fa-boxes"></i><span>Stock</span></a>
            <a href="/entrepots/transferts" class="submenu-link"><i class="fas fa-exchange-alt"></i><span>Transferts</span></a>
            <a href="/entrepots/rapports" class="submenu-link"><i class="fas fa-chart-bar"></i><span>Rapports</span></a>
        </div>
        @endif

        @endif

        <div class="menu-item has-submenu" data-tooltip="Support & Contrôle" data-module="support_controle">
            <div class="menu-icon">
                <i class="fas fa-shield-alt"></i>
            </div>
            <span class="menu-text">Support & Contrôle</span>
            <div class="submenu-arrow">
                <i class="fas fa-chevron-right"></i>
            </div>
        </div>
        <div class="floating-submenu" id="submenu-support_controle">
            @if(auth()->user()->canAccessModule('rh'))
            <div class="submenu-title">Ressources Humaines</div>
            <a href="/rh/dashboard" class="submenu-link"><i class="fas fa-users-cog"></i><span>Dashboard RH</span></a>
            <a href="{{ route('rh.personnel.index') }}" class="submenu-link"><i class="fas fa-users"></i><span>Personnel</span></a>
            <a href="{{ route('rh.paie.index') }}" class="submenu-link"><i class="fas fa-money-bill"></i><span>Paie</span></a>
            @endif

        </div>

        @if(auth()->user()->canAccessModule('projects'))
        <div class="menu-item has-submenu" data-tooltip="Gestion de Projet" data-module="gestion-projet">
            <div class="menu-icon"><i class="fas fa-hard-hat"></i></div>
            <span class="menu-text">Projets</span>
            <div class="submenu-arrow"><i class="fas fa-chevron-right"></i></div>
        </div>
        <div class="floating-submenu" id="submenu-gestion-projet">
            <a href="/projets/dashboard" class="submenu-link"><i class="fas fa-project-diagram"></i><span>Dashboard</span></a>
            <a href="/projets/list" class="submenu-link"><i class="fas fa-list"></i><span>Liste des projets</span></a>
            <a href="/projets/create" class="submenu-link"><i class="fas fa-plus"></i><span>Ouvrir un projet</span></a>
            <a href="/projets/reports" class="submenu-link"><i class="fas fa-chart-line"></i><span>Rapports projet</span></a>
        </div>
        @endif
        @endif



        <!-- Footer du menu -->
        <div class="menu-footer">
            <!-- Footer vide -->
        </div>
    </div>
</div>

@php
    $strictSidebarRoles = ['moderator', 'moderateur', 'modérateur'];
    $normalizedRole = auth()->check()
        ? \Illuminate\Support\Str::lower(trim((string) auth()->user()->role))
        : null;
    $useStrictSubmoduleSidebar = auth()->check() && in_array($normalizedRole, $strictSidebarRoles, true);
    $allowedSubmodulePaths = [];
    $existingSidebarPaths = [];

    // Construire le catalogue des routes statiques existantes pour éviter les liens morts.
    foreach (\Illuminate\Support\Facades\Route::getRoutes() as $route) {
        $uri = '/' . ltrim((string) $route->uri(), '/');
        $uri = preg_replace('#/+#', '/', $uri);

        // On ne garde que les routes sans paramètres dynamiques.
        if (str_contains($uri, '{')) {
            continue;
        }

        if ($uri === '') {
            $uri = '/';
        }

        $existingSidebarPaths[] = $uri;
    }
    $existingSidebarPaths = array_values(array_unique($existingSidebarPaths));

    if ($useStrictSubmoduleSidebar) {
        $currentUser = auth()->user();
        $allowedSubmodules = is_array($currentUser->submodules) ? $currentUser->submodules : [];

        foreach (config('submodules', []) as $moduleData) {
            foreach (($moduleData['submodules'] ?? []) as $submoduleKey => $submoduleData) {
                if (!in_array($submoduleKey, $allowedSubmodules, true)) {
                    continue;
                }

                if (!empty($submoduleData['route']) && \Illuminate\Support\Facades\Route::has($submoduleData['route'])) {
                    $allowedSubmodulePaths[] = parse_url(route($submoduleData['route']), PHP_URL_PATH);
                }

                if (!empty($submoduleData['url'])) {
                    $allowedSubmodulePaths[] = parse_url(url($submoduleData['url']), PHP_URL_PATH);
                }
            }
        }

        $allowedSubmodulePaths = array_values(array_unique(array_filter($allowedSubmodulePaths)));
    }
@endphp

@if($useStrictSubmoduleSidebar)
<script>
document.addEventListener('DOMContentLoaded', function () {
    const allowedPaths = @json($allowedSubmodulePaths);
    const existingPaths = @json($existingSidebarPaths);
    const normalizePath = (value) => {
        if (!value) return '/';
        let path = value;
        if (path.length > 1 && path.endsWith('/')) {
            path = path.slice(0, -1);
        }
        return path;
    };

    const allowedSet = new Set(allowedPaths.map(normalizePath));
    const existingSet = new Set(existingPaths.map(normalizePath));

    document.querySelectorAll('#app-sidebar .floating-submenu').forEach(function (submenu) {
        submenu.querySelectorAll('.submenu-link').forEach(function (link) {
            const href = link.getAttribute('href');
            if (!href) {
                return;
            }

            let path;
            try {
                path = new URL(href, window.location.origin).pathname;
            } catch (e) {
                return;
            }

            path = normalizePath(path);

            // Sécurité anti-404: masquer tout lien qui ne pointe pas vers une route statique existante.
            if (!existingSet.has(path)) {
                link.style.display = 'none';
                return;
            }

            if (!allowedSet.has(path)) {
                link.style.display = 'none';
            }
        });

        const visibleLinks = submenu.querySelectorAll('.submenu-link:not([style*="display: none"])').length;
        if (visibleLinks === 0) {
            submenu.style.display = 'none';
            const moduleKey = submenu.id.replace('submenu-', '');
            document
                .querySelectorAll('#app-sidebar .menu-item.has-submenu[data-module="' + moduleKey + '"]')
                .forEach(function (item) { item.style.display = 'none'; });
        }
    });
});
</script>
@endif

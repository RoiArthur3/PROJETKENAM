<style>
    #app-sidebar .floating-submenu {
        display: grid !important;
        grid-template-columns: repeat(4, minmax(180px, 1fr)) !important;
        gap: 10px !important;
        padding: 10px !important;
        align-items: stretch;
    }
    #app-sidebar .floating-submenu.large-grid {
        grid-template-columns: repeat(5, minmax(180px, 1fr)) !important;
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
        margin: 5px 0;
        font-weight: bold;
        color: #0d6efd;
        border-bottom: 2px solid #e9ecef;
        padding-bottom: 5px;
    }
    @media (max-width: 1600px) {
        #app-sidebar .floating-submenu, #app-sidebar .floating-submenu.large-grid {
            grid-template-columns: repeat(4, minmax(180px, 1fr)) !important;
        }
    }
    @media (max-width: 1200px) {
        #app-sidebar .floating-submenu, #app-sidebar .floating-submenu.large-grid {
            grid-template-columns: repeat(2, minmax(170px, 1fr)) !important;
        }
    }
    @media (max-width: 768px) {
        #app-sidebar .floating-submenu, #app-sidebar .floating-submenu.large-grid {
            grid-template-columns: 1fr !important;
        }
    }
</style>

<!-- Sidebar Métiers KENAM (SUPERADMIN) -->
<div id="app-sidebar" class="sidebar-desktop">
    <!-- Header -->
    <div class="sidebar-header">
        <a href="<?php echo e(auth()->user()->role === 'superadmin' ? '/dashboard' : (auth()->user()->getFirstAccessibleModuleUrl() ?? '/operations')); ?>">
            <img src="<?php echo e(asset('images/logo-kenam.png')); ?>" alt="KENAM SERVICES" class="sidebar-logo">
        </a>
    </div>

    <!-- Menu par Métiers -->
    <div class="sidebar-menu">
        <!-- 0. DASHBOARD GLOBAL -->
        <?php if(auth()->user()->role === 'superadmin'): ?>
        <a href="<?php echo e(route('dashboard')); ?>" class="menu-item active" data-tooltip="Tableau de bord">
            <div class="menu-icon">
                <i class="fas fa-home"></i>
            </div>
            <span class="menu-text">Vue Générale</span>
        </a>
        <?php endif; ?>

        <!-- 1. DIRECTION GÉNÉRALE -->
        <div class="menu-item has-submenu" data-tooltip="Direction Générale" data-module="direction_generale">
            <div class="menu-icon"><i class="fas fa-sitemap"></i></div>
            <span class="menu-text">Direction Générale</span>
            <div class="submenu-arrow"><i class="fas fa-chevron-right"></i></div>
        </div>
        <div class="floating-submenu" id="submenu-direction_generale">
            <a href="/reporting/dashboard" class="submenu-link"><i class="fas fa-chart-line text-primary"></i><span>Reporting</span></a>
            <?php if(Route::has('juridique.dashboard')): ?>
            <a href="<?php echo e(route('juridique.dashboard')); ?>" class="submenu-link"><i class="fas fa-gavel text-warning"></i><span>Juridique</span></a>
            <?php endif; ?>
            <a href="<?php echo e(route('hikvision.live')); ?>" class="submenu-link">
                <i class="fas fa-video text-primary"></i>
                <span>Surveillance en direct</span>
            </a>
            <a href="<?php echo e(route('validations.history')); ?>" class="submenu-link"><i class="fas fa-history text-secondary"></i><span>Suivi global</span></a>
            <?php if(Route::has('validations.to-pay')): ?>
            <a href="<?php echo e(route('validations.to-pay')); ?>" class="submenu-link"><i class="fas fa-stamp text-danger"></i><span>BON POUR ACCORD</span></a>
            <?php endif; ?>
        </div>

        <!-- 2. NOUVELLE DEMANDE -->
        <a href="<?php echo e(route('operations.create')); ?>" class="menu-item" data-tooltip="Nouvelle demande">
            <div class="menu-icon">
                <i class="fas fa-cogs text-success"></i>
            </div>
            <span class="menu-text">Nouvelle demande</span>
        </a>

        <!-- 3. SUIVI ET VALIDATIONS -->
        <div class="menu-item has-submenu" data-tooltip="Suivi et validations" data-module="validations">
            <div class="menu-icon"><i class="fas fa-tasks"></i></div>
            <span class="menu-text">Suivi et validations</span>
            <div class="submenu-arrow"><i class="fas fa-chevron-right"></i></div>
        </div>
        <div class="floating-submenu" id="submenu-validations">
            <?php
                $validationLinks = [
                    ['key' => 'validations_pending',          'label' => 'En attente',          'icon' => 'fas fa-clock text-warning',    'route' => 'validations.pending'],
                    ['key' => 'validations_approved',         'label' => 'Approuvées',           'icon' => 'fas fa-check text-success',    'url'   => '/validations/approved'],
                    ['key' => 'validations_rejected',         'label' => 'Rejetées',             'icon' => 'fas fa-times text-danger',     'url'   => '/validations/rejected'],
                    ['key' => 'validations_history',          'label' => 'Historique',           'icon' => 'fas fa-history text-secondary','route' => 'validations.history'],
                    ['key' => 'validations_to_pay',           'label' => 'BON POUR ACCORD',      'icon' => 'fas fa-stamp text-danger',     'route' => 'validations.to-pay'],
                    ['key' => 'validations_caisse_execution', 'label' => 'Bon pour exécution',   'icon' => 'fas fa-cash-register',         'route' => 'validations.caisse-execution'],
                    ['key' => 'validations_paid',             'label' => 'Payées',               'icon' => 'fas fa-coins text-primary',    'route' => 'validations.paid'],
                ];
            ?>
            <?php $__currentLoopData = $validationLinks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $vLink): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $canSee  = auth()->user()->canAccessSubmodule($vLink['key']);
                    $hasRoute = isset($vLink['route']) ? Route::has($vLink['route']) : true;
                    $href     = isset($vLink['route']) ? route($vLink['route']) : url($vLink['url']);
                ?>
                <?php if($canSee && $hasRoute): ?>
                <a href="<?php echo e($href); ?>" class="submenu-link"><i class="<?php echo e($vLink['icon']); ?>"></i><span><?php echo e($vLink['label']); ?></span></a>
                <?php endif; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>

        <!-- 3. COMPTABILITÉ -->
        <div class="menu-item has-submenu" data-tooltip="Comptabilité" data-module="comptabilite">
            <div class="menu-icon"><i class="fas fa-calculator"></i></div>
            <span class="menu-text">Comptabilité</span>
            <div class="submenu-arrow"><i class="fas fa-chevron-right"></i></div>
        </div>
        <div class="floating-submenu" id="submenu-comptabilite">
            <a href="<?php echo e(route('comptabilite.dashboard')); ?>" class="submenu-link"><i class="fas fa-tachometer-alt text-primary"></i><span>Dashboard</span></a>
            <a href="<?php echo e(route('comptabilite.factures.index')); ?>" class="submenu-link"><i class="fas fa-file-invoice text-info"></i><span>Factures</span></a>
            <a href="<?php echo e(route('comptabilite.depenses.index')); ?>" class="submenu-link"><i class="fas fa-money-bill-wave text-danger"></i><span>Dépenses</span></a>
            <a href="<?php echo e(route('comptabilite.recettes.index')); ?>" class="submenu-link"><i class="fas fa-hand-holding-usd text-success"></i><span>Recettes</span></a>
            <a href="<?php echo e(route('comptabilite.rapports.grand-journal')); ?>" class="submenu-link"><i class="fas fa-book-open text-info"></i><span>Grand Journal</span></a>
            <a href="<?php echo e(route('comptabilite.rapports.bilan')); ?>" class="submenu-link"><i class="fas fa-balance-scale text-secondary"></i><span>Bilan</span></a>
            <a href="<?php echo e(route('comptabilite.rapports.compte-resultat')); ?>" class="submenu-link"><i class="fas fa-chart-line text-warning"></i><span>Compte Résultat</span></a>
            <a href="<?php echo e(route('comptabilite.rapports.analyse-activite')); ?>" class="submenu-link"><i class="fas fa-table text-dark"></i><span>ANALYSE DE L'ACTIVITE</span></a>
            <a href="<?php echo e(route('comptabilite.rapports.analyse-rentabilite')); ?>" class="submenu-link"><i class="fas fa-percentage text-primary"></i><span>ANALYSE DE RENTABILITE</span></a>
            <a href="<?php echo e(route('comptabilite.rapports.analyse-variation-treso')); ?>" class="submenu-link"><i class="fas fa-water text-info"></i><span>ANALYSE VARIATION DE LA TRESO</span></a>
            <a href="<?php echo e(route('comptabilite.rapports.analyse-variation-dette')); ?>" class="submenu-link"><i class="fas fa-file-invoice-dollar text-danger"></i><span>ANALYSE VARIATION DE LA DETTE</span></a>
            <a href="<?php echo e(route('comptabilite.approvisionnement-demandes.pending')); ?>" class="submenu-link"><i class="fas fa-clipboard-check text-warning"></i><span>Demandes d'approvisionnement</span></a>
        </div>

        <!-- 4. TRÉSORERIE -->
        <div class="menu-item has-submenu" data-tooltip="Trésorerie" data-module="tresorerie">
            <div class="menu-icon"><i class="fas fa-coins"></i></div>
            <span class="menu-text">Trésorerie</span>
            <div class="submenu-arrow"><i class="fas fa-chevron-right"></i></div>
        </div>
        <div class="floating-submenu" id="submenu-tresorerie">
            <?php $tresorerieSubmodules = config('submodules.tresorerie.submodules', []); ?>
            <?php $__currentLoopData = $tresorerieSubmodules; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $submodule): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php if(in_array($key, ['tres_dashboard', 'tres_caisses', 'tres_appro', 'tres_appro_dem', 'tres_bon_accord', 'tres_encaisse', 'tres_decaisse', 'tres_rapproche', 'tres_a_payer', 'tres_soldes'])): ?>
                    <a href="<?php echo e(route($submodule['route'])); ?>" class="submenu-link"><i class="<?php echo e($submodule['icon']); ?> text-info"></i><span><?php echo e($submodule['name']); ?></span></a>
                <?php endif; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <a href="<?php echo e(route('tresorerie.banque')); ?>" class="submenu-link"><i class="fas fa-university text-info"></i><span>Banque</span></a>
            <a href="<?php echo e(route('tresorerie.avances')); ?>" class="submenu-link"><i class="fas fa-hand-holding-usd text-info"></i><span>Acomptes</span></a>
        </div>

        <!-- 4. COST CONTROL -->
        <div class="menu-item has-submenu" data-tooltip="Cost Control" data-module="cost_control">
            <div class="menu-icon"><i class="fas fa-stopwatch"></i></div>
            <span class="menu-text">Cost Control (Engins)</span>
            <div class="submenu-arrow"><i class="fas fa-chevron-right"></i></div>
        </div>
        <div class="floating-submenu" id="submenu-cost_control">
            <a href="<?php echo e(route('materiel.cost-control.home')); ?>" class="submenu-link"><i class="fas fa-tachometer-alt text-primary"></i><span>Dashboard Cost Control</span></a>
            <a href="<?php echo e(route('materiel.cost-control.engin.pointages.create')); ?>" class="submenu-link"><i class="fas fa-plus text-success"></i><span>Nouveau pointage engin</span></a>
            <a href="<?php echo e(route('materiel.cost-control.engin.list')); ?>" class="submenu-link"><i class="fas fa-list text-info"></i><span>Liste des pointages engins</span></a>
            
        </div>

        <!-- 5. NOUVELLE DEMANDE (déplacé dans Suivi et validations) -->

        <!-- 6. COMMERCIAL -->
        <?php if(auth()->user()->canAccessModule('commercial')): ?>
        <div class="menu-item has-submenu" data-tooltip="Commercial" data-module="commercial">
            <div class="menu-icon"><i class="fas fa-briefcase"></i></div>
            <span class="menu-text">Commercial</span>
            <div class="submenu-arrow"><i class="fas fa-chevron-right"></i></div>
        </div>
        <div class="floating-submenu" id="submenu-commercial">
            <?php if(Route::has('commercial.dashboard')): ?>
            <a href="<?php echo e(route('commercial.dashboard')); ?>" class="submenu-link"><i class="fas fa-tachometer-alt text-primary"></i><span>Dashboard Commercial</span></a>
            <?php else: ?>
            <a href="/commercial/dashboard" class="submenu-link"><i class="fas fa-tachometer-alt text-primary"></i><span>Dashboard Commercial</span></a>
            <?php endif; ?>
            <?php if(Route::has('commercial.clients.index')): ?>
            <a href="<?php echo e(route('commercial.clients.index')); ?>" class="submenu-link"><i class="fas fa-users text-info"></i><span>Clients</span></a>
            <?php endif; ?>
            <?php if(Route::has('commercial.prospects.index')): ?>
            <a href="<?php echo e(route('commercial.prospects.index')); ?>" class="submenu-link"><i class="fas fa-user-plus text-success"></i><span>Prospects</span></a>
            <?php else: ?>
            <a href="/commercial/prospects" class="submenu-link"><i class="fas fa-user-plus text-success"></i><span>Prospects</span></a>
            <?php endif; ?>
            <?php if(Route::has('commercial.commandes.index')): ?>
            <a href="<?php echo e(route('commercial.commandes.index')); ?>" class="submenu-link"><i class="fas fa-clipboard-list text-warning"></i><span>Commandes</span></a>
            <?php endif; ?>
            <?php if(Route::has('commercial.bon-commande.index')): ?>
            <a href="<?php echo e(route('commercial.bon-commande.index')); ?>" class="submenu-link"><i class="fas fa-file-signature text-secondary"></i><span>Bons de commande</span></a>
            <?php endif; ?>
            <?php if(Route::has('commercial.bon-livraison.index')): ?>
            <a href="<?php echo e(route('commercial.bon-livraison.index')); ?>" class="submenu-link"><i class="fas fa-truck text-success"></i><span>Bons de livraison</span></a>
            <?php endif; ?>
            <?php if(Route::has('commercial.devis.index')): ?>
            <a href="<?php echo e(route('commercial.devis.index')); ?>" class="submenu-link"><i class="fas fa-file-invoice text-danger"></i><span>Devis</span></a>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <?php if(auth()->user()->canAccessModule('achat')): ?>
        <div class="menu-item has-submenu" data-tooltip="Achat" data-module="achat">
            <div class="menu-icon"><i class="fas fa-shopping-cart"></i></div>
            <span class="menu-text">Achat</span>
            <div class="submenu-arrow"><i class="fas fa-chevron-right"></i></div>
        </div>
            <div class="floating-submenu" id="submenu-achat">
            <a href="<?php echo e(route('achat.index')); ?>" class="submenu-link"><i class="fas fa-tachometer-alt text-primary"></i><span>Liste des achats</span></a>
            <a href="<?php echo e(route('achat.create')); ?>" class="submenu-link"><i class="fas fa-plus text-success"></i><span>Nouvel achat</span></a>
        </div>
        <?php endif; ?>

        <!-- 7. FOURNISSEURS -->
        <div class="menu-item has-submenu" data-tooltip="Fournisseurs" data-module="fournisseurs">
            <div class="menu-icon"><i class="fas fa-truck"></i></div>
            <span class="menu-text">Fournisseurs</span>
            <div class="submenu-arrow"><i class="fas fa-chevron-right"></i></div>
        </div>
        <div class="floating-submenu" id="submenu-fournisseurs">
            <div class="submenu-divider">CRÉATION</div>
            <a href="<?php echo e(route('fournisseurs.create-engins')); ?>" class="submenu-link"><i class="fas fa-cogs text-primary"></i><span>Nouveau Fournisseur ENGIN</span></a>
            <a href="<?php echo e(route('fournisseurs.create')); ?>" class="submenu-link"><i class="fas fa-box text-warning"></i><span>Nouveau Fournisseur MATÉRIEL</span></a>

            <div class="submenu-divider">FOURNISSEURS ENGIN</div>
            <a href="/fournisseurs/engins/kenam" class="submenu-link"><i class="fas fa-building text-primary"></i><span>Fournisseurs Internes</span></a>
            <a href="/fournisseurs/engins/list" class="submenu-link"><i class="fas fa-truck text-success"></i><span>Fournisseurs Externes</span></a>

            <div class="submenu-divider">FOURNISSEURS MATÉRIEL</div>
            <a href="/fournisseurs/entrepots" class="submenu-link"><i class="fas fa-warehouse text-info"></i><span>Fournisseurs Entrepôts</span></a>
            <a href="/fournisseurs/magasin" class="submenu-link"><i class="fas fa-store text-warning"></i><span>Fournisseurs Magasin</span></a>
        </div>

        <!-- 8. STOCK & MAGASINS -->
        <div class="menu-item has-submenu" data-tooltip="Stock & Magasins" data-module="stock_magasins">
            <div class="menu-icon"><i class="fas fa-boxes"></i></div>
            <span class="menu-text">Stock & Magasins</span>
            <div class="submenu-arrow"><i class="fas fa-chevron-right"></i></div>
        </div>
        <div class="floating-submenu" id="submenu-stock_magasins">
            <div class="submenu-divider">GESTION DES STOCKS</div>
            <a href="/magasin/dashboard" class="submenu-link"><i class="fas fa-store text-success"></i><span>Magasin</span></a>
            <a href="/entrepots/dashboard" class="submenu-link"><i class="fas fa-building text-info"></i><span>Entrepôts</span></a>

            <div class="submenu-divider">OPÉRATIONS</div>
            <a href="/magasin/entrees" class="submenu-link"><i class="fas fa-sign-in-alt text-primary"></i><span>Entrées Stock</span></a>
            <a href="/magasin/sorties" class="submenu-link"><i class="fas fa-sign-out-alt text-warning"></i><span>Sorties Stock</span></a>
            <a href="/magasin/inventaire" class="submenu-link"><i class="fas fa-clipboard-list text-info"></i><span>Inventaire</span></a>
            <a href="/magasin/rapports" class="submenu-link"><i class="fas fa-chart-line text-secondary"></i><span>Rapports Stock</span></a>
        </div>


        <!-- 9. LOGISTIQUE -->
        <div class="menu-item has-submenu" data-tooltip="Logistique" data-module="logistique">
            <div class="menu-icon"><i class="fas fa-truck"></i></div>
            <span class="menu-text">Logistique</span>
            <div class="submenu-arrow"><i class="fas fa-chevron-right"></i></div>
        </div>
        <div class="floating-submenu large-grid" id="submenu-logistique">
            <div class="submenu-divider">ATELIER</div>
            <a href="<?php echo e(route('materiel.cost-control.home')); ?>" class="submenu-link"><i class="fas fa-tachometer-alt text-primary"></i><span>Dashboard Atelier</span></a>
            <a href="/fournisseurs/commandes" class="submenu-link"><i class="fas fa-shopping-cart text-success"></i><span>Commandes d'achat</span></a>
            <a href="<?php echo e(route('materiel.assurances.index')); ?>" class="submenu-link"><i class="fas fa-shield-alt text-warning"></i><span>Assurances</span></a>
            <a href="<?php echo e(route('materiel.visites.index')); ?>" class="submenu-link"><i class="fas fa-clipboard-check text-success"></i><span>Visites techniques</span></a>
            <a href="/materiel/vehicules" class="submenu-link"><i class="fas fa-truck text-info"></i><span>Engins & Véhicules</span></a>
            <a href="/materiel/maintenance" class="submenu-link"><i class="fas fa-wrench text-warning"></i><span>Maintenance</span></a>
            <a href="<?php echo e(route('fleet.affectations.index')); ?>" class="submenu-link"><i class="fas fa-user-tag text-success"></i><span>Affectations</span></a>
            <a href="<?php echo e(route('materiel.missions.index')); ?>" class="submenu-link"><i class="fas fa-map-location-dot text-danger"></i><span>Liste des missions</span></a>
            <a href="<?php echo e(route('materiel.missions.create')); ?>" class="submenu-link"><i class="fas fa-plus-circle text-success"></i><span>Nouvelle mission</span></a>
            <a href="<?php echo e(route('materiel.missions.export')); ?>" class="submenu-link"><i class="fas fa-file-export text-info"></i><span>Exporter missions</span></a>

            <div class="submenu-divider">PROJETS</div>
            <a href="/projets/dashboard" class="submenu-link"><i class="fas fa-hard-hat text-primary"></i><span>Dashboard Projets</span></a>
            <a href="/projets/list" class="submenu-link"><i class="fas fa-list text-info"></i><span>Liste des projets</span></a>
            <a href="/projets/create" class="submenu-link"><i class="fas fa-plus text-success"></i><span>Ouvrir un projet</span></a>
            <a href="/projets/reports" class="submenu-link"><i class="fas fa-chart-line text-warning"></i><span>Rapports projet</span></a>
        </div>

        <!-- 8. RESSOURCES HUMAINES -->
        <div class="menu-item has-submenu" data-tooltip="Ressources Humaines" data-module="rh">
            <div class="menu-icon"><i class="fas fa-users-cog"></i></div>
            <span class="menu-text">Ressources Humaines</span>
            <div class="submenu-arrow"><i class="fas fa-chevron-right"></i></div>
        </div>
        <div class="floating-submenu" id="submenu-rh">
            <a href="/rh/dashboard" class="submenu-link"><i class="fas fa-tachometer-alt text-primary"></i><span>Dashboard RH</span></a>
            <a href="<?php echo e(route('rh.personnel.index')); ?>" class="submenu-link"><i class="fas fa-users text-info"></i><span>Personnel</span></a>
            <a href="<?php echo e(route('rh.paie.index')); ?>" class="submenu-link"><i class="fas fa-money-bill text-success"></i><span>Paie</span></a>
            <a href="/rh/pointages" class="submenu-link"><i class="fas fa-clock text-secondary"></i><span>Pointage RH manuel</span></a>
            <a href="/rh/facial-pointage/dashboard" class="submenu-link"><i class="fas fa-id-card text-dark"></i><span>Pointage caméra Hikvision</span></a>
        </div>

        <!-- 10. ADMINISTRATION -->
        <div class="menu-item has-submenu" data-tooltip="Administration" data-module="administration">
            <div class="menu-icon"><i class="fas fa-user-shield"></i></div>
            <span class="menu-text">Administration</span>
            <div class="submenu-arrow"><i class="fas fa-chevron-right"></i></div>
        </div>
        <div class="floating-submenu" id="submenu-administration">
            <?php if(Route::has('settings.index')): ?>
            <a href="<?php echo e(route('settings.index')); ?>" class="submenu-link"><i class="fas fa-cog text-primary"></i><span>Paramètres</span></a>
            <?php endif; ?>
            <?php if(Route::has('parametrage.index')): ?>
            <a href="<?php echo e(route('parametrage.index')); ?>" class="submenu-link"><i class="fas fa-sliders-h text-info"></i><span>Paramétrage</span></a>
            <?php endif; ?>
            <?php if(Route::has('imports.historique.index')): ?>
            <a href="<?php echo e(route('imports.historique.index')); ?>" class="submenu-link"><i class="fas fa-file-import text-success"></i><span>Import Historique</span></a>
            <?php endif; ?>
            <?php if(Route::has('admin.permissions')): ?>
            <a href="<?php echo e(route('admin.permissions')); ?>" class="submenu-link"><i class="fas fa-key text-warning"></i><span>Permissions</span></a>
            <?php endif; ?>
            <?php if(Route::has('admin.comptes.users.index')): ?>
            <a href="<?php echo e(route('admin.comptes.users.index')); ?>" class="submenu-link"><i class="fas fa-users-cog text-success"></i><span>Utilisateurs</span></a>
            <?php endif; ?>
        </div>

        <div class="menu-footer">
            <!-- Footer vide -->
        </div>
    </div>
</div>

<?php
    $usePermissionFilteredSidebar = auth()->check() && auth()->user() && auth()->user()->role !== 'superadmin';
    $allowedSubmodulePaths = [];
    $existingSidebarPaths = [];

    foreach (\Illuminate\Support\Facades\Route::getRoutes() as $route) {
        $uri = '/' . ltrim((string) $route->uri(), '/');
        $uri = preg_replace('#/+#', '/', $uri);

        if (str_contains($uri, '{')) {
            continue;
        }

        if ($uri === '') {
            $uri = '/';
        }

        $existingSidebarPaths[] = $uri;
    }
    $existingSidebarPaths = array_values(array_unique($existingSidebarPaths));

    if ($usePermissionFilteredSidebar) {
        $currentUser = auth()->user();

        foreach (config('submodules', []) as $moduleKey => $moduleData) {
            if (!$currentUser->canAccessModule($moduleKey)) {
                continue;
            }

            $allowedSubmoduleKeys = $currentUser->getAllowedSubmodules($moduleKey);
            foreach (($moduleData['submodules'] ?? []) as $submoduleKey => $submoduleData) {
                if (!in_array($submoduleKey, $allowedSubmoduleKeys, true)) {
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

        // Ajouter les dashboards de modules autorisés (pas toujours présents dans config/submodules).
        $dashboardPathMap = [
            'operations' => '/operations',
            'validations' => '/validations/pending',
            'warehouse' => '/warehouse/dashboard',
            'magasin' => '/magasin/dashboard',
            'entrepots' => '/entrepots/dashboard',
            'materiel' => '/materiel/cost-control',
            'commercial' => '/commercial/dashboard',
            'fournisseurs' => '/fournisseurs/dashboard',
            'tresorerie' => '/tresorerie/dashboard',
            'comptabilite' => '/comptabilite/dashboard',
            'juridique' => '/juridique',
            'rh' => '/rh/dashboard',
            'reporting' => '/reporting/dashboard',
            'projects' => '/projets/dashboard',
            'audit' => '/audit/dashboard',
        ];

        foreach ($dashboardPathMap as $moduleKey => $path) {
            if ($currentUser->canAccessModule($moduleKey)) {
                $allowedSubmodulePaths[] = $path;
            }
        }

        $allowedSubmodulePaths = array_values(array_unique(array_filter($allowedSubmodulePaths)));
    }
?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const usePermissionFilteredSidebar = <?php echo e(auth()->check() && auth()->user() && auth()->user()->role !== 'superadmin' ? 'true' : 'false'); ?>;
    const allowedSubmodulePaths = <?php echo json_encode($allowedSubmodulePaths ?? [], 15, 512) ?>;
    const existingSidebarPaths = <?php echo json_encode($existingSidebarPaths ?? [], 15, 512) ?>;

    const normalizePath = function(path) {
        if (path.endsWith('/') && path !== '/') {
            path = path.slice(0, -1);
        }
        return path;
    };

    const allowedSet = new Set(allowedSubmodulePaths.map(normalizePath));
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
            if (!existingSet.has(path) || !allowedSet.has(path)) {
                link.style.setProperty('display', 'none', 'important');
            }
        });

        const visibleLinks = submenu.querySelectorAll('.submenu-link:not([style*="display: none"])').length;
        if (visibleLinks === 0) {
            submenu.style.setProperty('display', 'none', 'important');
            const moduleKey = submenu.id.replace('submenu-', '');
            document
                .querySelectorAll('#app-sidebar .menu-item.has-submenu[data-module="' + moduleKey + '"]')
                .forEach(function (item) { item.style.setProperty('display', 'none', 'important'); });
        }
    });
});
</script>
<?php /**PATH C:\laragon\www\kenam\resources\views/layouts/sidebar-superadmin.blade.php ENDPATH**/ ?>
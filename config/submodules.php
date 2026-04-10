<?php

return [
    'operations' => [
        'name' => 'Opérations',
        'icon' => 'fas fa-cogs',
        'submodules' => [
            'operations_list'   => ['name' => 'Liste des opérations', 'icon' => 'fas fa-list', 'route' => 'operations.index'],
            'operations_create' => ['name' => 'Nouvelle opération', 'icon' => 'fas fa-plus', 'route' => 'operations.create'],
        ]
    ],
    'validations' => [
        'name' => 'Suivi et validations',
        'icon' => 'fas fa-tasks',
        'submodules' => [
            'validations_pending'  => ['name' => 'En attente', 'icon' => 'fas fa-clock', 'url' => '/validations/pending'],
            'validations_approved' => ['name' => 'Approuvées', 'icon' => 'fas fa-check', 'url' => '/validations/approved'],
            'validations_to_pay'   => ['name' => 'A Payer (Compta)', 'icon' => 'fas fa-wallet text-primary', 'route' => 'validations.to-pay'],
            'validations_caisse_execution' => ['name' => 'Bon pour exécution', 'icon' => 'fas fa-cash-register text-warning', 'route' => 'validations.caisse-execution'],
            'validations_paid'     => ['name' => 'Payées', 'icon' => 'fas fa-coins text-success', 'route' => 'validations.paid'],
            'validations_rejected' => ['name' => 'Rejetées', 'icon' => 'fas fa-times', 'url' => '/validations/rejected'],
            'validations_history'  => ['name' => 'Historique', 'icon' => 'fas fa-history', 'route' => 'validations.history'],
        ]
    ],
    'warehouse' => [
        'name' => 'Entrepôt',
        'icon' => 'fas fa-warehouse',
        'submodules' => [
            'warehouse_dashboard' => ['name' => 'Dashboard', 'icon' => 'fas fa-tachometer-alt', 'url' => '/warehouse/dashboard'],
            'warehouse_inventory' => ['name' => 'Inventaire', 'icon' => 'fas fa-boxes', 'url' => '/warehouse/inventaire'],
            'warehouse_entries'   => ['name' => 'Entrées', 'icon' => 'fas fa-sign-in-alt', 'url' => '/warehouse/entrees'],
            'warehouse_exits'     => ['name' => 'Sorties', 'icon' => 'fas fa-sign-out-alt', 'url' => '/warehouse/sorties'],
            'warehouse_reports'   => ['name' => 'Rapports', 'icon' => 'fas fa-chart-bar', 'url' => '/warehouse/rapports'],
        ]
    ],
    'magasin' => [
        'name' => 'Magasin',
        'icon' => 'fas fa-warehouse',
        'submodules' => [
            'magasin_dashboard' => ['name' => 'Dashboard', 'icon' => 'fas fa-tachometer-alt', 'url' => '/magasin/dashboard'],
            'magasin_inventory' => ['name' => 'Inventaire', 'icon' => 'fas fa-boxes', 'url' => '/magasin/inventaire'],
            'magasin_entries'   => ['name' => 'Entrées', 'icon' => 'fas fa-sign-in-alt', 'url' => '/magasin/entrees'],
            'magasin_exits'     => ['name' => 'Sorties', 'icon' => 'fas fa-sign-out-alt', 'url' => '/magasin/sorties'],
            'magasin_reports'   => ['name' => 'Rapports', 'icon' => 'fas fa-chart-bar', 'url' => '/magasin/rapports'],
        ]
    ],
    'entrepots' => [
        'name' => 'Entrepôts',
        'icon' => 'fas fa-building',
        'submodules' => [
            'entrepots_dashboard' => ['name' => 'Dashboard', 'icon' => 'fas fa-tachometer-alt', 'url' => '/entrepots/dashboard'],
            'entrepots_list'      => ['name' => 'Liste', 'icon' => 'fas fa-list', 'url' => '/entrepots/list'],
            'entrepots_stock'     => ['name' => 'Stock', 'icon' => 'fas fa-boxes', 'url' => '/entrepots/stock'],
            'entrepots_transfers' => ['name' => 'Transferts', 'icon' => 'fas fa-exchange-alt', 'url' => '/entrepots/transferts'],
            'entrepots_reports'   => ['name' => 'Rapports', 'icon' => 'fas fa-chart-bar', 'url' => '/entrepots/rapports'],
        ]
    ],
    'materiel' => [
        'name' => 'Parc Auto / Flotte',
        'icon' => 'fas fa-car-side',
        'submodules' => [
            'materiel_dashboard'   => ['name' => 'Dashboard', 'icon' => 'fas fa-tachometer-alt', 'url' => '/materiel/dashboard'],
            'materiel_vehicles'    => ['name' => 'Véhicules', 'icon' => 'fas fa-truck', 'url' => '/materiel/vehicules'],
            'materiel_maintenance' => ['name' => 'Maintenance', 'icon' => 'fas fa-wrench', 'url' => '/materiel/maintenance'],
            'materiel_assurances'  => ['name' => 'Assurances', 'icon' => 'fas fa-shield-alt', 'route' => 'materiel.assurances.index'],
            'materiel_visites_techniques' => ['name' => 'Visites techniques', 'icon' => 'fas fa-clipboard-check', 'route' => 'materiel.visites.index'],
            'materiel_assignments' => ['name' => 'Affectations', 'icon' => 'fas fa-user-tag', 'route' => 'fleet.affectations.index'],
            'materiel_missions'    => ['name' => 'Liste des missions', 'icon' => 'fas fa-map-location-dot', 'route' => 'materiel.missions.index'],
            'materiel_missions_create' => ['name' => 'Nouvelle mission', 'icon' => 'fas fa-plus-circle', 'route' => 'materiel.missions.create'],
            'materiel_missions_export' => ['name' => 'Exporter missions', 'icon' => 'fas fa-file-export', 'route' => 'materiel.missions.export'],
            'materiel_fuel'        => ['name' => 'Carburant', 'icon' => 'fas fa-gas-pump', 'url' => '/materiel/carburant'],
            'materiel_reports'     => ['name' => 'Rapports', 'icon' => 'fas fa-chart-line', 'url' => '/materiel/rapports'],
            'fleet_checking'       => ['name' => 'Checking - Liste', 'icon' => 'fas fa-clipboard-check', 'route' => 'fleet.checking.index'],
        ]
    ],
    'cost_control' => [
        'name' => 'Cost Control',
        'icon' => 'fas fa-stopwatch',
        'submodules' => [
            'cost_control_dashboard' => ['name' => 'Dashboard', 'icon' => 'fas fa-tachometer-alt', 'url' => '/cost-control'],
            'cost_control_new_pointage' => ['name' => 'Nouveau pointage engin', 'icon' => 'fas fa-plus', 'url' => '/cost-control/pointages/create'],
            'cost_control_list_pointages' => ['name' => 'Liste des pointages engins', 'icon' => 'fas fa-list', 'url' => '/cost-control'],
            'cost_control_camion_plateau' => ['name' => 'Camion Plateau', 'icon' => 'fas fa-road', 'url' => '/cost-control/camion-plateau'],
        ]
    ],
    'fournisseurs' => [
        'name' => 'Fournisseurs',
        'icon' => 'fas fa-industry',
        'submodules' => [
            'fourn_dashboard' => ['name' => 'Dashboard', 'icon' => 'fas fa-tachometer-alt', 'url' => '/fournisseurs/dashboard'],
            'fourn_list'      => ['name' => 'Liste', 'icon' => 'fas fa-list', 'url' => '/fournisseurs/list'],
            'fourn_create'    => ['name' => 'Nouveau', 'icon' => 'fas fa-plus', 'url' => '/fournisseurs/create'],
            'fourn_orders'    => ['name' => 'Commandes', 'icon' => 'fas fa-shopping-cart', 'url' => '/fournisseurs/commandes'],
            'fourn_invoices'  => ['name' => 'Factures', 'icon' => 'fas fa-file-invoice', 'url' => '/fournisseurs/factures'],
        ]
    ],
    'achat' => [
        'name' => 'Achat',
        'icon' => 'fas fa-shopping-cart',
        'submodules' => [
            'achat_dashboard' => ['name' => 'Liste des achats', 'icon' => 'fas fa-list', 'route' => 'achat.index'],
            'achat_create'    => ['name' => 'Nouvel achat', 'icon' => 'fas fa-plus', 'route' => 'achat.create'],
            'achat_validated' => ['name' => 'Achats validés', 'icon' => 'fas fa-check-circle', 'route' => 'achat.index'],
            'achat_to_pay'    => ['name' => 'Achats à décaisser', 'icon' => 'fas fa-money-check-dollar', 'route' => 'achat.index'],
        ]
    ],
    'commercial' => [
        'name' => 'Commercial',
        'icon' => 'fas fa-briefcase',
        'submodules' => [
            'comm_dashboard'     => ['name' => 'Dashboard', 'icon' => 'fas fa-tachometer-alt', 'url' => '/commercial/dashboard'],
            'comm_clients'       => ['name' => 'Clients', 'icon' => 'fas fa-users', 'url' => '/commercial/clients'],
            'comm_commandes'     => ['name' => 'Commandes', 'icon' => 'fas fa-clipboard-list', 'url' => '/commercial/commandes'],
            'comm_bon_commande'  => ['name' => 'Bon de Commande', 'icon' => 'fas fa-file-invoice', 'url' => '/commercial/bon-commande'],
            'comm_bon_livraison' => ['name' => 'Bon de Livraison', 'icon' => 'fas fa-truck', 'url' => '/commercial/bon-livraison'],
            'comm_devis'         => ['name' => 'Devis', 'icon' => 'fas fa-file-invoice', 'url' => '/commercial/devis'],
        ]
    ],
    'tresorerie' => [
        'name' => 'Trésorerie',
        'icon' => 'fas fa-coins',
        'submodules' => [
            'tres_dashboard' => ['name' => 'Dashboard Trésorerie', 'icon' => 'fas fa-tachometer-alt', 'route' => 'tresorerie.dashboard'],
            'tres_caisses'   => ['name' => 'Caisses', 'icon' => 'fas fa-cash-register', 'route' => 'tresorerie.caisses.index'],
            'tres_appro'     => ['name' => 'Approvisionnements', 'icon' => 'fas fa-plus-circle', 'route' => 'tresorerie.approvisionnements'],
            'tres_appro_dem' => ['name' => 'Demandes d\'approvisionnement', 'icon' => 'fas fa-file-invoice-dollar', 'route' => 'tresorerie.approvisionnement-demandes.index'],
            'tres_bon_accord' => ['name' => 'BON POUR ACCORD', 'icon' => 'fas fa-stamp', 'route' => 'validations.to-pay'],
            'tres_encaisse'  => ['name' => 'Encaissements', 'icon' => 'fas fa-arrow-down', 'route' => 'tresorerie.encaissements'],
            'tres_decaisse'  => ['name' => 'Décaissements', 'icon' => 'fas fa-arrow-up', 'route' => 'tresorerie.decaissements'],
            'tres_rapproche' => ['name' => 'Rapprochements', 'icon' => 'fas fa-balance-scale', 'route' => 'tresorerie.rapprochements'],
            'tres_a_payer'   => ['name' => 'A Payer', 'icon' => 'fas fa-hand-holding-usd', 'route' => 'tresorerie.a-payer'],
            'tres_soldes'    => ['name' => 'Soldes de Caisse', 'icon' => 'fas fa-wallet', 'route' => 'tresorerie.soldes-caisse'],
        ]
    ],
    'comptabilite' => [
        'name' => 'Comptabilité',
        'icon' => 'fas fa-calculator',
        'submodules' => [
            'compta_dashboard' => ['name' => 'Dashboard', 'icon' => 'fas fa-tachometer-alt', 'route' => 'comptabilite.dashboard'],
            'compta_appro_dem' => ['name' => 'Demandes d\'approvisionnement', 'icon' => 'fas fa-clipboard-check', 'route' => 'comptabilite.approvisionnement-demandes.pending'],
            'compta_depenses'  => ['name' => 'Dépenses', 'icon' => 'fas fa-money-bill-wave', 'route' => 'comptabilite.depenses.index'],
            'compta_recettes'  => ['name' => 'Recettes', 'icon' => 'fas fa-hand-holding-usd', 'route' => 'comptabilite.recettes.index'],
            'compta_factures'  => ['name' => 'Factures', 'icon' => 'fas fa-file-invoice', 'route' => 'comptabilite.factures.index'],
            'compta_paiements' => ['name' => 'Paiements', 'icon' => 'fas fa-credit-card', 'route' => 'comptabilite.paiements.index'],
            'compta_grand_journal' => ['name' => 'Grand Journal', 'icon' => 'fas fa-book-open', 'route' => 'comptabilite.rapports.grand-journal'],
            'compta_bilan'     => ['name' => 'Bilan', 'icon' => 'fas fa-balance-scale', 'route' => 'comptabilite.rapports.bilan'],
            'compta_resultat'  => ['name' => 'Compte de Résultat', 'icon' => 'fas fa-chart-line', 'route' => 'comptabilite.rapports.compte-resultat'],
            'compta_indicateurs_financiers' => ['name' => 'Indicateurs Financiers', 'icon' => 'fas fa-chart-pie', 'route' => 'comptabilite.rapports.indicateurs-financiers'],
            'compta_analyse_activite' => ['name' => 'ANALYSE DE L\'ACTIVITE', 'icon' => 'fas fa-table', 'route' => 'comptabilite.rapports.analyse-activite'],
            'compta_analyse_rentabilite' => ['name' => 'ANALYSE DE RENTABILITE', 'icon' => 'fas fa-percentage', 'route' => 'comptabilite.rapports.analyse-rentabilite'],
            'compta_analyse_variation_treso' => ['name' => 'ANALYSE VARIATION DE LA TRESO', 'icon' => 'fas fa-water', 'route' => 'comptabilite.rapports.analyse-variation-treso'],
            'compta_analyse_variation_dette' => ['name' => 'ANALYSE VARIATION DE LA DETTE', 'icon' => 'fas fa-file-invoice-dollar', 'route' => 'comptabilite.rapports.analyse-variation-dette'],
            'compta_treso'     => ['name' => 'Rapport Trésorerie', 'icon' => 'fas fa-coins', 'route' => 'comptabilite.rapports.tresorerie'],
            'compta_is'        => ['name' => 'Impôt Sociétés (IS)', 'icon' => 'fas fa-building', 'route' => 'comptabilite.impot-societes.index'],
            'compta_tva_decl'  => ['name' => 'TVA Déclarative', 'icon' => 'fas fa-receipt', 'route' => 'comptabilite.tva-declarative.index'],
            'compta_syscohada' => ['name' => 'Plan Comptable SYSCOHADA', 'icon' => 'fas fa-book', 'route' => 'comptabilite.syscohada.index'],
            'compta_tfp'       => ['name' => 'TFP & Apprentissage', 'icon' => 'fas fa-graduation-cap', 'route' => 'comptabilite.tfp.index'],
            'compta_ras'       => ['name' => 'Retenue à la Source', 'icon' => 'fas fa-hand-holding-usd', 'route' => 'comptabilite.retenue-source.index'],
        ]
    ],
    'juridique' => [
        'name' => 'Juridique',
        'icon' => 'fas fa-gavel',
        'submodules' => [
            'juridique_dashboard'   => ['name' => 'Dashboard', 'icon' => 'fas fa-tachometer-alt', 'route' => 'juridique.dashboard'],
            'juridique_documents'   => ['name' => 'Documents', 'icon' => 'fas fa-folder-open', 'route' => 'juridique.documents.index'],
            'juridique_financements'=> ['name' => 'Dossiers de financement', 'icon' => 'fas fa-briefcase', 'route' => 'juridique.financements.index'],
            'juridique_offres'      => ['name' => 'Offres bancaires', 'icon' => 'fas fa-university', 'route' => 'juridique.offres.index'],
            'juridique_echeances'   => ['name' => 'Échéanciers', 'icon' => 'fas fa-calendar-alt', 'route' => 'juridique.echeances.index'],
        ]
    ],
    'rh' => [
        'name' => 'RH',
        'icon' => 'fas fa-users-cog',
        'submodules' => [
            'rh_dashboard' => ['name' => 'Tableau de bord', 'icon' => 'fas fa-tachometer-alt', 'url' => '/rh/dashboard'],
            'rh_agents'    => ['name' => 'Personnel', 'icon' => 'fas fa-users', 'url' => '/rh/personnel'],
            'rh_camera_sync' => ['name' => 'Sync Caméra', 'icon' => 'fas fa-camera', 'route' => 'rh.camera-sync.index'],
            'rh_conges'    => ['name' => 'Congés', 'icon' => 'fas fa-calendar-alt', 'url' => '/rh/conges'],
            'rh_pointages' => ['name' => 'Pointage RH manuel', 'icon' => 'fas fa-clock', 'url' => '/rh/pointages'],
            'rh_pointages_camera' => ['name' => 'Pointage caméra Hikvision', 'icon' => 'fas fa-id-card', 'url' => '/rh/facial-pointage/dashboard'],
            'rh_contrats'  => ['name' => 'Contrats', 'icon' => 'fas fa-file-contract', 'route' => 'rh.contrats.index'],
            'rh_paie'      => ['name' => 'Paie', 'icon' => 'fas fa-money-bill', 'route' => 'rh.paie.index'],
        ]
    ],
    'chantier' => [
        'name' => 'Gestion de Chantier',
        'icon' => 'fas fa-hard-hat',
        'submodules' => [
            'chantier_dashboard' => ['name' => 'Dashboard', 'icon' => 'fas fa-tachometer-alt', 'url' => '/projets/dashboard'],
            'chantier_list'      => ['name' => 'Liste des chantiers', 'icon' => 'fas fa-list', 'url' => '/projets/list'],
            'chantier_create'    => ['name' => 'Ouvrir un chantier', 'icon' => 'fas fa-plus', 'url' => '/projets/create'],
            'chantier_cost_control' => ['name' => 'Suivi Cost Control', 'icon' => 'fas fa-coins', 'url' => '/projets/dashboard'],
            'chantier_reports'   => ['name' => 'Rapports', 'icon' => 'fas fa-chart-line', 'url' => '/projets/reports'],
        ]
    ],
    'reporting' => [
        'name' => 'Reporting',
        'icon' => 'fas fa-chart-line',
        'submodules' => [
            'rep_dashboard' => ['name' => 'Dashboard', 'icon' => 'fas fa-tachometer-alt', 'url' => '/reporting/dashboard'],
            'rep_fin'       => ['name' => 'Financier', 'icon' => 'fas fa-dollar-sign', 'url' => '/reporting/financier'],
            'rep_ops'       => ['name' => 'Opérations', 'icon' => 'fas fa-cogs', 'url' => '/reporting/operations'],
            'rep_perf'      => ['name' => 'Performance', 'icon' => 'fas fa-tachometer-alt', 'url' => '/reporting/performance'],
            'rep_services'  => ['name' => 'Par Service', 'icon' => 'fas fa-building', 'url' => '/reporting/services'],
            'rep_export'    => ['name' => 'Export', 'icon' => 'fas fa-download', 'url' => '/reporting/export'],
        ]
    ],
    'audit' => [
        'name' => 'Checking',
        'icon' => 'fas fa-clipboard-check',
        'submodules' => [
            'audit_dashboard'  => ['name' => 'Dashboard', 'icon' => 'fas fa-tachometer-alt', 'url' => '/audit/dashboard'],
            'audit_controles'  => ['name' => 'Contrôles', 'icon' => 'fas fa-search', 'url' => '/audit/controles'],
            'audit_rapports'   => ['name' => 'Rapports', 'icon' => 'fas fa-file-alt', 'url' => '/audit/rapports'],
            'audit_alertes'    => ['name' => 'Alertes', 'icon' => 'fas fa-exclamation-triangle', 'url' => '/audit/alertes'],
            'audit_historique' => ['name' => 'Historique', 'icon' => 'fas fa-history', 'url' => '/audit/historique'],
        ]
    ],
];

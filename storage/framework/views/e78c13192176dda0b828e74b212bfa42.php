<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $__env->yieldContent('title', 'KENAM SERVICES'); ?></title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="<?php echo e(asset('css/fonts.css')); ?>" rel="stylesheet">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- DataTables -->
    <link href="https://cdn.datatables.net/1.13.8/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css" rel="stylesheet">

    <!-- Select2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-0-rc.0/dist/css/select2.min.css" rel="stylesheet">

    <!-- PWA -->
    <meta name="theme-color" content="#16a34a">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="Kenam Services">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <link rel="manifest" href="<?php echo e(asset('manifest.json')); ?>">
    <link rel="apple-touch-icon" href="<?php echo e(asset('icons/icon-192x192.png')); ?>">

    <!--/ Livewire Styles - Désactivé -->
    <!--
    <div style="display: none; position: absolute; left: -9999px; visibility: hidden;">
        @livewireStyles
    </div>
    -->

    <style>
        :root {
            --kenam-orange: #ff6b35;
            --kenam-orange-dark: #e55a2b;
            --kenam-orange-light: #ff8c42;
            --kenam-green: #16a34a;
            --kenam-green-dark: #15803d;
            --kenam-green-light: #22c55e;
            --kenam-white: #ffffff;
            --primary-color: #16a34a;
            --primary-dark: #15803d;
            --primary-light: #22c55e;
            --gray-50: #f8fafc;
            --gray-100: #f1f5f9;
            --gray-200: #e2e8f0;
            --gray-300: #cbd5e1;
            --gray-400: #94a3b8;
            --gray-500: #64748b;
            --gray-600: #475569;
            --gray-700: #334155;
            --gray-800: #1e293b;
            --gray-900: #0f172a;
            --sidebar-width: 72px;
            --sidebar-expanded: 280px;
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-color: var(--gray-50);
            color: var(--gray-800);
            line-height: 1.6;
            overflow-x: hidden;
        }

        /* Sidebar */
        .sidebar-desktop {
            position: fixed;
            top: 0;
            left: 0;
            width: 90px;
            height: 100vh;
            background: linear-gradient(180deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            z-index: 1050;
            transition: var(--transition);
            box-shadow: 4px 0 24px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .sidebar-desktop.expanded {
            width: 300px;
        }

        .sidebar-header {
            padding: 1.5rem 1rem;
            background: rgba(255, 255, 255, 0.1);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            text-align: center;
            backdrop-filter: blur(10px);
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .sidebar-logo {
            max-height: 45px;
            max-width: 90%;
            height: auto;
            width: auto;
            transition: var(--transition);
            display: block;
        }

        /* Effet blanc sur le logo en mode compact */
        .sidebar-desktop:not(.expanded) .sidebar-logo {
            filter: brightness(0) invert(1);
        }

        /* Logo normal en mode étendu */
        .sidebar-desktop.expanded .sidebar-logo {
            filter: none;
        }

        .sidebar-logo:hover {
            transform: scale(1.05);
        }

        .sidebar-menu {
            padding: 1rem 0;
            height: calc(100vh - 100px);
            overflow-y: auto;
            overflow-x: hidden;
            scrollbar-width: thin;
            scrollbar-color: rgba(255, 255, 255, 0.4) transparent;
        }

        /* Scrollbar stylisée - Chrome/Safari/Edge */
        .sidebar-menu::-webkit-scrollbar {
            width: 8px;
        }

        .sidebar-menu::-webkit-scrollbar-track {
            background: transparent;
        }

        .sidebar-menu::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.4);
            border-radius: 4px;
            border: 2px solid transparent;
            background-clip: padding-box;
        }

        .sidebar-menu::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.7);
            background-clip: padding-box;
        }

        .menu-item {
            display: flex;
            align-items: center;
            padding: 1rem 1rem;
            color: white;
            text-decoration: none;
            transition: var(--transition);
            position: relative;
            cursor: pointer;
            border-left: 3px solid transparent;
            font-size: 1rem;
            font-weight: 500;
        }

        .menu-item:hover {
            background: rgba(255, 255, 255, 0.1);
            border-left-color: white;
        }

        .menu-item.active {
            background: rgba(255, 255, 255, 0.2);
            border-left-color: #f59e0b;
        }

        .menu-icon {
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 1.25rem;
            font-size: 1.5rem;
            transition: var(--transition);
        }

        .menu-text {
            white-space: nowrap;
            opacity: 1;
            transition: var(--transition);
        }

        /* Flèche pour les menus avec sous-modules */
        .submenu-arrow {
            margin-left: auto;
            font-size: 0.75rem;
            opacity: 0.7;
            transition: var(--transition);
        }

        .menu-item.has-submenu:hover .submenu-arrow {
            opacity: 1;
            transform: translateX(2px);
        }

        .sidebar-desktop:not(.expanded) .submenu-arrow {
            display: none;
        }

        .sidebar-desktop:not(.expanded) .menu-text {
            opacity: 0;
            visibility: hidden;
        }

        .sidebar-desktop:not(.expanded) .menu-icon {
            margin-right: 0;
        }

        /* Tooltip pour mode compact */
        .sidebar-desktop:not(.expanded) .menu-item::after {
            content: attr(data-tooltip);
            position: absolute;
            left: 100%;
            top: 50%;
            transform: translateY(-50%);
            margin-left: 1rem;
            background: var(--gray-800);
            color: white;
            padding: 0.75rem 1rem;
            border-radius: 0.5rem;
            font-size: 1rem;
            white-space: nowrap;
            opacity: 0;
            pointer-events: none;
            transition: var(--transition);
            z-index: 1000;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .sidebar-desktop:not(.expanded) .menu-item:hover::after {
            opacity: 1;
        }

        /* Sous-modules flottants */
        .floating-submenu {
            position: fixed;
            background: #4ade80;
            border: 1px solid #22c55e;
            border-radius: 0.5rem;
            box-shadow: 0 8px 25px rgba(34, 197, 94, 0.3);
            z-index: 1000;
            padding: 0.25rem;
            min-width: 200px;
            opacity: 0;
            transform: translateY(-8px);
            transition: var(--transition);
            pointer-events: none;
        }

        /* Trésorerie: 4 sous-modules par ligne */
        .floating-submenu#submenu-tresorerie {
            display: grid;
            grid-template-columns: repeat(4, minmax(180px, 1fr));
            gap: 0.25rem;
            padding: 0.5rem;
            width: 960px;
            min-width: 960px;
            max-width: 960px;
        }

        /* Comptabilité: conserver la grille actuelle */
        .floating-submenu#submenu-comptabilite {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 0.25rem;
            padding: 0.5rem;
            width: 720px;
            min-width: 720px;
            max-width: 720px;
        }

        .floating-submenu#submenu-tresorerie .submenu-link {
            min-width: 0;
            max-width: none;
            font-size: 0.85rem;
            padding: 0.5rem 0.75rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.25rem;
            text-align: center;
        }

        .floating-submenu#submenu-comptabilite .submenu-link {
            min-width: 220px;
            max-width: 220px;
            font-size: 0.85rem;
            padding: 0.5rem 0.75rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.25rem;
            text-align: center;
        }

        .floating-submenu#submenu-tresorerie .submenu-link i,
        .floating-submenu#submenu-comptabilite .submenu-link i {
            margin-right: 0;
            font-size: 1rem;
        }

        .floating-submenu#submenu-tresorerie .submenu-divider,
        .floating-submenu#submenu-comptabilite .submenu-divider {
            grid-column: 1 / -1;
            margin: 0.25rem 0;
        }

        /* Sous-menu Paramètres - Position au-dessus des cartes, horizontal par défaut */
        .floating-submenu#submenu-settings {
            background: linear-gradient(135deg, #3b82f6, #1d4ed8);
            border: 1px solid #1e40af;
            box-shadow: 0 8px 25px rgba(59, 130, 246, 0.4);
            z-index: 1001;
            margin-top: -100px; /* Réduit de -120px à -100px */
            /* Layout horizontal par défaut */
            display: flex;
            flex-wrap: wrap;
            gap: 0.25rem;
            padding: 0.5rem;
            min-width: auto;
            max-width: 550px;
        }

        /* Header du sous-menu Paramètres */
        .floating-submenu#submenu-settings .submenu-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.25rem 0.5rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
            margin-bottom: 0.25rem;
            width: 100%;
        }

        .floating-submenu#submenu-settings .submenu-title {
            color: white;
            font-weight: 600;
            font-size: 0.75rem;
        }

        .floating-submenu#submenu-settings .submenu-toggle {
            background: rgba(255, 255, 255, 0.2);
            border: none;
            color: white;
            padding: 0.125rem 0.25rem;
            border-radius: 0.25rem;
            cursor: pointer;
            font-size: 0.625rem;
            transition: background 0.2s;
        }

        .floating-submenu#submenu-settings .submenu-toggle:hover {
            background: rgba(255, 255, 255, 0.3);
        }

        /* Contenu du sous-menu Paramètres (horizontal) */
        .floating-submenu#submenu-settings .submenu-content {
            display: flex;
            flex-wrap: wrap;
            gap: 0.25rem;
        }

        .floating-submenu#submenu-settings .submenu-link {
            padding: 0.375rem 0.5rem;
            font-size: 0.75rem;
            flex: 1;
            min-width: 100px;
            text-align: center;
        }

        .floating-submenu#submenu-settings .submenu-link i {
            margin-right: 0.125rem;
        }

        .floating-submenu#submenu-settings .submenu-link span {
            display: none;
        }

        /* Afficher les textes en hover pour le mode horizontal */
        .floating-submenu#submenu-settings .submenu-link:hover span {
            display: inline;
            position: absolute;
            bottom: -18px;
            left: 50%;
            transform: translateX(-50%);
            background: rgba(0,0,0,0.8);
            color: white;
            padding: 1px 4px;
            border-radius: 2px;
            font-size: 0.6875rem;
            white-space: nowrap;
            z-index: 1002;
        }

        /* Mode vertical (quand on clique sur le toggle) */
        .floating-submenu#submenu-settings.vertical {
            display: block;
            min-width: 200px;
            max-width: none;
        }

        .floating-submenu#submenu-settings.vertical .submenu-header {
            margin-bottom: 0.25rem;
        }

        .floating-submenu#submenu-settings.vertical .submenu-content {
            display: block;
            gap: 0;
        }

        .floating-submenu#submenu-settings.vertical .submenu-link {
            display: flex;
            align-items: center;
            padding: 0.5rem 0.75rem;
            flex: none;
            min-width: auto;
            text-align: left;
        }

        .floating-submenu#submenu-settings.vertical .submenu-link i {
            margin-right: 0.5rem;
        }

        .floating-submenu#submenu-settings.vertical .submenu-link span {
            display: inline;
        }

        .floating-submenu#submenu-settings.vertical .submenu-link:hover span {
            position: static;
            background: none;
            color: white;
            padding: 0;
            border-radius: 0;
            font-size: inherit;
            white-space: normal;
            transform: none;
            bottom: auto;
            left: auto;
        }

        /* Positionnement du Profil - au-dessus de Paramètres */
        .floating-submenu#submenu-profile {
            margin-top: -150px; /* Réduit de -180px à -150px */
            z-index: 1002;
        }

        /* Positionnement des autres sous-menus - en dessous de Paramètres */
        .floating-submenu:not(#submenu-settings):not(#submenu-profile) {
            margin-top: 15px; /* Réduit de 20px à 15px */
            z-index: 999; /* Moins que Paramètres et Profil */
        }

        .floating-submenu.show {
            opacity: 1;
            transform: translateY(0);
            pointer-events: auto;
        }

        .submenu-link {
            display: flex;
            align-items: center;
            padding: 0.5rem 0.75rem;
            color: white;
            text-decoration: none;
            border-radius: 0.375rem;
            transition: all 0.2s ease;
            font-size: 0.875rem;
        }

        .submenu-link:hover {
            background: #ea580c;
            color: white;
            transform: translateX(2px);
            border-color: rgba(255, 255, 255, 0.3);
            box-shadow: 0 4px 12px rgba(234, 88, 12, 0.4);
        }

        .submenu-link i {
            width: 16px;
            text-align: center;
            font-size: 0.875rem;
            color: rgba(255, 255, 255, 0.9);
            transition: var(--transition);
        }

        .submenu-link:hover i {
            color: white;
            transform: scale(1.1);
        }

        .submenu-link span {
            flex: 1;
            font-weight: 500;
        }

        /* Header */
        .header {
            position: fixed;
            top: 0;
            left: 90px;
            right: 0;
            height: 50px;
            background: white;
            border-bottom: 1px solid var(--gray-200);
            z-index: 1040;
            transition: var(--transition);
            display: flex;
            align-items: center;
            padding: 0 2rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .sidebar-desktop.expanded ~ .main-content .header {
            left: 300px;
        }

        .header-content {
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
        }

        .header-title {
            font-size: 1.15rem;
            font-weight: 600;
            color: var(--gray-700);
            flex: 1;
        }

        .header-actions {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-left: auto;
        }

        /* Main Content */
        .main-content {
            margin-left: 90px;
            padding-top: 70px;
            min-height: 100vh;
            transition: var(--transition);
            width: calc(100% - 90px);
            overflow-y: auto;
        }

        .sidebar-desktop.expanded ~ .main-content {
            margin-left: 300px;
            width: calc(100% - 300px);
        }

        .content-wrapper {
            width: 100%;
            margin: 0;
            padding: 0.5rem;
            min-height: calc(100vh - 70px);
            max-width: calc(100vw - 90px);
        }

        .sidebar-desktop.expanded ~ .main-content .content-wrapper {
            max-width: calc(100vw - 300px);
        }

        /* Conteneurs pleine largeur */
        .container-fluid,
        .container {
            width: 100% !important;
            max-width: 100% !important;
            padding-left: 0.5rem !important;
            padding-right: 0.5rem !important;
        }

        /* Assurer que les cards utilisent la pleine largeur */
        .card {
            width: 100%;
        }

        /* Responsive pour les écrans plus petits */
        @media (max-width: 1400px) {
            .content-wrapper,
            .container-fluid,
            .container {
                max-width: 100%;
                padding: 0.25rem !important;
            }

            .main-content {
                padding-top: 60px;
            }
        }

        @media (max-width: 1200px) {
            .sidebar-desktop.expanded ~ .main-content .content-wrapper {
                max-width: calc(100vw - 90px);
            }
        }

        /* Cards */
        .card {
            border: none;
            border-radius: 1rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            transition: var(--transition);
            background: white;
            overflow: hidden;
            margin-bottom: 1.5rem;
        }

        .card:hover {
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
            transform: translateY(-2px);
        }

        .card-header {
            background: var(--primary-color);
            color: white;
            border: none;
            padding: 1.25rem;
            font-weight: 600;
        }

        .card-body {
            padding: 1.5rem;
        }

        /* Buttons */
        .btn {
            border-radius: 0.5rem;
            font-weight: 500;
            padding: 0.625rem 1.25rem;
            transition: var(--transition);
            border: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-primary {
            background: var(--primary-color);
            color: white;
        }

        .btn-primary:hover {
            background: var(--primary-dark);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(22, 163, 74, 0.3);
        }

        /* Forms */
        .form-control {
            border: 2px solid var(--gray-200);
            border-radius: 0.5rem;
            padding: 0.75rem 1rem;
            transition: var(--transition);
            font-size: 0.875rem;
        }

        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(22, 163, 74, 0.1);
            outline: none;
        }

        .form-label {
            font-weight: 600;
            color: var(--gray-700);
            margin-bottom: 0.5rem;
        }

        /* Alerts */
        .alert {
            border: none;
            border-radius: 0.5rem;
            padding: 1rem 1.5rem;
            margin-bottom: 1.5rem;
        }

        .alert-success {
            background: #10b981;
            color: white;
        }

        .alert-danger {
            background: #ef4444;
            color: white;
        }

        .alert-warning {
            background: #f59e0b;
            color: white;
        }

        /* Tables */
        .table {
            border-radius: 0.5rem;
            overflow: hidden;
        }

        .table thead {
            background: var(--gray-50);
        }

        .table th {
            font-weight: 600;
            color: var(--gray-700);
            border: none;
            padding: 1rem;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .sidebar-desktop {
                transform: translateX(-100%);
            }

            .sidebar-desktop.show {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
                width: 100%;
                padding-top: 50px;
            }

            .header {
                left: 0;
                width: 100%;
                height: 50px;
            }

            .content-wrapper {
                padding: 1rem;
                max-width: 100%;
            }
        }

        /* Navigation Header Styles */
        .navbar-custom {
            background: linear-gradient(135deg, #ff8c00 0%, #ffa726 100%);
            border-bottom: 2px solid #ff6f00;
            box-shadow: 0 4px 20px rgba(255, 140, 0, 0.3);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
        }

        /* Animation pour la cloche de notification */
        @keyframes ring {
            0% { transform: rotate(0); }
            10% { transform: rotate(15deg); }
            20% { transform: rotate(-10deg); }
            30% { transform: rotate(10deg); }
            40% { transform: rotate(-8deg); }
            50% { transform: rotate(6deg); }
            60% { transform: rotate(-4deg); }
            70% { transform: rotate(2deg); }
            80% { transform: rotate(-1deg); }
            90% { transform: rotate(1deg); }
            100% { transform: rotate(0); }
        }

        .fa-bell.text-warning {
            display: inline-block;
            animation: ring 2s ease infinite;
            transform-origin: top center;
        }

        /* Style pour le bouton App Mobile */
        .mobile-app-btn {
            background: rgba(255, 255, 255, 0.15) !important;
            border: 1px solid rgba(255, 255, 255, 0.3) !important;
            color: #ffffff !important;
            border-radius: 20px !important;
            padding: 8px 16px !important;
        }

        .mobile-app-btn i {
            animation: pulse-mobile 2s infinite;
        }

        @keyframes pulse-mobile {
            0% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.1); opacity: 0.8; }
            100% { transform: scale(1); opacity: 1; }
        }

        .mobile-app-btn:hover {
            background: rgba(255, 255, 255, 0.25) !important;
            box-shadow: 0 0 15px rgba(255, 255, 255, 0.2);
        }

        .nav-button {
            background: transparent;
            border: 2px solid rgba(255, 255, 255, 0.8);
            color: white;
            padding: 10px 18px;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
            margin: 0 6px;
            position: relative;
            overflow: hidden;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .nav-button:hover {
            background: rgba(255, 255, 255, 0.1);
            border-color: rgba(255, 255, 255, 1);
            transform: translateY(-1px);
            box-shadow: 0 4px 15px rgba(255, 255, 255, 0.2);
        }

        .nav-button:active {
            transform: translateY(0);
            box-shadow: 0 2px 8px rgba(255, 255, 255, 0.15);
        }

        .dropdown-menu {
            background: rgba(255, 140, 0, 0.95);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 8px;
            box-shadow: 0 20px 60px rgba(255, 140, 0, 0.4);
        }

        .dropdown-item {
            color: #ffffff;
            padding: 12px 20px;
            transition: all 0.3s ease;
        }

        .dropdown-item:hover {
            background: rgba(255, 255, 255, 0.15);
            color: #ffffff;
        }

        .dropdown-divider {
            border-color: rgba(255, 255, 255, 0.2);
        }

        /* Cacher le contenu parasite de Livewire */
        [data-livewire-style],
        [wire\:style],
        [wire\:target],
        [wire\:loading],
        [wire\:key] {
            display: none !important;
        }

        /* Cacher les scripts Livewire qui s'affichent */
        script[data-livewire] {
            display: none !important;
        }

        /* Cacher tout contenu parasite qui pourrait s'afficher */
        .livewire-style-container,
        .livewire-component,
        .livewire-update,
        [wire\:id],
        [wire\:init],
        [wire\:loading],
        [wire\:offline],
        [wire\:dirty],
        [wire\:polling],
        [wire\:ignore],
        [wire\:model],
        [wire\:key],
        [wire\:target],
        [wire\:style],
        [data-livewire],
        [data-wire-id],
        [data-wire\:init],
        [data-wire\:loading],
        [data-wire\:offline],
        [data-wire\:dirty],
        [data-wire\:polling],
        [data-wire\:ignore],
        [data-wire\:model],
        [data-wire\:key],
        [data-wire\:target],
        [data-wire\:style] {
            display: none !important;
            visibility: hidden !important;
            position: absolute !important;
            left: -9999px !important;
            top: -9999px !important;
            width: 0 !important;
            height: 0 !important;
            overflow: hidden !important;
            opacity: 0 !important;
            pointer-events: none !important;
        }

        /* Cacher les scripts Livewire qui s'affichent */
        script[data-livewire],
        script[wire\:id],
        script[wire\:init],
        script[wire\:loading],
        script[wire\:offline],
        script[wire\:dirty],
        script[wire\:polling],
        script[wire\:ignore],
        script[wire\:model],
        script[wire\:key],
        script[wire\:target],
        script[wire\:style],
        script[data-wire-id],
        script[data-wire\:init],
        script[data-wire\:loading],
        script[data-wire\:offline],
        script[data-wire\:dirty],
        script[data-wire\:polling],
        script[data-wire\:ignore],
        [data-wire\:model],
        [data-wire\:key],
        [data-wire\:target],
        [data-wire\:style] {
            display: none !important;
            visibility: hidden !important;
        }

        /* Cacher tout texte parasite qui pourrait s'afficher */
        body > text,
        body > comment,
        body > style:not(:first-of-type),
        body > script:not(:first-of-type) {
            display: none !important;
        }

        /* Cacher spécifiquement le contenu parasite de Livewire */
        .livewire-styles,
        .livewire-styles *,
        .livewire-styles:before,
        .livewire-styles:after {
            display: none !important;
            content: none !important;
        }
    </style>
</head>

<body>
    <?php echo $__env->make('layouts.navigation', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php echo $__env->make('layouts.sidebar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <main class="main-content">
        <div class="content-wrapper">
            <?php if(session('status')): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?php echo e(session('status')); ?>

                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <?php if(session('warning')): ?>
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <?php echo e(session('warning')); ?>

                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <?php if($errors->any()): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <ul class="mb-0">
                        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li><?php echo e($error); ?></li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <?php echo $__env->yieldContent('content'); ?>
        </div>
    </main>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.8/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

    <!-- Scripts principaux -->
    <script>
        // Configuration CSRF pour les requêtes AJAX
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Initialisation des tooltips
        document.addEventListener('DOMContentLoaded', function() {
            // Initialiser les tooltips Bootstrap si disponibles
            if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
                var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
                var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                    return new bootstrap.Tooltip(tooltipTriggerEl)
                });
            }
        });

        window.addEventListener('pageshow', function(event) {
            if (event.persisted) {
                window.location.reload();
            }
        });
    </script>
    <script src="<?php echo e(asset('js/sidebar-submenu.js')); ?>"></script>
    <script src="<?php echo e(asset('js/sidebar-overlay.js')); ?>"></script>

    <!-- Livewire Scripts - Désactivé -->
    <!--
    <script style="display: none;">
        @livewireScripts
    </script>
    -->

    <?php echo $__env->yieldPushContent('scripts'); ?>
    <script>
        (function () {
            if (window.__KENAM_GLOBAL_SMART_FILTER_INIT__) {
                return;
            }
            window.__KENAM_GLOBAL_SMART_FILTER_INIT__ = true;

            function normalizeText(value) {
                return (value || '')
                    .toString()
                    .normalize('NFD')
                    .replace(/[\u0300-\u036f]/g, '')
                    .toLowerCase()
                    .replace(/\s+/g, ' ')
                    .trim();
            }

            function parseTokens(value) {
                var tokens = [];
                var input = value || '';
                var pattern = /"([^"]+)"|(\S+)/g;
                var match;

                while ((match = pattern.exec(input)) !== null) {
                    var raw = match[1] || match[2] || '';
                    var token = normalizeText(raw);
                    if (!token) {
                        continue;
                    }

                    if (token.charAt(0) === '-' && token.length > 1) {
                        tokens.push({ type: 'exclude', value: token.substring(1) });
                    } else {
                        tokens.push({ type: 'include', value: token });
                    }
                }

                return tokens;
            }

            function findBestTable(input) {
                var scope = input.closest('.card, .container-fluid, .content-wrapper, main, body') || document.body;
                var tables = Array.prototype.slice.call(scope.querySelectorAll('table'));
                tables = tables.filter(function (table) {
                    return table.querySelectorAll('tbody tr').length > 0;
                });

                if (!tables.length) {
                    return null;
                }

                var afterInput = tables.filter(function (table) {
                    return Boolean(input.compareDocumentPosition(table) & Node.DOCUMENT_POSITION_FOLLOWING);
                });

                return afterInput[0] || tables[0];
            }

            function bindSmartFilter(input) {
                if (!input || input.dataset.smartFilterBound === '1') {
                    return;
                }

                if (input.closest('.smart-list-layout')) {
                    // Deja gere par le composant list-layout.
                    return;
                }

                var table = findBestTable(input);
                if (!table) {
                    return;
                }

                var rows = Array.prototype.slice.call(table.querySelectorAll('tbody tr'));
                if (!rows.length) {
                    return;
                }

                input.dataset.smartFilterBound = '1';

                function escapeRegex(value) {
                    return value.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
                }

                function applyToDataTable(tokens) {
                    if (typeof window.jQuery === 'undefined' || !window.jQuery.fn || !window.jQuery.fn.dataTable) {
                        return false;
                    }

                    var $ = window.jQuery;
                    if (!$.fn.dataTable.isDataTable(table)) {
                        return false;
                    }

                    var dt = $(table).DataTable();
                    if (!tokens.length) {
                        dt.search('').draw();
                        return true;
                    }

                    var includePatterns = tokens
                        .filter(function (token) { return token.type === 'include'; })
                        .map(function (token) { return '(?=.*' + escapeRegex(token.value) + ')'; })
                        .join('');

                    var excludePatterns = tokens
                        .filter(function (token) { return token.type === 'exclude'; })
                        .map(function (token) { return '(?!.*' + escapeRegex(token.value) + ')'; })
                        .join('');

                    var regex = '^' + excludePatterns + includePatterns + '.*$';
                    dt.search(regex, true, false).draw();

                    return true;
                }

                var run = function () {
                    var tokens = parseTokens(input.value);

                    if (applyToDataTable(tokens)) {
                        return;
                    }

                    rows.forEach(function (row) {
                        var haystack = normalizeText(row.innerText || row.textContent || '');
                        var includeOk = tokens
                            .filter(function (token) { return token.type === 'include'; })
                            .every(function (token) { return haystack.indexOf(token.value) !== -1; });
                        var excludeOk = tokens
                            .filter(function (token) { return token.type === 'exclude'; })
                            .every(function (token) { return haystack.indexOf(token.value) === -1; });

                        row.style.display = includeOk && excludeOk ? '' : 'none';
                    });
                };

                var timer = null;
                input.addEventListener('input', function () {
                    if (timer) {
                        clearTimeout(timer);
                    }
                    timer = setTimeout(run, 120);
                });

                run();
            }

            function discoverAndBind() {
                var selector = [
                    'form[method="GET"] input[type="search"]',
                    'form[method="GET"] input[type="text"]',
                    'form input[name="search"]',
                    'form input[name="q"]',
                    'form input[name="keyword"]',
                    'form input[name="recherche"]',
                    'form input[id="search"]',
                    'form input[id*="search"]',
                    'form input[placeholder*="Rechercher"]',
                    'form input[placeholder*="rechercher"]',
                    'input.js-global-smart-filter'
                ].join(', ');

                Array.prototype.slice.call(document.querySelectorAll(selector)).forEach(function (input) {
                    if (!input || input.type === 'hidden' || input.type === 'date' || input.type === 'number') {
                        return;
                    }

                    bindSmartFilter(input);
                });
            }

            document.addEventListener('DOMContentLoaded', discoverAndBind);

            var observer = new MutationObserver(function () {
                discoverAndBind();
            });

            observer.observe(document.body, {
                childList: true,
                subtree: true
            });
        })();
    </script>
    <script>
        // Enregistrement du Service Worker pour la PWA
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function() {
                navigator.serviceWorker.register('/sw.js').then(function(registration) {
                    console.log('ServiceWorker registration successful with scope: ', registration.scope);
                }, function(err) {
                    console.log('ServiceWorker registration failed: ', err);
                });
            });
        }
    </script>
    <!-- Modal de prévisualisation d'image -->
    <div class="modal fade" id="imagePreviewModal" tabindex="-1" aria-hidden="true" style="z-index: 2000;">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content bg-transparent border-0 shadow-none">
                <div class="modal-body p-0 position-relative text-center">
                    <button type="button" class="btn-close btn-close-white position-absolute top-0 end-0 m-3" data-bs-dismiss="modal" aria-label="Close" style="z-index: 2010;"></button>
                    <div class="bg-white p-2 rounded shadow-lg d-inline-block">
                        <img id="previewImage" src="" class="img-fluid rounded" style="max-height: 80vh; min-width: 200px;">
                    </div>
                    <div class="mt-4 d-flex justify-content-center gap-3">
                        <a id="downloadImageBtn" href="#" download class="btn btn-light btn-lg rounded-pill shadow">
                            <i class="fas fa-download me-2"></i>Télécharger
                        </a>
                        <a id="convertToPdfBtn" href="#" class="btn btn-danger btn-lg rounded-pill shadow">
                            <i class="fas fa-file-pdf me-2"></i>Version PDF
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const imagePreviewModal = document.getElementById('imagePreviewModal');
            const previewImage = document.getElementById('previewImage');
            const downloadBtn = document.getElementById('downloadImageBtn');
            const pdfBtn = document.getElementById('convertToPdfBtn');
            const bsModal = new bootstrap.Modal(imagePreviewModal);

            document.addEventListener('click', function(e) {
                const trigger = e.target.closest('.image-preview-trigger');
                if (trigger) {
                    e.preventDefault();
                    const src = trigger.getAttribute('href');
                    const fileId = trigger.getAttribute('data-file-id');
                    const fileName = trigger.getAttribute('data-file-name');

                    previewImage.src = src;
                    downloadBtn.href = src;
                    downloadBtn.setAttribute('download', fileName || 'image.jpg');

                    if (fileId) {
                        // Construire l'URL de conversion PDF dynamiquement
                        let pdfUrl = "<?php echo e(route('operations.files.download-as-pdf', ':id')); ?>";
                        pdfUrl = pdfUrl.replace(':id', fileId);
                        pdfBtn.href = pdfUrl;
                        pdfBtn.style.display = 'inline-block';
                    } else {
                        pdfBtn.style.display = 'none';
                    }

                    bsModal.show();
                }
            });
        });
    </script>
</body>
</html>

<?php /**PATH C:\laragon\www\kenam\resources\views/layouts/app.blade.php ENDPATH**/ ?>
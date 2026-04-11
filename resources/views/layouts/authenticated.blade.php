<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'KENAM SERVICES') - Tableau de Bord</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="{{ asset('css/fonts.css') }}" rel="stylesheet">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- FontAwesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <style>
        :root {
            --primary-color: #16a34a;
            --secondary-color: #64748b;
            --sidebar-width: 280px;
            --header-height: 70px;
            --font-primary: 'Inter', sans-serif;
        }

        body {
            font-family: var(--font-primary);
            background: #f8fafc;
            margin: 0;
            padding: 0;
        }

        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: var(--sidebar-width);
            height: 100vh;
            background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
            color: white;
            overflow-y: auto;
            z-index: 1000;
            transition: transform 0.3s ease;
        }

        .sidebar-header {
            padding: 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            text-align: center;
        }

        .sidebar-logo {
            max-height: 50px;
            margin-bottom: 10px;
        }

        .sidebar-user {
            padding: 15px 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            font-size: 0.9rem;
        }

        .sidebar-nav {
            padding: 20px 0;
        }

        .nav-section {
            margin-bottom: 30px;
        }

        .nav-section-title {
            padding: 5px 20px;
            font-size: 0.75rem;
            text-transform: uppercase;
            color: #94a3b8;
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        .nav-link {
            display: flex;
            align-items: center;
            padding: 12px 20px;
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            transition: all 0.3s ease;
            border-left: 3px solid transparent;
        }

        .nav-link:hover {
            background: rgba(255, 255, 255, 0.05);
            color: white;
            text-decoration: none;
        }

        .nav-link.active {
            background: rgba(255, 255, 255, 0.1);
            color: white;
            border-left-color: var(--primary-color);
        }

        .nav-link.disabled {
            color: rgba(255, 255, 255, 0.3);
            cursor: not-allowed;
            opacity: 0.5;
        }

        .nav-link.disabled:hover {
            background: transparent;
            color: rgba(255, 255, 255, 0.3);
        }

        .nav-link i {
            width: 20px;
            margin-right: 12px;
            font-size: 1rem;
        }

        .main-content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            transition: margin-left 0.3s ease;
        }

        .top-header {
            background: white;
            height: var(--header-height);
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 30px;
            position: sticky;
            top: 0;
            z-index: 999;
        }

        .header-title {
            font-size: 1.5rem;
            font-weight: 700;
            color: #1e293b;
            margin: 0;
        }

        .header-actions {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .user-dropdown {
            position: relative;
        }

        .user-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: white;
            color: #1e40af;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .user-initials {
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .user-avatar:hover {
            transform: scale(1.05);
        }

        .content-area {
            padding: 30px;
        }

        .permission-badge {
            font-size: 0.6rem;
            padding: 2px 6px;
            border-radius: 10px;
            background: rgba(255, 255, 255, 0.2);
            margin-left: 8px;
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-100%);
            }

            .sidebar.show {
                transform: translateX(0);
            }

            .main-content {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-header">
            <img src="{{ asset('images/logo-kenam.png') }}" alt="KENAM SERVICES" class="sidebar-logo">
            <div class="text-white fw-bold">KENAM SERVICES</div>
            <div class="text-white-50 small">Système de Gestion</div>
        </div>

        <div class="sidebar-user">
            <div class="d-flex align-items-center">
                <div class="user-avatar me-3">
                    {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                </div>
                <div>
                    <div class="fw-bold">{{ auth()->user()->name }}</div>
                </div>
            </div>
        </div>

        <nav class="sidebar-nav">
            <!-- Pilotage -->
            <div class="nav-section">
                <div class="nav-section-title">Pilotage</div>

                @if(auth()->user()->canAccessModule('dashboard'))
                    <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <i class="fas fa-tachometer-alt"></i>
                        Tableau de Bord
                    </a>
                @else
                    <div class="nav-link disabled">
                        <i class="fas fa-tachometer-alt"></i>
                        Tableau de Bord
                        <span class="permission-badge">Non autorisé</span>
                    </div>
                @endif
            </div>

            <!-- Exécution & Processus -->
            <div class="nav-section">
                <div class="nav-section-title">Exécution & Processus</div>

                @if(auth()->user()->canAccessModule('operations'))
                    <a href="{{ url('/operations') }}" class="nav-link {{ request()->is('operations*') ? 'active' : '' }}">
                        <i class="fas fa-tasks"></i>
                        Requêtes
                    </a>
                @else
                    <div class="nav-link disabled">
                        <i class="fas fa-tasks"></i>
                        Requêtes
                        <span class="permission-badge">Non autorisé</span>
                    </div>
                @endif
            </div>

            <!-- Logistique & Stocks -->
            <div class="nav-section">
                <div class="nav-section-title">Logistique & Stocks</div>


                @if(auth()->user()->canAccessModule('fleet'))
                    <a href="{{ url('/fleet') }}" class="nav-link {{ request()->is('fleet*') ? 'active' : '' }}">
                        <i class="fas fa-car"></i>
                        Parc Auto
                    </a>
                @else
                    <div class="nav-link disabled">
                        <i class="fas fa-car"></i>
                        Parc Auto
                        <span class="permission-badge">Non autorisé</span>
                    </div>
                @endif

            </div>

            <!-- Finance & Clientèle -->
            <div class="nav-section">
                <div class="nav-section-title">Finance & Clientèle</div>

                <!-- Commercial -->
                @if(auth()->user()->canAccessModule('commercial'))
                    <div class="nav-link nav-link-parent {{ request()->is('commercial*') ? 'active' : '' }}">
                        <i class="fas fa-handshake"></i>
                        Commercial
                        <i class="fas fa-chevron-down ms-2"></i>
                    </div>
                    <div style="padding-left: 1.5rem;">
                        <a href="{{ url('/commercial/dashboard') }}" class="nav-link {{ request()->is('commercial/dashboard*') ? 'active' : '' }}">
                            <i class="fas fa-chart-line"></i> Tableau de bord
                        </a>
                        <a href="{{ url('/commercial/clients') }}" class="nav-link {{ request()->is('commercial/clients*') ? 'active' : '' }}">
                            <i class="fas fa-user-tie"></i> Clients
                        </a>
                        <a href="{{ url('/commercial/prospects') }}" class="nav-link {{ request()->is('commercial/prospects*') ? 'active' : '' }}">
                            <i class="fas fa-user-plus"></i> Prospects
                        </a>
                        <a href="{{ url('/commercial/devis') }}" class="nav-link {{ request()->is('commercial/devis*') ? 'active' : '' }}">
                            <i class="fas fa-file-invoice"></i> Devis
                        </a>
                        <a href="{{ url('/commercial/commandes') }}" class="nav-link {{ request()->is('commercial/commandes*') ? 'active' : '' }}">
                            <i class="fas fa-shopping-cart"></i> Commandes
                        </a>
                    </div>
                @else
                    <div class="nav-link disabled">
                        <i class="fas fa-handshake"></i>
                        Commercial
                        <span class="permission-badge">Non autorisé</span>
                    </div>
                @endif

                <!-- Trésorerie -->
                @if(auth()->user()->canAccessModule('tresorerie'))
                    <div class="nav-link nav-link-parent {{ request()->is('tresorerie*') ? 'active' : '' }}">
                        <i class="fas fa-piggy-bank"></i>
                        Trésorerie
                        <i class="fas fa-chevron-down ms-2"></i>
                    </div>
                    <div style="padding-left: 1.5rem;">
                        <a href="{{ url('/tresorerie/banque') }}" class="nav-link {{ request()->is('tresorerie/banque*') ? 'active' : '' }}">
                            <i class="fas fa-university"></i> Banque
                        </a>
                        <a href="{{ url('/tresorerie/avances') }}" class="nav-link {{ request()->is('tresorerie/avances*') ? 'active' : '' }}">
                            <i class="fas fa-hand-holding-usd"></i> Acomptes
                        </a>
                    </div>
                @else
                    <div class="nav-link disabled">
                        <i class="fas fa-piggy-bank"></i>
                        Trésorerie
                        <span class="permission-badge">Non autorisé</span>
                    </div>
                @endif


                <!-- Comptabilité -->
                @if(auth()->user()->canAccessModule('accounting'))
                    <a href="{{ url('/comptabilite') }}" class="nav-link {{ request()->is('comptabilite*') ? 'active' : '' }}">
                        <i class="fas fa-calculator"></i>
                        Comptabilité
                    </a>
                @else
                    <div class="nav-link disabled">
                        <i class="fas fa-calculator"></i>
                        Comptabilité
                        <span class="permission-badge">Non autorisé</span>
                    </div>
                @endif

            </div>

            <!-- Fournisseurs -->
            <div class="nav-section">
                <div class="nav-section-title">Fournisseurs</div>

                <!-- Création -->
                @if(auth()->user()->canAccessModule('fournisseurs'))
                    <div class="nav-link nav-link-parent">
                        <i class="fas fa-plus-circle"></i>
                        Création
                        <i class="fas fa-chevron-down ms-2"></i>
                    </div>
                    <div style="padding-left: 1.5rem;">
                        <a href="{{ url('/fournisseurs/create-engins') }}" class="nav-link {{ request()->is('fournisseurs/create-engins*') ? 'active' : '' }}">
                            <i class="fas fa-cogs"></i> Nouveau Fournisseur ENGIN
                        </a>
                        <a href="{{ url('/fournisseurs/create') }}" class="nav-link {{ request()->is('fournisseurs/create*') ? 'active' : '' }}">
                            <i class="fas fa-box"></i> Nouveau Fournisseur MATÉRIEL
                        </a>
                    </div>
                @else
                    <div class="nav-link disabled">
                        <i class="fas fa-plus-circle"></i>
                        Création
                        <span class="permission-badge">Non autorisé</span>
                    </div>
                @endif

                <!-- Fournisseurs Engin -->
                @if(auth()->user()->canAccessModule('fournisseurs'))
                    <div class="nav-link nav-link-parent {{ request()->is('fournisseurs/engins/*') ? 'active' : '' }}">
                        <i class="fas fa-truck"></i>
                        Fournisseurs ENGIN
                        <i class="fas fa-chevron-down ms-2"></i>
                    </div>
                    <div style="padding-left: 1.5rem;">
                        <a href="{{ url('/fournisseurs/engins/kenam') }}" class="nav-link {{ request()->is('fournisseurs/engins/kenam*') ? 'active' : '' }}">
                            <i class="fas fa-building"></i> Fournisseurs Internes
                        </a>
                        <a href="{{ url('/fournisseurs/engins/list') }}" class="nav-link {{ request()->is('fournisseurs/engins/list*') ? 'active' : '' }}">
                            <i class="fas fa-truck"></i> Fournisseurs Externes
                        </a>
                    </div>
                @else
                    <div class="nav-link disabled">
                        <i class="fas fa-truck"></i>
                        Fournisseurs ENGIN
                        <span class="permission-badge">Non autorisé</span>
                    </div>
                @endif

                <!-- Fournisseurs Matériel -->
                @if(auth()->user()->canAccessModule('fournisseurs'))
                    <div class="nav-link nav-link-parent {{ request()->is('fournisseurs/magasin*') || request()->is('fournisseurs/entrepots*') ? 'active' : '' }}">
                        <i class="fas fa-box"></i>
                        Fournisseurs MATÉRIEL
                        <i class="fas fa-chevron-down ms-2"></i>
                    </div>
                    <div style="padding-left: 1.5rem;">
                        <a href="{{ url('/fournisseurs/entrepots') }}" class="nav-link {{ request()->is('fournisseurs/entrepots*') ? 'active' : '' }}">
                            <i class="fas fa-warehouse"></i> Fournisseurs Entrepôts
                        </a>
                        <a href="{{ url('/fournisseurs/magasin') }}" class="nav-link {{ request()->is('fournisseurs/magasin*') ? 'active' : '' }}">
                            <i class="fas fa-store"></i> Fournisseurs Magasin
                        </a>
                    </div>
                @else
                    <div class="nav-link disabled">
                        <i class="fas fa-box"></i>
                        Fournisseurs MATÉRIEL
                        <span class="permission-badge">Non autorisé</span>
                    </div>
                @endif
            </div>

            <!-- Stock & Magasins -->
            <div class="nav-section">
                <div class="nav-section-title">Stock & Magasins</div>

                <!-- Gestion des Stocks -->
                @if(auth()->user()->canAccessModule('magasin'))
                    <div class="nav-link nav-link-parent {{ request()->is('magasin*') || request()->is('entrepot*') ? 'active' : '' }}">
                        <i class="fas fa-boxes"></i>
                        Gestion des Stocks
                        <i class="fas fa-chevron-down ms-2"></i>
                    </div>
                    <div style="padding-left: 1.5rem;">
                        <a href="{{ url('/magasin') }}" class="nav-link {{ request()->is('magasin*') ? 'active' : '' }}">
                            <i class="fas fa-store"></i> Magasin
                        </a>
                        <a href="{{ url('/entrepot') }}" class="nav-link {{ request()->is('entrepot*') ? 'active' : '' }}">
                            <i class="fas fa-warehouse"></i> Entrepôts
                        </a>
                    </div>
                @else
                    <div class="nav-link disabled">
                        <i class="fas fa-boxes"></i>
                        Gestion des Stocks
                        <span class="permission-badge">Non autorisé</span>
                    </div>
                @endif

                <!-- Opérations Stock -->
                @if(auth()->user()->canAccessModule('magasin'))
                    <div class="nav-link nav-link-parent">
                        <i class="fas fa-exchange-alt"></i>
                        Opérations Stock
                        <i class="fas fa-chevron-down ms-2"></i>
                    </div>
                    <div style="padding-left: 1.5rem;">
                        <a href="{{ url('/magasin/entrees') }}" class="nav-link">
                            <i class="fas fa-sign-in-alt"></i> Entrées Stock
                        </a>
                        <a href="{{ url('/magasin/sorties') }}" class="nav-link">
                            <i class="fas fa-sign-out-alt"></i> Sorties Stock
                        </a>
                        <a href="{{ url('/magasin/inventaire') }}" class="nav-link">
                            <i class="fas fa-clipboard-list"></i> Inventaire
                        </a>
                        <a href="{{ url('/magasin/rapports') }}" class="nav-link">
                            <i class="fas fa-chart-line"></i> Rapports Stock
                        </a>
                    </div>
                @else
                    <div class="nav-link disabled">
                        <i class="fas fa-exchange-alt"></i>
                        Opérations Stock
                        <span class="permission-badge">Non autorisé</span>
                    </div>
                @endif
            </div>

            <!-- Support & Analyse -->
            <div class="nav-section">
                <div class="nav-section-title">Support & Analyse</div>

                @if(auth()->user()->canAccessModule('hr'))
                    <a href="{{ url('/rh') }}" class="nav-link {{ request()->is('rh*') ? 'active' : '' }}">
                        <i class="fas fa-users"></i>
                        Ressources Humaines
                    </a>
                @else
                    <div class="nav-link disabled">
                        <i class="fas fa-users"></i>
                        Ressources Humaines
                        <span class="permission-badge">Non autorisé</span>
                    </div>
                @endif

                @if(auth()->user()->canAccessModule('projects'))
                    <a href="{{ url('/projets') }}" class="nav-link {{ request()->is('projets*') ? 'active' : '' }}">
                        <i class="fas fa-project-diagram"></i>
                        Gestion des chantiers
                    </a>
                @else
                    <div class="nav-link disabled">
                        <i class="fas fa-project-diagram"></i>
                        Gestion des chantiers
                        <span class="permission-badge">Non autorisé</span>
                    </div>
                @endif

                @if(auth()->user()->canAccessModule('audit'))
                    <a href="{{ url('/controle-audit') }}" class="nav-link {{ request()->is('controle-audit*') ? 'active' : '' }}">
                        <i class="fas fa-search"></i>
                        Contrôle & Audit
                    </a>
                @else
                    <div class="nav-link disabled">
                        <i class="fas fa-search"></i>
                        Contrôle & Audit
                        <span class="permission-badge">Non autorisé</span>
                    </div>
                @endif
            </div>
        </nav>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        <!-- Header -->
        <header class="top-header">
            <div>
                <h1 class="header-title">@yield('header-title', 'Tableau de Bord')</h1>
            </div>

            <div class="header-actions">
                <div class="text-muted small">
                    <i class="fas fa-clock me-1"></i>
                    {{ now()->format('d/m/Y H:i') }}
                </div>

                <div class="user-dropdown">
                    <div class="user-avatar" title="{{ auth()->user()->name }}">
                        {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                    </div>
                </div>
            </div>
        </header>

        <!-- Content Area -->
        <div class="content-area">
            @yield('content')
        </div>
    </main>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    @yield('scripts')
</body>
</html>

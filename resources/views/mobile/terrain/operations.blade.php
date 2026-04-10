<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Mes Opérations - Kenam OPS</title>
    <meta name="theme-color" content="#2563eb">
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #2563eb;
            --success: #16a34a;
            --warning: #f59e0b;
            --danger: #dc2626;
        }
        
        body {
            background: #f8fafc;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            padding-bottom: 80px;
        }
        
        .mobile-container {
            max-width: 480px;
            margin: 0 auto;
            background: white;
            min-height: 100vh;
        }
        
        .mobile-header {
            background: var(--primary);
            color: white;
            padding: 20px;
            position: sticky;
            top: 0;
            z-index: 100;
        }
        
        .search-bar {
            padding: 15px 20px;
            background: white;
            border-bottom: 1px solid #e2e8f0;
        }
        
        .search-input {
            border-radius: 20px;
            border: 1px solid #e2e8f0;
            padding: 10px 15px;
            width: 100%;
        }
        
        .filter-tabs {
            display: flex;
            padding: 15px 20px;
            gap: 10px;
            overflow-x: auto;
            background: white;
            border-bottom: 1px solid #e2e8f0;
        }
        
        .filter-tab {
            padding: 8px 16px;
            border-radius: 20px;
            border: 1px solid #e2e8f0;
            background: white;
            color: #64748b;
            text-decoration: none;
            font-size: 0.9rem;
            white-space: nowrap;
            transition: all 0.3s ease;
        }
        
        .filter-tab.active {
            background: var(--primary);
            color: white;
            border-color: var(--primary);
        }
        
        .operations-list {
            padding: 20px;
        }
        
        .operation-card {
            background: white;
            border-radius: 16px;
            padding: 16px;
            margin-bottom: 15px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            border-left: 4px solid var(--primary);
            transition: all 0.3s ease;
        }
        
        .operation-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }
        
        .operation-card.status-en_attente_de_validation {
            border-left-color: var(--warning);
        }
        
        .operation-card.status-approuvee {
            border-left-color: var(--success);
        }
        
        .operation-card.status-payee {
            border-left-color: var(--primary);
        }
        
        .operation-header {
            display: flex;
            justify-content: between;
            align-items: start;
            margin-bottom: 10px;
        }
        
        .operation-title {
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 5px;
        }
        
        .operation-client {
            color: #64748b;
            font-size: 0.9rem;
        }
        
        .operation-status {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
        }
        
        .status-en_attente_de_validation {
            background: #fef3c7;
            color: #92400e;
        }
        
        .status-approuvee {
            background: #dcfce7;
            color: #166534;
        }
        
        .status-payee {
            background: #dbeafe;
            color: #1e40af;
        }
        
        .operation-details {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 10px;
            padding-top: 10px;
            border-top: 1px solid #f1f5f9;
        }
        
        .operation-info {
            font-size: 0.85rem;
            color: #64748b;
        }
        
        .operation-amount {
            font-weight: 600;
            color: var(--primary);
        }
        
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #64748b;
        }
        
        .empty-state i {
            font-size: 3rem;
            color: #cbd5e1;
            margin-bottom: 20px;
        }
        
        .floating-btn {
            position: fixed;
            bottom: 90px;
            right: 20px;
            width: 56px;
            height: 56px;
            border-radius: 50%;
            background: var(--primary);
            color: white;
            border: none;
            box-shadow: 0 4px 20px rgba(37, 99, 235, 0.3);
            font-size: 1.5rem;
            z-index: 999;
        }
        
        .bottom-nav {
            position: fixed;
            bottom: 0;
            left: 50%;
            transform: translateX(-50%);
            width: 100%;
            max-width: 480px;
            background: white;
            border-top: 1px solid #e2e8f0;
            display: flex;
            justify-content: space-around;
            padding: 10px 0;
            z-index: 1000;
        }
        
        .nav-item {
            flex: 1;
            text-align: center;
            padding: 8px;
            color: #64748b;
            text-decoration: none;
            font-size: 0.8rem;
        }
        
        .nav-item.active {
            color: var(--primary);
        }
        
        .nav-item i {
            display: block;
            font-size: 1.2rem;
            margin-bottom: 4px;
        }
    </style>
</head>
<body>
    <div class="mobile-container">
        <!-- Header -->
        <div class="mobile-header">
            <div class="d-flex align-items-center">
                <a href="{{ route('mobile.terrain.index') }}" class="text-white me-3">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <div>
                    <h1 class="h5 mb-0">Mes Opérations</h1>
                    <small>{{ $operations->total() }} opération(s)</small>
                </div>
            </div>
        </div>
        
        <!-- Barre de recherche -->
        <div class="search-bar">
            <form method="GET" action="{{ route('mobile.terrain.operations') }}">
                <input type="text" name="search" class="search-input" placeholder="Rechercher..." value="{{ request('search') }}">
            </form>
        </div>
        
        <!-- Filtres -->
        <div class="filter-tabs">
            <a href="{{ route('mobile.terrain.operations') }}" class="filter-tab {{ !request('status') ? 'active' : '' }}">
                Tout
            </a>
            <a href="{{ route('mobile.terrain.operations', ['status' => 'en_attente_de_validation']) }}" class="filter-tab {{ request('status') == 'en_attente_de_validation' ? 'active' : '' }}">
                En attente
            </a>
            <a href="{{ route('mobile.terrain.operations', ['status' => 'Approuvé_en_attente_paiement']) }}" class="filter-tab {{ request('status') == 'Approuvé_en_attente_paiement' ? 'active' : '' }}">
                Approuvées
            </a>
            <a href="{{ route('mobile.terrain.operations', ['status' => 'payee']) }}" class="filter-tab {{ request('status') == 'payee' ? 'active' : '' }}">
                Payées
            </a>
        </div>
        
        <!-- Liste des opérations -->
        <div class="operations-list">
            @if($operations->count() > 0)
                @foreach($operations as $operation)
                <a href="{{ route('mobile.terrain.show', $operation->id) }}" class="operation-card status-{{ $operation->statut_courant }}">
                    <div class="operation-header">
                        <div>
                            <div class="operation-title">{{ $operation->titre }}</div>
                            <div class="operation-client">{{ $operation->client->name ?? 'Client non spécifié' }}</div>
                        </div>
                        <div class="operation-status status-{{ $operation->statut_courant }}">
                            {{ $operation->statut_courant == 'en_attente_de_validation' ? 'En attente' : 
                               ($operation->statut_courant == 'Approuvé_en_attente_paiement' ? 'Approuvée' : 
                               ($operation->statut_courant == 'payee' ? 'Payée' : $operation->statut_courant)) }}
                        </div>
                    </div>
                    <div class="operation-details">
                        <div class="operation-info">
                            <div><i class="fas fa-calendar me-1"></i>{{ $operation->created_at->format('d/m/Y') }}</div>
                            <div><i class="fas fa-tag me-1"></i>{{ $operation->typeOperation->name ?? 'Non défini' }}</div>
                        </div>
                        @if($operation->montant_estime)
                        <div class="operation-amount">
                            {{ number_format($operation->montant_estime, 0, ',', ' ') }} FCFA
                        </div>
                        @endif
                    </div>
                </a>
                @endforeach
                
                <!-- Pagination -->
                @if($operations->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    {{ $operations->links() }}
                </div>
                @endif
                
            @else
                <div class="empty-state">
                    <i class="fas fa-clipboard-list"></i>
                    <h5>Aucune opération</h5>
                    <p>Commencez par créer votre première opération</p>
                    <a href="{{ route('mobile.terrain.create') }}" class="btn btn-primary rounded-pill">
                        <i class="fas fa-plus me-2"></i>Créer une opération
                    </a>
                </div>
            @endif
        </div>
        
        <!-- Floating Action Button -->
        <a href="{{ route('mobile.terrain.create') }}" class="floating-btn">
            <i class="fas fa-plus"></i>
        </a>
        
        <!-- Bottom Navigation -->
        <div class="bottom-nav">
            <a href="{{ route('mobile.terrain.index') }}" class="nav-item">
                <i class="fas fa-home"></i>
                Accueil
            </a>
            <a href="{{ route('mobile.terrain.operations') }}" class="nav-item active">
                <i class="fas fa-list"></i>
                Opérations
            </a>
            <a href="{{ route('logout') }}" class="nav-item">
                <i class="fas fa-sign-out-alt"></i>
                Déconnexion
            </a>
        </div>
    </div>
</body>
</html>

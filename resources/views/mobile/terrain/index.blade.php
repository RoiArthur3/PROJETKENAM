<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Kenam OPS - Terrain</title>
    <meta name="description" content="Application mobile pour les opérations terrain">
    <meta name="theme-color" content="#2563eb">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="Kenam OPS">
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <link rel="apple-touch-icon" href="{{ asset('icons/icon-192x192.png') }}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #2563eb;
            --primary-dark: #1e40af;
            --success: #16a34a;
            --warning: #f59e0b;
            --danger: #dc2626;
            --light: #f8fafc;
        }
        
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            padding-bottom: 80px;
        }
        
        .mobile-container {
            max-width: 480px;
            margin: 0 auto;
            background: white;
            min-height: 100vh;
            position: relative;
            box-shadow: 0 0 40px rgba(0,0,0,0.1);
        }
        
        .mobile-header {
            background: var(--primary);
            color: white;
            padding: 20px;
            text-align: center;
            position: sticky;
            top: 0;
            z-index: 100;
        }
        
        .mobile-header h1 {
            font-size: 1.5rem;
            margin: 0;
            font-weight: 600;
        }
        
        .mobile-header .user-info {
            font-size: 0.9rem;
            opacity: 0.9;
            margin-top: 5px;
        }
        
        .quick-actions {
            padding: 20px;
            background: var(--light);
        }
        
        .action-card {
            background: white;
            border-radius: 16px;
            padding: 20px;
            margin-bottom: 15px;
            text-decoration: none;
            color: inherit;
            display: flex;
            align-items: center;
            transition: all 0.3s ease;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        
        .action-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            color: inherit;
        }
        
        .action-card.primary {
            border-left: 4px solid var(--primary);
        }
        
        .action-card.success {
            border-left: 4px solid var(--success);
        }
        
        .action-card.warning {
            border-left: 4px solid var(--warning);
        }
        
        .action-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            font-size: 1.3rem;
        }
        
        .action-card.primary .action-icon {
            background: rgba(37, 99, 235, 0.1);
            color: var(--primary);
        }
        
        .action-card.success .action-icon {
            background: rgba(22, 163, 74, 0.1);
            color: var(--success);
        }
        
        .action-card.warning .action-icon {
            background: rgba(245, 158, 11, 0.1);
            color: var(--warning);
        }
        
        .action-content h3 {
            margin: 0;
            font-size: 1.1rem;
            font-weight: 600;
        }
        
        .action-content p {
            margin: 5px 0 0;
            font-size: 0.85rem;
            color: #64748b;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
            padding: 20px;
        }
        
        .stat-card {
            background: white;
            border-radius: 12px;
            padding: 15px;
            text-align: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        
        .stat-number {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--primary);
        }
        
        .stat-label {
            font-size: 0.8rem;
            color: #64748b;
            margin-top: 5px;
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
            transition: color 0.3s ease;
        }
        
        .nav-item.active {
            color: var(--primary);
        }
        
        .nav-item i {
            display: block;
            font-size: 1.2rem;
            margin-bottom: 4px;
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
            transition: all 0.3s ease;
        }
        
        .floating-btn:hover {
            transform: scale(1.1);
            box-shadow: 0 6px 30px rgba(37, 99, 235, 0.4);
        }
        
        .sync-indicator {
            position: absolute;
            top: 20px;
            right: 20px;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background: var(--success);
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0% { opacity: 1; }
            50% { opacity: 0.5; }
            100% { opacity: 1; }
        }
        
        .offline-indicator {
            background: var(--warning);
        }
    </style>
</head>
<body>
    <div class="mobile-container">
        <!-- Header -->
        <div class="mobile-header">
            <div class="sync-indicator" id="syncIndicator"></div>
            <h1><i class="fas fa-truck-loading me-2"></i>Kenam OPS</h1>
            <div class="user-info">
                <i class="fas fa-user me-1"></i>{{ $user->name }}
            </div>
        </div>
        
        <!-- Stats Rapides -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number" id="todayCount">0</div>
                <div class="stat-label">Aujourd'hui</div>
            </div>
            <div class="stat-card">
                <div class="stat-number" id="pendingCount">0</div>
                <div class="stat-label">En attente</div>
            </div>
            <div class="stat-card">
                <div class="stat-number" id="validatedCount">0</div>
                <div class="stat-label">Validées</div>
            </div>
            <div class="stat-card">
                <div class="stat-number" id="totalCount">0</div>
                <div class="stat-label">Total</div>
            </div>
        </div>
        
        <!-- Actions Rapides -->
        <div class="quick-actions">
            <h5 class="mb-3"><i class="fas fa-bolt me-2"></i>Actions Rapides</h5>
            
            <a href="{{ route('mobile.terrain.create') }}" class="action-card primary">
                <div class="action-icon">
                    <i class="fas fa-plus"></i>
                </div>
                <div class="action-content">
                    <h3>Nouvelle Opération</h3>
                    <p>Créer une opération terrain avec photos</p>
                </div>
                <i class="fas fa-chevron-right ms-auto text-muted"></i>
            </a>
            
            <a href="{{ route('mobile.terrain.operations') }}" class="action-card success">
                <div class="action-icon">
                    <i class="fas fa-list"></i>
                </div>
                <div class="action-content">
                    <h3>Mes Opérations</h3>
                    <p>Voir et suivre mes opérations</p>
                </div>
                <i class="fas fa-chevron-right ms-auto text-muted"></i>
            </a>
            
            <a href="#" class="action-card warning" onclick="syncData(); return false;">
                <div class="action-icon">
                    <i class="fas fa-sync"></i>
                </div>
                <div class="action-content">
                    <h3>Synchroniser</h3>
                    <p>Mettre à jour les données hors-ligne</p>
                </div>
                <i class="fas fa-chevron-right ms-auto text-muted"></i>
            </a>
        </div>
        
        <!-- Bottom Navigation -->
        <div class="bottom-nav">
            <a href="{{ route('mobile.terrain.index') }}" class="nav-item active">
                <i class="fas fa-home"></i>
                Accueil
            </a>
            <a href="{{ route('mobile.terrain.operations') }}" class="nav-item">
                <i class="fas fa-list"></i>
                Opérations
            </a>
            <a href="#" class="nav-item" onclick="syncData(); return false;">
                <i class="fas fa-sync"></i>
                Sync
            </a>
            <a href="{{ route('logout') }}" class="nav-item">
                <i class="fas fa-sign-out-alt"></i>
                Déconnexion
            </a>
        </div>
        
        <!-- Floating Action Button -->
        <button class="floating-btn" onclick="window.location.href='{{ route('mobile.terrain.create') }}'">
            <i class="fas fa-plus"></i>
        </button>
    </div>
    
    <script>
        // Vérifier le statut de connexion
        function updateConnectionStatus() {
            const indicator = document.getElementById('syncIndicator');
            if (navigator.onLine) {
                indicator.classList.remove('offline-indicator');
                localStorage.setItem('lastSync', new Date().toISOString());
            } else {
                indicator.classList.add('offline-indicator');
            }
        }
        
        // Charger les statistiques
        async function loadStats() {
            try {
                const response = await fetch('/api/mobile/stats');
                const data = await response.json();
                
                document.getElementById('todayCount').textContent = data.today || 0;
                document.getElementById('pendingCount').textContent = data.pending || 0;
                document.getElementById('validatedCount').textContent = data.validated || 0;
                document.getElementById('totalCount').textContent = data.total || 0;
            } catch (error) {
                console.error('Erreur chargement stats:', error);
            }
        }
        
        // Synchronisation des données
        async function syncData() {
            const indicator = document.getElementById('syncIndicator');
            indicator.style.background = '#f59e0b';
            
            try {
                const response = await fetch('/api/mobile/sync');
                const data = await response.json();
                
                // Stocker les données localement pour hors-ligne
                localStorage.setItem('mobileData', JSON.stringify(data));
                
                indicator.style.background = '#16a34a';
                showNotification('Données synchronisées avec succès', 'success');
                
                // Recharger les stats
                await loadStats();
            } catch (error) {
                indicator.style.background = '#dc2626';
                showNotification('Erreur de synchronisation', 'error');
            }
        }
        
        // Notifications
        function showNotification(message, type = 'info') {
            // Créer une notification simple
            const notification = document.createElement('div');
            notification.className = `alert alert-${type} position-fixed top-0 start-50 translate-middle-x mt-3`;
            notification.style.zIndex = '9999';
            notification.textContent = message;
            
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.remove();
            }, 3000);
        }
        
        // Service Worker Registration
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('/sw.js')
                .then(registration => console.log('SW registered'))
                .catch(error => console.log('SW registration failed'));
        }
        
        // Initialisation
        document.addEventListener('DOMContentLoaded', function() {
            updateConnectionStatus();
            loadStats();
            
            // Vérifier la connexion toutes les 30 secondes
            setInterval(updateConnectionStatus, 30000);
            
            // Auto-sync toutes les 5 minutes
            setInterval(syncData, 300000);
        });
        
        window.addEventListener('online', updateConnectionStatus);
        window.addEventListener('offline', updateConnectionStatus);
    </script>
</body>
</html>

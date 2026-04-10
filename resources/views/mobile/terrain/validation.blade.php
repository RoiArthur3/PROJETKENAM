<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Validation Opérations - Kenam OPS</title>
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
            --dark: #1e293b;
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
        
        .validation-tabs {
            display: flex;
            padding: 15px 20px;
            gap: 10px;
            overflow-x: auto;
            background: white;
            border-bottom: 1px solid #e2e8f0;
        }
        
        .validation-tab {
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
        
        .validation-tab.active {
            background: var(--primary);
            color: white;
            border-color: var(--primary);
        }
        
        .validation-card {
            background: white;
            border-radius: 16px;
            padding: 16px;
            margin: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            border-left: 4px solid var(--warning);
            transition: all 0.3s ease;
        }
        
        .validation-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
        }
        
        .validation-card.urgent {
            border-left-color: var(--danger);
            background: #fef2f2;
        }
        
        .operation-number {
            font-family: 'Courier New', monospace;
            font-weight: 600;
            color: var(--primary);
            font-size: 0.9rem;
        }
        
        .operation-title {
            font-weight: 600;
            color: var(--dark);
            margin: 8px 0;
        }
        
        .operation-meta {
            display: flex;
            gap: 15px;
            margin: 10px 0;
            font-size: 0.85rem;
            color: #64748b;
        }
        
        .operation-amount {
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--primary);
            margin: 10px 0;
        }
        
        .validation-actions {
            display: flex;
            gap: 10px;
            margin-top: 15px;
        }
        
        .btn-reject {
            background: var(--danger);
            color: white;
            border: none;
            border-radius: 8px;
            padding: 8px 16px;
            font-size: 0.9rem;
        }
        
        .btn-approve {
            background: var(--success);
            color: white;
            border: none;
            border-radius: 8px;
            padding: 8px 16px;
            font-size: 0.9rem;
        }
        
        .btn-details {
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 8px;
            padding: 8px 16px;
            font-size: 0.9rem;
        }
        
        .stats-bar {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
            padding: 15px 20px;
            background: white;
            border-bottom: 1px solid #e2e8f0;
        }
        
        .stat-item {
            text-align: center;
        }
        
        .stat-number {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary);
        }
        
        .stat-label {
            font-size: 0.7rem;
            color: #64748b;
        }
        
        .filter-section {
            padding: 15px 20px;
            background: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
        }
        
        .filter-row {
            display: flex;
            gap: 10px;
            margin-bottom: 10px;
        }
        
        .filter-select {
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            padding: 8px 12px;
            font-size: 0.9rem;
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
        
        .modal-content {
            border-radius: 16px;
        }
        
        .modal-header {
            background: var(--primary);
            color: white;
            border-radius: 16px 16px 0 0;
        }
        
        .form-control, .form-select {
            border-radius: 8px;
            border: 1px solid #e2e8f0;
        }
        
        .urgency-badge {
            background: var(--danger);
            color: white;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 0.7rem;
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="mobile-container">
        <!-- Header -->
        <div class="mobile-header">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h1 class="h5 mb-0">Validations</h1>
                    <small>{{ Auth::user()->name }} - {{ Auth::user()->role }}</small>
                </div>
                <div class="urgency-badge" id="urgentCount" style="display: none;">
                    Urgent
                </div>
            </div>
        </div>
        
        <!-- Stats -->
        <div class="stats-bar">
            <div class="stat-item">
                <div class="stat-number" id="enAttenteCount">0</div>
                <div class="stat-label">En attente</div>
            </div>
            <div class="stat-item">
                <div class="stat-number" id="todayCount">0</div>
                <div class="stat-label">Aujourd'hui</div>
            </div>
            <div class="stat-item">
                <div class="stat-number" id="myValidationsCount">0</div>
                <div class="stat-label">Mes validations</div>
            </div>
        </div>
        
        <!-- Filtres -->
        <div class="filter-section">
            <div class="filter-row">
                <select class="filter-select" id="serviceFilter">
                    <option value="">Tous les services</option>
                    @foreach($services as $service)
                    <option value="{{ $service->id }}">{{ $service->nom }}</option>
                    @endforeach
                </select>
                <select class="filter-select" id="statusFilter">
                    <option value="">Tous les statuts</option>
                    <option value="en_attente_de_validation">En attente</option>
                    <option value="en_validation">En validation</option>
                    <option value="Approuvé_en_attente_paiement">Approuvées</option>
                </select>
            </div>
            <div class="filter-row">
                <input type="text" class="filter-select" id="searchFilter" placeholder="Rechercher...">
                <select class="filter-select" id="urgencyFilter">
                    <option value="">Toutes</option>
                    <option value="1">Urgentes seulement</option>
                </select>
            </div>
        </div>
        
        <!-- Tabs de validation -->
        <div class="validation-tabs">
            <a href="#" class="validation-tab active" data-tab="a-valider">
                À valider ({{ $aValider->count() }})
            </a>
            <a href="#" class="validation-tab" data-tab="en-cours">
                En cours ({{ $enCours->count() }})
            </a>
            <a href="#" class="validation-tab" data-tab="approuvees">
                Approuvées ({{ $approuvees->count() }})
            </a>
            <a href="#" class="validation-tab" data-tab="historique">
                Historique
            </a>
        </div>
        
        <!-- Liste des validations -->
        <div id="validationsList">
            <!-- Contenu chargé dynamiquement -->
        </div>
        
        <!-- Bottom Navigation -->
        <div class="bottom-nav">
            <a href="{{ route('mobile.terrain.index') }}" class="nav-item">
                <i class="fas fa-home"></i>
                Accueil
            </a>
            <a href="{{ route('mobile.terrain.operations') }}" class="nav-item">
                <i class="fas fa-list"></i>
                Opérations
            </a>
            <a href="#" class="nav-item active">
                <i class="fas fa-check-circle"></i>
                Validations
            </a>
            <a href="{{ route('logout') }}" class="nav-item">
                <i class="fas fa-sign-out-alt"></i>
                Déconnexion
            </a>
        </div>
    </div>
    
    <!-- Modal de validation -->
    <div class="modal fade" id="validationModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Validation d'opération</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="modalOperationDetails"></div>
                    
                    <div class="mb-3">
                        <label class="form-label">Décision</label>
                        <select class="form-select" id="validationDecision">
                            <option value="">Choisir...</option>
                            <option value="approve">Approuver</option>
                            <option value="reject">Rejeter</option>
                            <option value="request_info">Demander des informations</option>
                        </select>
                    </div>
                    
                    <div class="mb-3" id="commentSection" style="display: none;">
                        <label class="form-label">Commentaire</label>
                        <textarea class="form-control" id="validationComment" rows="3" placeholder="Ajoutez votre commentaire..."></textarea>
                    </div>
                    
                    <div class="mb-3" id="dgValidationSection" style="display: none;">
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Cette opération nécessite une validation DG en raison du montant élevé.
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="button" class="btn btn-primary" id="submitValidation">Confirmer</button>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let currentTab = 'a-valider';
        let operationsData = {};
        
        // Charger les données initiales
        async function loadValidationData() {
            try {
                const response = await fetch('/api/mobile/validation-data');
                operationsData = await response.json();
                
                updateStats();
                loadValidations('a-valider');
            } catch (error) {
                console.error('Erreur chargement données:', error);
            }
        }
        
        // Mettre à jour les stats
        function updateStats() {
            document.getElementById('enAttenteCount').textContent = operationsData.a_valider?.length || 0;
            document.getElementById('todayCount').textContent = operationsData.today?.length || 0;
            document.getElementById('myValidationsCount').textContent = operationsData.mes_validations?.length || 0;
            
            // Afficher le badge urgent si nécessaire
            const urgentCount = operationsData.urgent?.length || 0;
            const urgentBadge = document.getElementById('urgentCount');
            if (urgentCount > 0) {
                urgentBadge.style.display = 'block';
                urgentBadge.textContent = `${urgentCount} urgent`;
            }
        }
        
        // Charger les validations selon le tab
        function loadValidations(tab) {
            const container = document.getElementById('validationsList');
            let operations = [];
            
            switch(tab) {
                case 'a-valider':
                    operations = operationsData.a_valider || [];
                    break;
                case 'en-cours':
                    operations = operationsData.en_cours || [];
                    break;
                case 'approuvees':
                    operations = operationsData.approuvees || [];
                    break;
                case 'historique':
                    operations = operationsData.historique || [];
                    break;
            }
            
            // Appliquer les filtres
            operations = applyFilters(operations);
            
            // Afficher les opérations
            if (operations.length > 0) {
                container.innerHTML = operations.map(op => createOperationCard(op)).join('');
            } else {
                container.innerHTML = `
                    <div class="text-center p-5">
                        <i class="fas fa-clipboard-check fa-3x text-muted mb-3"></i>
                        <h5>Aucune opération</h5>
                        <p class="text-muted">Aucune opération à valider dans cette catégorie</p>
                    </div>
                `;
            }
        }
        
        // Appliquer les filtres
        function applyFilters(operations) {
            const serviceFilter = document.getElementById('serviceFilter').value;
            const statusFilter = document.getElementById('statusFilter').value;
            const searchFilter = document.getElementById('searchFilter').value.toLowerCase();
            const urgencyFilter = document.getElementById('urgencyFilter').value;
            
            return operations.filter(op => {
                if (serviceFilter && op.service_operationnel_id != serviceFilter) return false;
                if (statusFilter && op.statut_courant != statusFilter) return false;
                if (searchFilter && !op.titre.toLowerCase().includes(searchFilter)) return false;
                if (urgencyFilter === '1' && !op.urgence) return false;
                return true;
            });
        }
        
        // Créer une carte d'opération
        function createOperationCard(operation) {
            const isUrgent = operation.urgence;
            const operationNumber = generateOperationNumber(operation);
            
            return `
                <div class="validation-card ${isUrgent ? 'urgent' : ''}">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="operation-number">${operationNumber}</div>
                            <div class="operation-title">${operation.titre}</div>
                            <div class="operation-meta">
                                <span><i class="fas fa-user me-1"></i>${operation.client?.name || 'N/A'}</span>
                                <span><i class="fas fa-building me-1"></i>${operation.service?.nom || 'N/A'}</span>
                            </div>
                            <div class="operation-amount">${formatAmount(operation.montant_estime)} FCFA</div>
                        </div>
                        ${isUrgent ? '<div class="urgency-badge">URGENT</div>' : ''}
                    </div>
                    
                    <div class="validation-actions">
                        <button class="btn-details" onclick="showDetails(${operation.id})">
                            <i class="fas fa-eye me-1"></i>Détails
                        </button>
                        <button class="btn-reject" onclick="openValidation(${operation.id}, 'reject')">
                            <i class="fas fa-times me-1"></i>Rejeter
                        </button>
                        <button class="btn-approve" onclick="openValidation(${operation.id}, 'approve')">
                            <i class="fas fa-check me-1"></i>Approuver
                        </button>
                    </div>
                </div>
            `;
        }
        
        // Générer le numéro d'opération
        function generateOperationNumber(operation) {
            const date = new Date(operation.created_at);
            const year = date.getFullYear();
            const month = String(date.getMonth() + 1).padStart(2, '0');
            const order = String(operation.id).padStart(4, '0');
            return `OP-${year}${month}-${order}`;
        }
        
        // Formater le montant
        function formatAmount(amount) {
            return new Intl.NumberFormat('fr-FR').format(amount || 0);
        }
        
        // Ouvrir la modal de validation
        function openValidation(operationId, action) {
            // Charger les détails de l'opération
            const operation = findOperation(operationId);
            if (!operation) return;
            
            // Afficher les détails dans la modal
            document.getElementById('modalOperationDetails').innerHTML = `
                <div class="mb-3">
                    <strong>Numéro:</strong> ${generateOperationNumber(operation)}<br>
                    <strong>Titre:</strong> ${operation.titre}<br>
                    <strong>Client:</strong> ${operation.client?.name || 'N/A'}<br>
                    <strong>Montant:</strong> ${formatAmount(operation.montant_estime)} FCFA<br>
                    <strong>Date:</strong> ${new Date(operation.created_at).toLocaleDateString('fr-FR')}
                </div>
            `;
            
            // Pré-sélectionner l'action
            document.getElementById('validationDecision').value = action;
            document.getElementById('validationDecision').dispatchEvent(new Event('change'));
            
            // Ouvrir la modal
            const modal = new bootstrap.Modal(document.getElementById('validationModal'));
            modal.show();
        }
        
        // Afficher les détails
        function showDetails(operationId) {
            // Rediriger vers la page de détails ou ouvrir une modal détaillée
            window.location.href = `/mobile/terrain/operations/${operationId}`;
        }
        
        // Trouver une opération
        function findOperation(operationId) {
            for (let category in operationsData) {
                const operation = operationsData[category].find(op => op.id == operationId);
                if (operation) return operation;
            }
            return null;
        }
        
        // Soumettre la validation
        document.getElementById('submitValidation').addEventListener('click', async function() {
            const decision = document.getElementById('validationDecision').value;
            const comment = document.getElementById('validationComment').value;
            
            if (!decision) {
                alert('Veuillez sélectionner une décision');
                return;
            }
            
            try {
                // Envoyer la validation au serveur
                const response = await fetch('/api/mobile/submit-validation', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        operation_id: currentOperationId,
                        decision: decision,
                        comment: comment
                    })
                });
                
                const result = await response.json();
                
                if (result.success) {
                    // Fermer la modal
                    bootstrap.Modal.getInstance(document.getElementById('validationModal')).hide();
                    
                    // Recharger les données
                    await loadValidationData();
                    
                    // Afficher une notification
                    showNotification('Validation enregistrée avec succès', 'success');
                } else {
                    throw new Error(result.message || 'Erreur lors de la validation');
                }
                
            } catch (error) {
                showNotification('Erreur: ' + error.message, 'error');
            }
        });
        
        // Afficher les notifications
        function showNotification(message, type = 'info') {
            const notification = document.createElement('div');
            notification.className = `alert alert-${type} position-fixed top-0 start-50 translate-middle-x mt-3`;
            notification.style.zIndex = '9999';
            notification.textContent = message;
            
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.remove();
            }, 3000);
        }
        
        // Gestion des tabs
        document.querySelectorAll('.validation-tab').forEach(tab => {
            tab.addEventListener('click', function(e) {
                e.preventDefault();
                
                // Mettre à jour le tab actif
                document.querySelectorAll('.validation-tab').forEach(t => t.classList.remove('active'));
                this.classList.add('active');
                
                // Charger les validations
                currentTab = this.dataset.tab;
                loadValidations(currentTab);
            });
        });
        
        // Gestion des filtres
        document.querySelectorAll('.filter-select, #searchFilter').forEach(filter => {
            filter.addEventListener('change', function() {
                loadValidations(currentTab);
            });
        });
        
        document.getElementById('searchFilter').addEventListener('input', function() {
            loadValidations(currentTab);
        });
        
        // Afficher/masquer les commentaires selon la décision
        document.getElementById('validationDecision').addEventListener('change', function() {
            const commentSection = document.getElementById('commentSection');
            const decision = this.value;
            
            if (decision === 'reject' || decision === 'request_info') {
                commentSection.style.display = 'block';
                document.getElementById('validationComment').required = true;
            } else {
                commentSection.style.display = 'none';
                document.getElementById('validationComment').required = false;
            }
        });
        
        // Initialisation
        document.addEventListener('DOMContentLoaded', function() {
            loadValidationData();
        });
    </script>
</body>
</html>

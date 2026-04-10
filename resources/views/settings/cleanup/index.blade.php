@extends('layouts.app')

@section('title', 'Nettoyage des Données | KENAM SERVICES')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-broom me-2 text-danger"></i>Nettoyage des Données par Date
                    </h5>
                    <div class="alert alert-warning py-2 px-3 mb-0 small">
                        <i class="fas fa-exclamation-triangle me-1"></i>
                        <strong>Attention:</strong> Cette action est irréversible
                    </div>
                </div>
                <div class="card-body">
                    
                    <!-- État actuel des tables -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="fw-bold text-dark mb-3">
                                <i class="fas fa-database me-2"></i>État Actuel des Tables Principales
                            </h6>
                            <div class="row">
                                @foreach($tables as $tableName => $count)
                                <div class="col-md-3 col-sm-6 mb-3">
                                    <div class="card border-start border-primary border-4 h-100">
                                        <div class="card-body text-center">
                                            <h4 class="fw-bold text-primary mb-1">{{ number_format($count, 0, ',', ' ') }}</h4>
                                            <p class="small text-muted mb-0 text-uppercase">{{ $tableName }}</p>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Formulaire de nettoyage -->
                    <div class="row">
                        <div class="col-md-8">
                            <div class="card border-start border-danger border-4">
                                <div class="card-body">
                                    <h6 class="fw-bold text-danger mb-3">
                                        <i class="fas fa-trash-alt me-2"></i>Configuration du Nettoyage
                                    </h6>
                                    <form id="cleanupForm">
                                        @csrf
                                        <div class="row g-3">
                                            <div class="col-md-4">
                                                <label class="form-label fw-bold">Table à nettoyer</label>
                                                <select name="table" id="tableSelect" class="form-select" required>
                                                    <option value="">Sélectionner une table</option>
                                                    <option value="operations">Opérations</option>
                                                    <option value="clients">Clients</option>
                                                    <option value="factures">Factures</option>
                                                    <option value="depenses">Dépenses</option>
                                                    <option value="audit_logs">Logs d'audit</option>
                                                    <option value="notifications">Notifications</option>
                                                    <option value="sessions">Sessions</option>
                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label fw-bold">Champ de date</label>
                                                <select name="date_field" id="dateFieldSelect" class="form-select" required>
                                                    <option value="">Sélectionner d'abord une table</option>
                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label fw-bold">Antérieur à</label>
                                                <input type="date" name="before_date" id="beforeDate" class="form-control" required>
                                            </div>
                                        </div>
                                        
                                        <div class="row mt-3">
                                            <div class="col-12">
                                                <div class="d-flex gap-2">
                                                    <button type="button" id="previewBtn" class="btn btn-outline-primary">
                                                        <i class="fas fa-search me-1"></i>Prévisualiser
                                                    </button>
                                                    <button type="button" id="executeBtn" class="btn btn-danger" disabled>
                                                        <i class="fas fa-trash me-1"></i>Exécuter le nettoyage
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="card border-start border-info border-4 h-100">
                                <div class="card-body">
                                    <h6 class="fw-bold text-info mb-3">
                                        <i class="fas fa-info-circle me-2"></i>Guide d'utilisation
                                    </h6>
                                    <ol class="small mb-0">
                                        <li class="mb-2">Sélectionnez la table à nettoyer</li>
                                        <li class="mb-2">Choisissez le champ de date (created_at, updated_at, etc.)</li>
                                        <li class="mb-2">Définissez la date limite</li>
                                        <li class="mb-2">Cliquez sur "Prévisualiser" pour voir le nombre d'enregistrements concernés</li>
                                        <li class="mb-2">Confirmez avec "Exécuter le nettoyage"</li>
                                    </ol>
                                    <div class="alert alert-warning small mt-3">
                                        <strong>Note:</strong> Sauvegardez votre base de données avant d'effectuer un nettoyage.
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Résultat de la prévisualisation -->
                    <div class="row mt-4" id="previewResult" style="display: none;">
                        <div class="col-12">
                            <div class="card border-start border-warning border-4">
                                <div class="card-body">
                                    <h6 class="fw-bold text-warning mb-3">
                                        <i class="fas fa-exclamation-triangle me-2"></i>Résultat de la Prévisualisation
                                    </h6>
                                    <div id="previewContent"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Confirmation finale -->
                    <div class="row mt-4" id="confirmSection" style="display: none;">
                        <div class="col-12">
                            <div class="card border-start border-danger border-4">
                                <div class="card-body">
                                    <h6 class="fw-bold text-danger mb-3">
                                        <i class="fas fa-exclamation-triangle me-2"></i>Confirmation Irréversible
                                    </h6>
                                    <form id="executeForm">
                                        @csrf
                                        <input type="hidden" name="table" id="confirmTable">
                                        <input type="hidden" name="date_field" id="confirmDateField">
                                        <input type="hidden" name="before_date" id="confirmBeforeDate">
                                        
                                        <div class="form-check mb-3">
                                            <input class="form-check-input" type="checkbox" name="confirm" id="confirmCheck" required>
                                            <label class="form-check-label" for="confirmCheck">
                                                Je confirme vouloir supprimer définitivement ces enregistrements
                                            </label>
                                        </div>
                                        
                                        <div class="d-flex gap-2">
                                            <button type="submit" class="btn btn-danger">
                                                <i class="fas fa-trash me-1"></i>Confirmer la suppression
                                            </button>
                                            <button type="button" class="btn btn-secondary" onclick="cancelCleanup()">
                                                <i class="fas fa-times me-1"></i>Annuler
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Mapping des champs de date par table
const dateFields = {
    'operations': ['created_at', 'updated_at', 'date_debut', 'date_fin'],
    'clients': ['created_at', 'updated_at'],
    'factures': ['created_at', 'updated_at', 'date_emission', 'date_echeance'],
    'depenses': ['created_at', 'updated_at', 'date_depense'],
    'audit_logs': ['created_at'],
    'notifications': ['created_at'],
    'sessions': ['last_activity']
};

// Mettre à jour les champs de date selon la table sélectionnée
document.getElementById('tableSelect').addEventListener('change', function() {
    const table = this.value;
    const dateFieldSelect = document.getElementById('dateFieldSelect');
    
    dateFieldSelect.innerHTML = '<option value="">Sélectionner un champ</option>';
    
    if (table && dateFields[table]) {
        dateFields[table].forEach(field => {
            const option = document.createElement('option');
            option.value = field;
            option.textContent = field;
            dateFieldSelect.appendChild(option);
        });
    }
});

// Prévisualiser le nettoyage
document.getElementById('previewBtn').addEventListener('click', function() {
    const formData = new FormData(document.getElementById('cleanupForm'));
    
    if (!formData.get('table') || !formData.get('date_field') || !formData.get('before_date')) {
        alert('Veuillez remplir tous les champs');
        return;
    }
    
    fetch('{{ route('settings.cleanup.preview') }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            table: formData.get('table'),
            date_field: formData.get('date_field'),
            before_date: formData.get('before_date')
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('previewContent').innerHTML = `
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>${data.count}</strong> enregistrements seront supprimés de la table <strong>${formData.get('table')}</strong>
                    <br><small>Champ: ${formData.get('date_field')} | Date limite: ${formData.get('before_date')}</small>
                </div>
            `;
            
            document.getElementById('previewResult').style.display = 'block';
            document.getElementById('executeBtn').disabled = data.count === 0;
            
            // Stocker les valeurs pour la confirmation
            document.getElementById('confirmTable').value = formData.get('table');
            document.getElementById('confirmDateField').value = formData.get('date_field');
            document.getElementById('confirmBeforeDate').value = formData.get('before_date');
        } else {
            alert('Erreur: ' + data.error);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Erreur lors de la prévisualisation');
    });
});

// Afficher la confirmation
document.getElementById('executeBtn').addEventListener('click', function() {
    document.getElementById('confirmSection').style.display = 'block';
});

// Annuler le nettoyage
function cancelCleanup() {
    document.getElementById('confirmSection').style.display = 'none';
    document.getElementById('executeBtn').disabled = true;
}

// Soumettre le formulaire de nettoyage
document.getElementById('executeForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    if (!document.getElementById('confirmCheck').checked) {
        alert('Vous devez cocher la case de confirmation');
        return;
    }
    
    const formData = new FormData(this);
    
    fetch('{{ route('settings.cleanup.execute') }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'text/html'
        },
        body: formData
    })
    .then(response => response.text())
    .then(html => {
        // Recharger la page pour voir les résultats
        window.location.reload();
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Erreur lors du nettoyage');
    });
});
</script>
@endsection

<x-dashboard-layout title="Gestion des requêtes entre services" icon="fas fa-exchange-alt">
    <x-slot name="kpis">
        <div class="row">
            <div class="col-lg-3 col-md-6 mb-3">
                <x-kpi-card 
                    title="Total requêtes" 
                    value="{{ $stats['total'] }}" 
                    icon="fas fa-list" 
                    color="primary" 
                    trend="stable" />
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <x-kpi-card 
                    title="En attente" 
                    value="{{ $stats['en_attente'] }}" 
                    icon="fas fa-clock" 
                    color="warning" 
                    trend="up" />
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <x-kpi-card 
                    title="En cours" 
                    value="{{ $stats['en_cours'] }}" 
                    icon="fas fa-spinner" 
                    color="info" 
                    trend="stable" />
            </div>
            <div class="col-lg-3 col-md-6 mb-3">
                <x-kpi-card 
                    title="Clôturées" 
                    value="{{ $stats['cloturees'] }}" 
                    icon="fas fa-check-circle" 
                    color="success" 
                    trend="up" />
            </div>
        </div>
    </x-slot>

    <x-slot name="filters">
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Recherche</label>
                <input type="text" class="form-control" placeholder="Objet, description, référence..." id="searchInput">
            </div>
            <div class="col-md-3">
                <label class="form-label">Service</label>
                <select class="form-select" id="serviceFilter">
                    <option value="">Tous les services</option>
                    @foreach($services as $service)
                        <option value="{{ $service->id }}">{{ $service->nom }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Statut</label>
                <select class="form-select" id="statusFilter">
                    <option value="">Tous les statuts</option>
                    <option value="ENREGISTREE">Enregistrée</option>
                    <option value="EN_ATTENTE_ENVOI">En attente d'envoi</option>
                    <option value="ENVOYEE">Envoyée</option>
                    <option value="EN_COURS_DE_TRAITEMENT">En cours</option>
                    <option value="TRANSFERE">Transférée</option>
                    <option value="CLOTUREE">Clôturée</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">&nbsp;</label>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-primary" onclick="applyFilters()">
                        <i class="fas fa-search"></i> Rechercher
                    </button>
                    <a href="{{ route('requetes.create') }}" class="btn btn-success">
                        <i class="fas fa-plus me-1"></i>Nouvelle requête
                    </a>
                </div>
            </div>
        </div>
    </x-slot>

    <!-- Section Requêtes à traiter -->
    @php($requetesATraiter = $operations->filter(fn($op) => in_array($op->statut_requete, ['ENVOYEE', 'EN_COURS_DE_TRAITEMENT'])))
    @if($requetesATraiter->count() > 0)
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-warning bg-opacity-10">
            <h5 class="card-title mb-0">
                <i class="fas fa-exclamation-triangle text-warning me-2"></i>
                Requêtes à traiter ({{ $requetesATraiter->count() }})
            </h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Référence</th>
                            <th>Objet</th>
                            <th>Service</th>
                            <th>Demandeur</th>
                            <th>Priorité</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($requetesATraiter as $operation)
                            <tr class="table-warning">
                                <td>
                                    <code class="bg-light px-2 py-1 rounded">{{ $operation->reference_requete }}</code>
                                </td>
                                <td>
                                    <div class="fw-semibold">{{ Str::limit($operation->nom, 40) }}</div>
                                    <small class="text-muted">{{ Str::limit($operation->description, 50) }}</small>
                                </td>
                                <td>
                                    <span class="badge bg-primary">{{ optional($operation->serviceDestinataire)->nom ?? '—' }}</span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px;">
                                            {{ strtoupper(substr(optional($operation->user)->name ?? 'U', 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="fw-medium">{{ optional($operation->user)->name ?? 'Utilisateur inconnu' }}</div>
                                            <small class="text-muted">{{ optional($operation->user)->email ?? '—' }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-{{
                                        $operation->priorite == 'URGENTE' ? 'danger' :
                                        ($operation->priorite == 'HAUTE' ? 'warning' : 
                                        ($operation->priorite == 'MOYENNE' ? 'info' : 'success'))
                                    }}">
                                        {{ $operation->priorite }}
                                    </span>
                                </td>
                                <td>
                                    <small>{{ optional($operation->created_at)->format('d/m/Y H:i') ?? '—' }}</small>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('requetes.show', $operation) }}" class="btn btn-outline-primary" title="Voir">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if($operation->statut_requete == 'ENVOYEE')
                                            <button class="btn btn-outline-success" title="Prendre en charge">
                                                <i class="fas fa-play"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    <!-- Section Historique -->
    <div class="card shadow-sm">
        <div class="card-header bg-light">
            <h5 class="card-title mb-0">
                <i class="fas fa-history text-info me-2"></i>
                Toutes les requêtes
            </h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-striped">
                    <thead class="table-light">
                        <tr>
                            <th>Référence</th>
                            <th>Objet</th>
                            <th>Service</th>
                            <th>Demandeur</th>
                            <th>Statut</th>
                            <th>Priorité</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($operations as $operation)
                            <tr>
                                <td>
                                    <code class="bg-light px-2 py-1 rounded">{{ $operation->reference_requete }}</code>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm bg-{{ $operation->statut_requete == 'CLOTUREE' ? 'success' : ($operation->statut_requete == 'REJETEE' ? 'danger' : 'warning') }} text-white rounded-circle d-flex align-items-center justify-content-center me-2">
                                            <i class="fas fa-{{ $operation->statut_requete == 'CLOTUREE' ? 'check' : ($operation->statut_requete == 'REJETEE' ? 'times' : 'clock') }}"></i>
                                        </div>
                                        <div>
                                            <div class="fw-semibold">{{ $operation->nom }}</div>
                                            <small class="text-muted">{{ Str::limit($operation->description, 60) }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @php($sdNom = optional($operation->serviceDestinataire)->nom)
                                    <span class="badge bg-{{
                                        $sdNom === 'Comptabilité' ? 'primary' :
                                        ($sdNom === 'RH' ? 'warning' :
                                        ($sdNom === 'Technique' ? 'info' : 'secondary'))
                                    }}">
                                        {{ $sdNom ?? '—' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px;">
                                            {{ strtoupper(substr(optional($operation->user)->name ?? 'U', 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="fw-medium">{{ optional($operation->user)->name ?? 'Utilisateur inconnu' }}</div>
                                            <small class="text-muted">{{ optional($operation->user)->email ?? '—' }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-{{
                                        $operation->statut_requete == 'ENREGISTREE' ? 'secondary' :
                                        ($operation->statut_requete == 'ENVOYEE' ? 'warning' :
                                        ($operation->statut_requete == 'EN_COURS_DE_TRAITEMENT' ? 'info' :
                                        ($operation->statut_requete == 'CLOTUREE' ? 'success' : 'danger')))
                                    }}">
                                        {{ str_replace('_', ' ', $operation->statut_requete) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-{{
                                        $operation->priorite == 'URGENTE' ? 'danger' :
                                        ($operation->priorite == 'HAUTE' ? 'warning' : 
                                        ($operation->priorite == 'MOYENNE' ? 'info' : 'success'))
                                    }}">
                                        {{ $operation->priorite }}
                                    </span>
                                </td>
                                <td>
                                    <small>{{ optional($operation->created_at)->format('d/m/Y') ?? '—' }}<br>{{ optional($operation->created_at)->format('H:i') ?? '' }}</small>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('requetes.show', $operation) }}" class="btn btn-outline-primary" title="Voir">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if($operation->statut_requete == 'ENREGISTREE' || $operation->statut_requete == 'EN_ATTENTE_ENVOI')
                                            <a href="{{ route('requetes.show', $operation) }}" class="btn btn-outline-success" title="Envoyer">
                                                <i class="fas fa-paper-plane"></i>
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4">
                                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                    <div class="text-muted">Aucune requête trouvée</div>
                                    <p class="text-muted small">Aucune demande en attente pour le moment.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="d-flex justify-content-center">
                {{ $operations->links() }}
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function applyFilters() {
            const search = document.getElementById('searchInput').value;
            const service = document.getElementById('serviceFilter').value;
            const status = document.getElementById('statusFilter').value;
            
            let url = new URL(window.location);
            
            if (search) url.searchParams.set('search', search);
            else url.searchParams.delete('search');
            
            if (service) url.searchParams.set('service', service);
            else url.searchParams.delete('service');
            
            if (status) url.searchParams.set('statut', status);
            else url.searchParams.delete('statut');
            
            window.location = url;
        }

        // Auto-submit on Enter
        document.getElementById('searchInput').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') applyFilters();
        });

        document.getElementById('serviceFilter').addEventListener('change', applyFilters);
        document.getElementById('statusFilter').addEventListener('change', applyFilters);
    </script>
    @endpush
</x-dashboard-layout>

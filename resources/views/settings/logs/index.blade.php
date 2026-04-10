@extends('layouts.app')

@section('title', 'Gestion des Logs Système | KENAM SERVICES')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-file-alt me-2"></i>Gestion des Logs Système
                    </h5>
                    <div class="btn-group btn-group-sm">
                        <a href="{{ route('settings.logs.download') }}" class="btn btn-outline-primary">
                            <i class="fas fa-download me-1"></i>Télécharger
                        </a>
                        <a href="{{ route('settings.logs.clear') }}" class="btn btn-outline-warning" 
                           onclick="return confirm('Êtes-vous sûr de vouloir vider le fichier de logs ?')">
                            <i class="fas fa-eraser me-1"></i>Vider
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    
                    <!-- Fichiers de logs -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h6 class="fw-bold text-dark mb-3">
                                <i class="fas fa-folder-open me-2"></i>Fichiers de Logs Disponibles
                            </h6>
                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Nom du fichier</th>
                                            <th>Taille</th>
                                            <th>Dernière modification</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($logFiles as $file)
                                        <tr>
                                            <td>
                                                <i class="fas fa-file-alt text-muted me-2"></i>
                                                <code>{{ $file['filename'] }}</code>
                                            </td>
                                            <td>
                                                <span class="badge bg-info">{{ $file['size'] }}</span>
                                            </td>
                                            <td>
                                                <small class="text-muted">{{ $file['modified'] }}</small>
                                            </td>
                                            <td>
                                                <button class="btn btn-sm btn-outline-primary" 
                                                        onclick="viewLogFile('{{ $file['filename'] }}')">
                                                    <i class="fas fa-eye me-1"></i>Voir
                                                </button>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="4" class="text-center text-muted py-4">
                                                <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                                Aucun fichier de log trouvé
                                            </td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Nettoyage automatique -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card border-start border-warning border-4">
                                <div class="card-body">
                                    <h6 class="fw-bold text-dark mb-3">
                                        <i class="fas fa-broom me-2"></i>Nettoyage Automatique
                                    </h6>
                                    <form action="{{ route('settings.logs.cleanup') }}" method="POST" class="d-flex gap-2">
                                        @csrf
                                        <div class="flex-grow-1">
                                            <select name="days" class="form-select form-select-sm">
                                                <option value="7">7 jours</option>
                                                <option value="15">15 jours</option>
                                                <option value="30">30 jours</option>
                                                <option value="60">60 jours</option>
                                                <option value="90">90 jours</option>
                                            </select>
                                        </div>
                                        <button type="submit" class="btn btn-warning btn-sm"
                                                onclick="return confirm('Supprimer les fichiers de logs plus anciens que la période sélectionnée ?')">
                                            <i class="fas fa-trash me-1"></i>Nettoyer
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card border-start border-info border-4">
                                <div class="card-body">
                                    <h6 class="fw-bold text-dark mb-3">
                                        <i class="fas fa-info-circle me-2"></i>Informations
                                    </h6>
                                    <ul class="small mb-0">
                                        <li>Les logs sont stockés dans <code>storage/logs/</code></li>
                                        <li>Le fichier principal est <code>laravel.log</code></li>
                                        <li>Les anciens logs sont archivés automatiquement</li>
                                        <li>Le nettoyage libère de l'espace disque</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Logs récents -->
                    <div class="row">
                        <div class="col-12">
                            <h6 class="fw-bold text-dark mb-3">
                                <i class="fas fa-history me-2"></i>50 Dernières Entrées de Log
                            </h6>
                            <div class="card bg-dark text-light">
                                <div class="card-body p-0" style="max-height: 400px; overflow-y: auto;">
                                    @forelse($recentLogs as $log)
                                        <div class="log-entry p-2 border-bottom border-secondary" style="font-family: 'Courier New', monospace; font-size: 12px; white-space: pre-wrap;">{{ $log }}</div>
                                    @empty
                                        <div class="text-center text-muted py-4">
                                            Aucune entrée de log récente
                                        </div>
                                    @endforelse
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
function viewLogFile(filename) {
    // Ouvrir le fichier de log dans une nouvelle fenêtre ou modal
    window.open(`/storage/logs/${filename}`, '_blank');
}
</script>
@endsection

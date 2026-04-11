@extends('layouts.app')

@section('title', 'Documents juridiques')

@section('content')
<div class="content-wrapper">
    <!-- En-tête -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">
            <i class="fas fa-file-contract me-2"></i>Documents juridiques
        </h1>
        <a href="{{ route('juridique.documents.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>Nouveau document
        </a>
    </div>

    <!-- Tableau des documents -->
    <div class="card">
        <div class="card-header">
            <h6 class="mb-0">
                <i class="fas fa-list me-2"></i>Liste des documents
            </h6>
        </div>
        <div class="card-body table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Référence</th>
                        <th>Titre</th>
                        <th>Type</th>
                        <th>Contrat</th>
                        <th>Fichier</th>
                        <th>Expiration</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($documents as $document)
                        <tr>
                            <td>
                                <span class="badge bg-light text-dark">{{ $document->reference ?? 'N/A' }}</span>
                            </td>
                            <td>
                                <strong>{{ $document->titre }}</strong>
                            </td>
                            <td>
                                <span class="badge bg-info">{{ $document->type_document ?? 'Non défini' }}</span>
                            </td>
                            <td>
                                @if($document->contrat)
                                    <a href="{{ route('juridique.contrats.show', $document->contrat) }}" class="text-decoration-none">
                                        {{ $document->contrat->titre }}
                                    </a>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($document->chemin_fichier)
                                    <span class="badge bg-success">
                                        <i class="fas fa-check me-1"></i>Disponible
                                    </span>
                                @else
                                    <span class="badge bg-secondary">
                                        <i class="fas fa-times me-1"></i>Aucun
                                    </span>
                                @endif
                            </td>
                            <td>
                                @if($document->date_expiration)
                                    <span class="text-{{ optional($document->date_expiration)->isPast() ? 'danger' : 'success' }}">
                                        {{ optional($document->date_expiration)->format('d/m/Y') }}
                                    </span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-{{ $document->statut == 'actif' ? 'success' : 'secondary' }}">
                                    {{ $document->statut ?? 'Inconnu' }}
                                </span>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('juridique.documents.show', $document) }}" class="btn btn-outline-primary" title="Voir">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('juridique.documents.edit', $document) }}" class="btn btn-outline-warning" title="Modifier">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form action="{{ route('juridique.documents.destroy', $document) }}" method="POST" style="display: inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger" title="Supprimer" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce document ?')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4">
                                <div class="text-muted">
                                    <i class="fas fa-inbox fa-3x mb-3"></i>
                                    <p class="mb-3">Aucun document juridique trouvé</p>
                                    <a href="{{ route('juridique.documents.create') }}" class="btn btn-primary">
                                        <i class="fas fa-plus me-2"></i>Créer le premier document
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <!-- Pagination -->
            @if($documents->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    {{ $documents->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<style>
.badge {
    font-size: 0.75rem;
}

.table th {
    font-weight: 600;
    font-size: 0.875rem;
    border-top: none;
    background-color: #f8f9fa;
}

.table td {
    font-size: 0.875rem;
    vertical-align: middle;
}

.btn-group-sm > .btn {
    padding: 0.25rem 0.5rem;
}

.card {
    border: none;
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
}

.card-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid rgba(0, 0, 0, 0.125);
}

.text-decoration-none:hover {
    text-decoration: underline !important;
}
</style>

<script>
// Confirmation de suppression
function confirmDelete(id) {
    if (confirm('Êtes-vous sûr de vouloir supprimer ce document ? Cette action est irréversible.')) {
        document.getElementById('delete-form-' + id).submit();
    }
}

// Animation au chargement
document.addEventListener('DOMContentLoaded', function() {
    const rows = document.querySelectorAll('tbody tr');
    rows.forEach((row, index) => {
        row.style.opacity = '0';
        row.style.transform = 'translateX(-20px)';
        setTimeout(() => {
            row.style.transition = 'all 0.3s ease';
            row.style.opacity = '1';
            row.style.transform = 'translateX(0)';
        }, index * 50);
    });
});
</script>
@endsection

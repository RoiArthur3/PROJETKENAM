@extends('layouts.app')

@section('title', "Rapports d'Exécution - KENAM SERVICES")

@section('content')
<x-list-layout
    title="Rapports d'Exécution"
    icon="fa-file-alt"
    createRoute="#"
    createText="Nouveau Rapport"
>

    <x-slot name="filters">
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label fw-bold">Projet</label>
                <input type="text" class="form-control" placeholder="Référence ou nom du projet">
            </div>
            <div class="col-md-3">
                <label class="form-label fw-bold">Type de Rapport</label>
                <select class="form-select">
                    <option value="">Tous</option>
                    <option value="journalier">Rapport journalier</option>
                    <option value="incident">Rapport d'incident</option>
                    <option value="livraison">Rapport de livraison</option>
                    <option value="final">Rapport final</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-bold">Période</label>
                <select class="form-select">
                    <option value="">Toutes les périodes</option>
                    <option value="semaine">Cette semaine</option>
                    <option value="mois">Ce mois</option>
                    <option value="trimestre">Ce trimestre</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">&nbsp;</label>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-primary w-100">
                        <i class="fas fa-search me-2"></i>Filtrer
                    </button>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover" id="rapportsTable">
                    <thead class="table-light">
                        <tr>
                            <th>N° Rapport</th>
                            <th>Date</th>
                            <th>Projet</th>
                            <th>Type</th>
                            <th>Auteur</th>
                            <th>Statut</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse(($rapports ?? []) as $rapport)
                            <tr>
                                <td><strong>{{ $rapport->reference ?? '-' }}</strong></td>
                                <td>{{ optional($rapport->created_at ?? null)->format('d/m/Y H:i') ?? '-' }}</td>
                                <td>
                                    {{ $rapport->projet_reference ?? '-' }}<br>
                                    <small class="text-muted">{{ $rapport->projet_nom ?? '' }}</small>
                                </td>
                                <td>
                                    <span class="badge bg-secondary">{{ $rapport->type ?? '-' }}</span>
                                </td>
                                <td>{{ $rapport->auteur ?? '-' }}</td>
                                <td>
                                    <span class="badge bg-primary">{{ $rapport->statut ?? '-' }}</span>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm">
                                        <button type="button" class="btn btn-outline-primary" title="Voir">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    <i class="fas fa-file-alt fa-3x text-muted mb-3"></i>
                                    <div class="text-muted">Aucun rapport d'exécution trouvé</div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</x-list-layout>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.8/js/jquery.dataTables.min.js"></script>
<script>
$(document).ready(function() {
    $('#rapportsTable').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.10.25/i18n/French.json'
        },
        order: [[0, 'desc']]
    });
});
</script>
@endsection

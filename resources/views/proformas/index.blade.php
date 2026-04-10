@extends('layouts.app')

@section('title', 'Gestion des Proformas')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="fas fa-file-invoice-dollar me-2"></i>
                        Gestion des Proformas
                    </h4>
                    <a href="{{ route('proformas.create') }}" class="btn btn-light">
                        <i class="fas fa-plus me-1"></i>
                        Nouvelle Proforma
                    </a>
                </div>

                <div class="card-body">
                    <div class="table-responsive">
                        <table id="proformas-table" class="table table-striped table-bordered">
                            <thead class="table-success">
                                <tr>
                                    <th>Référence</th>
                                    <th>Client</th>
                                    <th>Projet</th>
                                    <th>Date proposition</th>
                                    <th>Total TTC</th>
                                    <th>Statut</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#proformas-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route("proformas.index") }}',
        columns: [
            { data: 'reference', name: 'reference' },
            { data: 'client_name', name: 'client_name' },
            { data: 'projet_name', name: 'projet_name' },
            { data: 'date_proposition', name: 'date_proposition' },
            {
                data: 'total_ttc',
                name: 'total_ttc',
                render: function(data) {
                    return parseFloat(data).toLocaleString('fr-FR', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    }) + ' FCFA';
                }
            },
            { data: 'statut_badge', name: 'statut', orderable: false },
            { data: 'actions', name: 'actions', orderable: false, searchable: false }
        ],
        language: {
            url: '//cdn.datatables.net/plug-ins/1.10.24/i18n/French.json'
        },
        order: [[3, 'desc']], // Trier par date de proposition décroissante
        pageLength: 25
    });
});

function duplicateProforma(id) {
    if (confirm('Êtes-vous sûr de vouloir dupliquer cette proforma ?')) {
        window.location.href = '{{ url("proformas") }}/' + id + '/duplicate';
    }
}

function validateAndInvoice(id) {
    if (!confirm('Valider cette proforma et générer la facture ?')) {
        return;
    }

    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '{{ url("proformas") }}/' + id + '/validate-invoice';

    const token = document.createElement('input');
    token.type = 'hidden';
    token.name = '_token';
    token.value = '{{ csrf_token() }}';
    form.appendChild(token);

    document.body.appendChild(form);
    form.submit();
}
</script>
@endsection

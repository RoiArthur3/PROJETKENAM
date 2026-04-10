<x-dashboard-layout title="Détails de l'Alerte" icon="fa-solid fa-bell">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">
                        <i class="fa-solid fa-bell me-2"></i>
                        Alerte #{{ $alerte->id }} - {{ $alerte->immatriculation }}
                    </h3>
                    <div class="card-tools">
                        <a href="{{ route('alerts.index') }}" class="btn btn-secondary btn-sm">
                            <i class="fa-solid fa-arrow-left me-1"></i>
                            Retour à la liste
                        </a>
                        <a href="{{ route('alerts.edit', $alerte->id) }}" class="btn btn-warning btn-sm">
                            <i class="fa-solid fa-edit me-1"></i>
                            Modifier
                        </a>
                        @if (!$alerte->traitee)
                            <button class="btn btn-success btn-sm" onclick="marquerCommeTraitee({{ $alerte->id }})">
                                <i class="fa-solid fa-check me-1"></i>
                                Marquer traité
                            </button>
                        @endif
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Informations principales -->
                        <div class="col-md-6">
                            <div class="card card-outline card-primary">
                                <div class="card-header">
                                    <h5 class="card-title">
                                        <i class="fa-solid fa-info-circle me-2"></i>
                                        Informations
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-sm">
                                        <tr>
                                            <td><strong>Véhicule:</strong></td>
                                            <td>{{ $alerte->immatriculation }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Marque:</strong></td>
                                            <td>{{ $alerte->marque }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Modèle:</strong></td>
                                            <td>{{ $alerte->modele }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Type:</strong></td>
                                            <td>
                                                @switch($alerte->type_alerte)
                                                    @case('assurance')
                                                        <span class="badge bg-info">Assurance</span>
                                                    @break
                                                    @case('maintenance')
                                                        <span class="badge bg-warning">Maintenance</span>
                                                    @break
                                                    @case('kilometrage')
                                                        <span class="badge bg-secondary">Kilométrage</span>
                                                    @break
                                                    @default
                                                        <span class="badge bg-light">{{ $alerte->type_alerte }}</span>
                                                @endswitch
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- Détails de l'alerte -->
                        <div class="col-md-6">
                            <div class="card card-outline card-warning">
                                <div class="card-header">
                                    <h5 class="card-title">
                                        <i class="fa-solid fa-exclamation-triangle me-2"></i>
                                        Détails de l'Alerte
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <table class="table table-sm">
                                        <tr>
                                            <td><strong>Message:</strong></td>
                                            <td>{{ $alerte->message }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Date:</strong></td>
                                            <td>{{ \Carbon\Carbon::parse($alerte->date_alerte)->format('d/m/Y H:i') }}</td>
                                        </tr>
                                        <tr>
                                            <td><strong>Niveau:</strong></td>
                                            <td>
                                                @switch($alerte->niveau_alerte)
                                                    @case('info')
                                                        <span class="badge bg-info">Information</span>
                                                    @break
                                                    @case('warning')
                                                        <span class="badge bg-warning">Avertissement</span>
                                                    @break
                                                    @case('critical')
                                                        <span class="badge bg-danger">Critique</span>
                                                    @break
                                                    @default
                                                        <span class="badge bg-secondary">{{ $alerte->niveau_alerte }}</span>
                                                @endswitch
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Statut:</strong></td>
                                            <td>
                                                @switch($alerte->traitee)
                                                    @case(0)
                                                        <span class="badge bg-warning">En attente</span>
                                                    @break
                                                    @case(1)
                                                        <span class="badge bg-success">Traité</span>
                                                    @break
                                                    @default
                                                        <span class="badge bg-secondary">{{ $alerte->traitee }}</span>
                                                @endswitch
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Historique -->
                    <div class="col-12 mt-4">
                        <div class="card card-outline card-info">
                            <div class="card-header">
                                <h5 class="card-title">
                                    <i class="fa-solid fa-history me-2"></i>
                                    Historique
                                </h5>
                            </div>
                            <div class="card-body">
                                <p class="mb-0">
                                    <strong>Créée le:</strong> {{ \Carbon\Carbon::parse($alerte->created_at)->format('d/m/Y H:i') }}
                                </p>
                                @if($alerte->updated_at != $alerte->created_at)
                                    <p class="mb-0">
                                        <strong>Dernière modification:</strong> {{ \Carbon\Carbon::parse($alerte->updated_at)->format('d/m/Y H:i') }}
                                    </p>
                                @endif
                                @if($alerte->updated_by)
                                    <p class="mb-0">
                                        <strong>Modifié par:</strong> {{ $alerte->updated_by_name ?? 'Système' }}
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-dashboard-layout>

@push('scripts')
<script>
function marquerCommeTraitee(id) {
    if (confirm('Marquer cette alerte comme traitée ?')) {
        fetch(`/alerts/${id}/marquer-traite`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({})
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Erreur: ' + data.message);
            }
        })
        .catch(error => console.error('Erreur:', error));
    }
}
</script>
@endpush

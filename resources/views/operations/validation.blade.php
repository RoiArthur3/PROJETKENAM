
@extends('layouts.app')

@section('title', __('operations.validation.title').' - '.config('app.name'))

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">{{ __('operations.validation.title') }} • Opération #{{ $operation->id }}</h1>
        <a href="{{ route('operations.show', $operation->id) }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i> Détails
        </a>
    </div>

    <div class="row">
        <div class="col-lg-8 mb-4">
            <div class="card shadow">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">{{ __('operations.validation.step') }} S{{ $serviceStep->ordre }} • {{ $serviceStep->service_name }}</h6>
                    <span class="badge bg-{{ getOperationStatusColor($serviceStep->statut) }}">
                        {{ getOperationStatusText($serviceStep->statut) }}
                    </span>
                </div>
                <div class="card-body">
                    <p class="text-muted">Titre: <strong>{{ $operation->titre }}</strong></p>
                    <p class="text-muted">Priorité: <span class="badge bg-{{ getPriorityColor($operation->priorite) }}">{{ __('operations.traductions.' . $operation->priorite) ?? ucfirst($operation->priorite) }}</span></p>

                    <!-- Vérification du niveau de validation -->
                    @php
                        $canValidate = true;
                        $validationMessage = '';

                        if ($serviceStep->ordre < 3) {
                            // Pour les niveaux 1 et 2 : vérifier que les niveaux supérieurs n'ont pas validé
                            $higherSteps = $steps->where('ordre', '>', $serviceStep->ordre);
                            foreach ($higherSteps as $higherStep) {
                                if ($higherStep->statut === 'approved') {
                                    $canValidate = false;
                                    $validationMessage = 'Le niveau supérieur (' . $higherStep->ordre . ') a déjà été validé. Vous ne pouvez plus valider ce niveau.';
                                    break;
                                }
                            }
                        } elseif ($serviceStep->ordre == 3) {
                            // Pour le niveau 3 (destinataire principal) : vérifier que les niveaux 1 et 2 sont validés
                            $previousSteps = $steps->where('ordre', '<', 3);
                            foreach ($previousSteps as $previousStep) {
                                if ($previousStep->statut !== 'approved') {
                                    $canValidate = false;
                                    $validationMessage = 'Le destinataire principal (niveau 3) ne peut valider que lorsque les niveaux 1 et 2 ont été approuvés.';
                                    break;
                                }
                            }
                        }
                    @endphp

                    @if($canValidate)
                        <!-- Formulaire d'approbation -->
                        <form method="POST" action="{{ route('validations.operations.approve', ['operation' => $operation->id, 'step' => $serviceStep->ordre]) }}" class="mb-3">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">{{ __('operations.validation.comment') }}</label>
                                <textarea class="form-control" name="commentaire" rows="4" placeholder="Votre commentaire est optionnel..." maxlength="1000"></textarea>
                                <div class="form-text">Le commentaire est optionnel pour valider cette étape.</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Nom du validateur <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="validator" placeholder="Votre nom" required maxlength="255">
                            </div>
                            <button type="submit" class="btn btn-success me-2"><i class="fas fa-check me-1"></i> {{ __('operations.validation.approve', ['step' => $serviceStep->ordre]) }}</button>
                            <button type="button" class="btn btn-danger" onclick="showRejectForm()"><i class="fas fa-times me-1"></i> {{ __('operations.validation.reject') }}</button>
                        </form>

                        <!-- Formulaire de rejet (caché par défaut) -->
                        <form id="rejectForm" method="POST" action="{{ route('validations.operations.reject', ['operation' => $operation->id, 'step' => $serviceStep->ordre]) }}" style="display: none;">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">Motif du rejet</label>
                                <textarea class="form-control" name="commentaire" rows="4" placeholder="Expliquez les raisons du rejet (optionnel)..." maxlength="1000"></textarea>
                                <div class="form-text">Le commentaire est optionnel pour rejeter cette étape.</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Nom du validateur <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="validator" placeholder="Votre nom" required maxlength="255">
                            </div>
                            <button type="submit" class="btn btn-danger me-2"><i class="fas fa-times me-1"></i> Confirmer le rejet</button>
                            <button type="button" class="btn btn-secondary" onclick="hideRejectForm()">Annuler</button>
                        </form>
                    @else
                        <!-- Message d'erreur si la validation n'est pas autorisée -->
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            {{ $validationMessage }}
                        </div>
                        <p class="text-muted">
                            @if($serviceStep->ordre == 1)
                                <strong>Niveau 1 ({{ $serviceStep->service_name }})</strong> peut valider en premier, mais ne peut plus valider si les niveaux supérieurs ont déjà validé.
                            @elseif($serviceStep->ordre == 2)
                                <strong>Niveau 2 ({{ $serviceStep->service_name }})</strong> peut valider après le niveau 1, mais ne peut plus valider si le niveau 3 a déjà validé.
                            @else
                                <strong>Niveau 3 ({{ $serviceStep->service_name }}) - Destinataire Principal</strong> ne peut valider qu'en dernier, lorsque les niveaux 1 et 2 sont approuvés.
                            @endif
                        </p>
                        <ul class="text-muted">
                            <li>Un commentaire obligatoire est fourni</li>
                            @if($serviceStep->ordre == 3)
                                <li>Les niveaux 1 et 2 ont été approuvés</li>
                            @endif
                        </ul>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">{{ __('operations.show.services_sequence') }}</h6>
                </div>
                <div class="card-body">
                    <ol class="list-group list-group-numbered">
                        @foreach($steps as $s)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span>S{{ $s->ordre }} • {{ $s->service_name }}</span>
                                <span class="badge bg-{{ getOperationStatusColor($s->statut) }}">{{ getOperationStatusText($s->statut) }}</span>
                            </li>
                        @endforeach
                    </ol>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function showRejectForm() {
    document.getElementById('rejectForm').style.display = 'block';
    // Faire défiler jusqu'au formulaire de rejet
    document.getElementById('rejectForm').scrollIntoView({ behavior: 'smooth' });
}

function hideRejectForm() {
    document.getElementById('rejectForm').style.display = 'none';
}
</script>
@endsection

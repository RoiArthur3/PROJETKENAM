@extends('layouts.app')

@section('title', 'Modifier l\'opération - KENAM SERVICES')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .select2-container--default .select2-selection--multiple {
        border: 1px solid #d1d5db;
        border-radius: 0.375rem;
        padding: 0.25rem 0.5rem;
        min-height: 38px;
    }
    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        background-color: #e5e7eb;
        border: 1px solid #d1d5db;
        border-radius: 0.25rem;
        padding: 0 0.5rem;
    }
    .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
        margin-right: 5px;
        color: #6b7280;
    }
    .form-label.required:after {
        content: " *";
        color: #dc3545;
    }
    .file-preview {
        margin-top: 10px;
    }
    .file-item {
        display: flex;
        align-items: center;
        margin-bottom: 5px;
        padding: 5px;
        background: #f8f9fa;
        border-radius: 4px;
    }
    .file-item button {
        margin-left: 10px;
    }
    .service-item {
        transition: all 0.3s ease;
    }
    .service-item:hover {
        cursor: grabbing;
    }
    .sortable-services {
        min-height: 50px;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-edit me-2"></i>Modifier l'opération #{{ $operation->id }}
                    </h6>
                    <div class="d-flex align-items-center gap-2">
                        <a href="{{ route('types-operations.index') }}" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-cog me-1"></i> {{ __('operations.create.manage_types') }}
                        </a>
                        <span class="badge bg-info">{{ ucfirst(str_replace('_', ' ', $operation->statut_courant)) }}</span>
                    </div>
                </div>
                <div class="card-body">
                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                            <i class="fas fa-times-circle me-2"></i>
                            <strong>{{ __('operations.create.error_title') }}</strong><br>
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form id="operationForm" method="POST" action="{{ route('operations.update', ['operationId' => $operation->id]) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="text-primary mb-3">{{ __('operations.create.general_info') }}</h5>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="titre" class="form-label required">{{ __('operations.fields.title') }} <span style="color:red">*</span></label>
                                <input type="text" name="titre" class="form-control @error('titre') is-invalid @enderror"
                                       id="titre" value="{{ old('titre', $operation->titre) }}" required>
                                @error('titre')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">{{ __('operations.create.title_help') }}</div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="priorite" class="form-label required">{{ __('operations.fields.priority') }} <span style="color:red">*</span></label>
                                <select class="form-select @error('priorite') is-invalid @enderror" name="priorite" id="priorite" required>
                                    <option value="basse" {{ old('priorite', $operation->priorite) == 'basse' ? 'selected' : '' }}>{{ __('operations.traductions.basse') ?? 'Basse' }}</option>
                                    <option value="moyenne" {{ old('priorite', $operation->priorite) == 'moyenne' ? 'selected' : '' }}>{{ __('operations.traductions.moyenne') ?? 'Moyenne' }}</option>
                                    <option value="haute" {{ old('priorite', $operation->priorite) == 'haute' ? 'selected' : '' }}>{{ __('operations.traductions.haute') ?? 'Haute' }}</option>
                                    <option value="urgente" {{ old('priorite', $operation->priorite) == 'urgente' ? 'selected' : '' }}>{{ __('operations.traductions.urgente') ?? 'Urgente' }}</option>
                                </select>
                                @error('priorite')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="destinataire_principal" class="form-label required">{{ __('operations.create.main_recipient') }} <span style="color:red">*</span></label>
                                <small class="text-muted">{{ __('operations.create.main_recipient_help') }}</small>
                                <select class="form-select @error('destinataire_principal') is-invalid @enderror" name="destinataire_principal" id="destinataire_principal" required>
                                    <option value="">{{ __('operations.create.select_service') }}</option>
                                    @foreach(($services ?? []) as $service)
                                        <option value="{{ $service->id }}" {{ old('destinataire_principal', $operation->operational_service_id) == $service->id ? 'selected' : '' }}>
                                            {{ $service->nom }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('destinataire_principal')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="services_cc" class="form-label">{{ __('operations.create.cc_services') }}</label>
                                <small class="text-muted">{{ __('operations.create.cc_services_help') }}</small>
                                <div class="border rounded p-3 bg-light">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <small class="text-muted">{{ __('operations.create.add_cc_help') }}</small>
                                        <button type="button" class="btn btn-sm btn-primary" onclick="addCcService()">
                                            <i class="fas fa-plus me-1"></i>{{ __('operations.create.add_cc_btn') }}
                                        </button>
                                    </div>
                                    <div id="ccServicesList"></div>
                                    <input type="hidden" name="services_cc" id="services_cc" value="">
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="type_operation_id" class="form-label required">{{ __('operations.fields.type') ?? 'Type d\'opération' }} <span style="color:red">*</span></label>
                                <select class="form-select @error('type_operation_id') is-invalid @enderror" name="type_operation_id" id="type_operation_id" required>
                                    <option value="">{{ __('operations.create.type_select') }}</option>
                                    @foreach(($types ?? []) as $type)
                                        <option value="{{ $type->id }}" {{ old('type_operation_id', $operation->type_operation_id) == $type->id ? 'selected' : '' }}>
                                            {{ $type->libelle }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('type_operation_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">{{ __('operations.create.types_help') }}</div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="echeance" class="form-label">{{ __('operations.fields.due_date') }} <span style="color:red">*</span></label>
                                @php
                                    $echeanceValue = old('echeance');

                                    if ($echeanceValue === null && !empty($operation->echeance)) {
                                        $echeanceValue = \Carbon\Carbon::parse($operation->echeance)->format('Y-m-d');
                                    }

                                    $echeanceMin = $echeanceValue ?: now()->format('Y-m-d');
                                @endphp
                                <div class="input-group">
                                    <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                     <input type="date" name="echeance" class="form-control @error('echeance') is-invalid @enderror"
                                           id="echeance" value="{{ $echeanceValue }}" min="{{ $echeanceMin }}">
                                </div>
                                @error('echeance')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="montant" class="form-label">{{ __('operations.create.amount_label') }}</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-money-bill-wave"></i></span>
                                    <input type="number" name="montant" class="form-control @error('montant') is-invalid @enderror"
                                           id="montant" value="{{ old('montant', $operation->montant) }}" min="0" step="1">
                                </div>
                                @error('montant')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">{{ __('operations.create.amount_help') }}</div>
                            </div>

                            <div class="col-md-12 mb-3">
                                <label for="description" class="form-label">{{ __('operations.fields.description') }}</label>
                                <textarea name="description" id="description" rows="4" class="form-control @error('description') is-invalid @enderror"
                                          placeholder="{{ __('operations.create.desc_placeholder') }}">{{ old('description', $operation->description) }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <h5 class="text-primary mb-3">{{ __('operations.create.attachments_title') }}</h5>
                            </div>
                            <div class="col-md-12 mb-3">
                                @if($operation->fichiers->count() > 0)
                                    <div class="mb-3">
                                        <p class="mb-2"><strong>Fichiers actuels :</strong></p>
                                        <div class="d-flex flex-wrap gap-2">
                                            @foreach($operation->fichiers as $fichier)
                                                <div class="file-item rounded">
                                                    <i class="fas fa-file-alt me-2"></i>
                                                    <span>{{ $fichier->nom_original }}</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                                <label for="fichiers" class="form-label">{{ __('operations.create.files_label') }}</label>
                                <input type="file" name="fichiers[]" id="fichiers" class="form-control @error('fichiers') is-invalid @enderror" multiple>
                                @error('fichiers')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">{{ __('operations.create.files_help') }}</div>
                                <div id="filePreview" class="file-preview"></div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mt-4">
                            <a href="{{ route('operations.show', ['operationId' => $operation->id]) }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Annuler
                            </a>
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>{{ __('operations.create.submit_btn') }}
                                </button>
                                <button type="button" class="btn btn-danger" onclick='confirmDelete({{ $operation->id }}, @json($operation->titre), @json(route('operations.destroy', ['operationId' => $operation->id])))'>
                                    <i class="fas fa-trash me-2"></i>Supprimer
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let availableServices = @json($services ?? []);
    // Puisque les CC ne sont pas sauvegardés, on ne peut pas les pré-remplir facilement sans modifier le schéma.
    // Cependant, pour l'aspect visuel du formulaire, on garde la même logique.

    function addCcService(selectedId = null) {
        const serviceId = 'cc_service_' + Date.now() + Math.floor(Math.random() * 1000);
        const serviceHtml = `
            <div id="${serviceId}" class="d-flex align-items-center mb-2 p-2 bg-white rounded border">
                <select class="form-select form-select-sm me-2" onchange="updateCcServices()">
                    <option value="">{{ __('operations.create.choose_service') }}</option>
                    ${availableServices.map(service =>
                        `<option value="${service.id}" ${selectedId == service.id ? 'selected' : ''}>${service.nom}</option>`
                    ).join('')}
                </select>
                <button type="button" class="btn btn-sm btn-danger" onclick="removeCcService('${serviceId}')">
                    <i class="fas fa-trash"></i>
                </button>
            </div>`;

        document.getElementById('ccServicesList').insertAdjacentHTML('beforeend', serviceHtml);
        updateCcServices();
    }

    function removeCcService(serviceId) {
        document.getElementById(serviceId).remove();
        updateCcServices();
    }

    function updateCcServices() {
        const selects = document.querySelectorAll('#ccServicesList select');
        const services = Array.from(selects)
            .map(select => select.value)
            .filter(value => value !== '');

        document.getElementById('services_cc').value = services.join(',');
    }

    // Fonction de confirmation de suppression
    function confirmDelete(operationId, operationTitle, deleteUrl) {
        if (confirm(`Êtes-vous sûr de vouloir supprimer l'opération "${operationTitle}" (ID: ${operationId}) ?\n\nCette action est irréversible !`)) {
            // Créer un formulaire caché pour la suppression
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = deleteUrl;
            form.style.display = 'none';

            // Ajouter le token CSRF
            const csrfToken = document.querySelector('meta[name="csrf-token"]');
            if (csrfToken) {
                const csrfInput = document.createElement('input');
                csrfInput.type = 'hidden';
                csrfInput.name = '_token';
                csrfInput.value = csrfToken.getAttribute('content');
                form.appendChild(csrfInput);
            }

            // Ajouter le champ pour la méthode DELETE
            const methodInput = document.createElement('input');
            methodInput.type = 'hidden';
            methodInput.name = '_method';
            methodInput.value = 'DELETE';
            form.appendChild(methodInput);

            // Soumettre le formulaire
            document.body.appendChild(form);
            form.submit();
        }
    }
</script>
@endpush

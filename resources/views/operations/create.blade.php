@extends('layouts.app')

@section('title', __('operations.create.page_title') . ' - KENAM SERVICES')

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
                        <i class="fas fa-plus-circle me-2"></i>{{ __('operations.create.form_title') }}
                    </h6>
                    <div class="d-flex align-items-center gap-2">
                        <a href="{{ route('types-operations.index') }}" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-cog me-1"></i> {{ __('operations.create.manage_types') }}
                        </a>
                        <span class="badge bg-secondary">{{ __('operations.create.draft_badge') }}</span>
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

                    <form id="operationForm" method="POST" action="{{ route('operations.store') }}" enctype="multipart/form-data">
                        @csrf

                        <!-- Section: Informations Générales -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="text-primary mb-3">
                                    <i class="fas fa-info-circle me-2"></i>Informations Générales
                                </h5>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="titre" class="form-label required">{{ __('operations.fields.title') }} <span style="color:red">*</span></label>
                                <input type="text" name="titre" class="form-control @error('titre') is-invalid @enderror"
                                       id="titre" value="{{ old('titre') }}" required>
                                @error('titre')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">{{ __('operations.create.title_help') }}</div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="priorite" class="form-label required">{{ __('operations.fields.priority') }} <span style="color:red">*</span></label>
                                <select class="form-select @error('priorite') is-invalid @enderror" name="priorite" id="priorite" required>
                                    <option value="basse" {{ old('priorite') == 'basse' ? 'selected' : '' }}>{{ __('operations.traductions.basse') ?? 'Basse' }}</option>
                                    <option value="moyenne" {{ old('priorite', 'moyenne') == 'moyenne' ? 'selected' : '' }}>{{ __('operations.traductions.moyenne') ?? 'Moyenne' }}</option>
                                    <option value="haute" {{ old('priorite') == 'haute' ? 'selected' : '' }}>{{ __('operations.traductions.haute') ?? 'Haute' }}</option>
                                    <option value="urgente" {{ old('priorite') == 'urgente' ? 'selected' : '' }}>{{ __('operations.traductions.urgente') ?? 'Urgente' }}</option>
                                </select>
                                @error('priorite')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="type_operation_id" class="form-label required">Type d'opération <span style="color:red">*</span></label>
                                <select class="form-select @error('type_operation_id') is-invalid @enderror" name="type_operation_id" id="type_operation_id" required>
                                    <option value="">{{ __('operations.create.type_select') }}</option>
                                    @foreach(($types ?? []) as $type)
                                        <option value="{{ $type->id }}" {{ old('type_operation_id') == $type->id ? 'selected' : '' }}>
                                            {{ $type->libelle }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('type_operation_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                @if(($types ?? collect())->isEmpty())
                                    <div class="alert alert-warning mt-2 d-flex justify-content-between align-items-center">
                                        <div>
                                            <i class="fas fa-exclamation-triangle me-2"></i>
                                            {{ __('operations.create.no_types') }}
                                        </div>
                                        <a href="{{ route('types-operations.create') }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-plus me-1"></i> {{ __('operations.create.new_type') }}
                                        </a>
                                    </div>
                                @else
                                    <div class="form-text">{{ __('operations.create.types_help') }}</div>
                                @endif
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="destinataire_principal" class="form-label required">
                                    <i class="fas fa-user-tie text-primary me-1"></i>Destinataire Principal <span style="color:red">*</span>
                                </label>
                                <select class="form-select @error('destinataire_principal') is-invalid @enderror" name="destinataire_principal" id="destinataire_principal" required>
                                    <option value="">-- Sélectionner le destinataire principal --</option>
                                    @foreach(($services ?? []) as $service)
                                        <option value="{{ $service->id }}" {{ old('destinataire_principal') == $service->id ? 'selected' : '' }}>
                                            {{ $service->nom }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('destinataire_principal')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">
                                    <i class="fas fa-info-circle text-primary me-1"></i>Service principal concerné par l'opération
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="engin_id" class="form-label">
                                    <i class="fas fa-cogs text-primary me-1"></i>Engin
                                </label>
                                <select class="form-select @error('engin_id') is-invalid @enderror" name="engin_id" id="engin_id">
                                    <option value="">-- Sélectionner l'engin --</option>
                                    @foreach(($engins ?? []) as $engin)
                                        <option value="{{ $engin->id }}" {{ old('engin_id') == $engin->id ? 'selected' : '' }}>
                                            {{ $engin->immatriculation ?? ('Engin #' . $engin->id) }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('engin_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="fournisseur_id" class="form-label">
                                    <i class="fas fa-truck text-primary me-1"></i>Fournisseur
                                </label>
                                <select class="form-select @error('fournisseur_id') is-invalid @enderror" name="fournisseur_id" id="fournisseur_id">
                                    <option value="">-- Sélectionner le fournisseur --</option>
                                    @foreach(($fournisseurs ?? []) as $fournisseur)
                                        <option value="{{ $fournisseur->id }}" {{ old('fournisseur_id') == $fournisseur->id ? 'selected' : '' }}>
                                            {{ $fournisseur->raison_sociale }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('fournisseur_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="echeance" class="form-label">Échéance</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                    <input type="date" name="echeance" class="form-control @error('echeance') is-invalid @enderror"
                                           id="echeance" value="{{ old('echeance') }}" min="{{ now()->format('Y-m-d') }}">
                                </div>
                                @error('echeance')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">
                                    <i class="fas fa-info-circle text-primary me-1"></i>Date limite pour l'exécution (optionnel)
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="montant" class="form-label">Montant</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-money-bill-wave"></i></span>
                                    <input type="number" name="montant" class="form-control @error('montant') is-invalid @enderror"
                                           id="montant" value="{{ old('montant') }}" min="0" step="0.01">
                                </div>
                                @error('montant')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">{{ __('operations.create.amount_help') }}</div>
                            </div>

                            <div class="col-md-12 mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea name="description" id="description" rows="4" class="form-control @error('description') is-invalid @enderror"
                                          placeholder="{{ __('operations.create.desc_placeholder') }}">{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Section: Chaîne de Validation -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="text-primary mb-3">
                                    <i class="fas fa-check-double me-2"></i>Chaîne de Validation
                                </h5>
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle me-2"></i>
                                    <strong>Processus de validation à 3 niveaux :</strong>
                                    Chaque validateur reçoit un email. Après validation, le validateur suivant est automatiquement notifié.
                                    <br><br>
                                    <strong>Ordre de validation :</strong> Validateur 1 → Validateur 2 → Validateur 3 (Destinataire Final)
                                </div>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="validateur_1" class="form-label">
                                    <i class="fas fa-user-check text-primary me-1"></i>Validateur 1
                                </label>
                                <select class="form-select @error('validateur_1') is-invalid @enderror" name="validateur_1" id="validateur_1">
                                    <option value="">-- Sélectionner le validateur 1 --</option>
                                    @foreach(($services ?? []) as $service)
                                        <option value="{{ $service->id }}" {{ old('validateur_1') == $service->id ? 'selected' : '' }}>
                                            {{ $service->nom }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('validateur_1')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">
                                    <i class="fas fa-envelope text-success me-1"></i>Recevra l'email en premier
                                </div>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="validateur_2" class="form-label">
                                    <i class="fas fa-user-check text-info me-1"></i>Validateur 2
                                </label>
                                <select class="form-select @error('validateur_2') is-invalid @enderror" name="validateur_2" id="validateur_2">
                                    <option value="">-- Sélectionner le validateur 2 --</option>
                                    @foreach(($services ?? []) as $service)
                                        <option value="{{ $service->id }}" {{ old('validateur_2') == $service->id ? 'selected' : '' }}>
                                            {{ $service->nom }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('validateur_2')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">
                                    <i class="fas fa-envelope text-info me-1"></i>Recevra l'email après validation du 1er
                                </div>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="validateur_3" class="form-label">
                                    <i class="fas fa-user-check text-secondary me-1"></i>Validateur 3 (Destinataire Final)
                                </label>
                                <select class="form-select @error('validateur_3') is-invalid @enderror" name="validateur_3" id="validateur_3">
                                    <option value="">-- Sélectionner le validateur 3 --</option>
                                    @foreach(($services ?? []) as $service)
                                        <option value="{{ $service->id }}" {{ old('validateur_3') == $service->id ? 'selected' : '' }}>
                                            {{ $service->nom }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('validateur_3')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">
                                    <i class="fas fa-envelope text-secondary me-1"></i>Recevra l'email en dernier
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="services_cc" class="form-label">Services en CC</label>
                                <small class="text-muted">Services qui recevront une copie de la requête</small>
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
                                @if(($services ?? collect())->isEmpty())
                                    <div class="alert alert-warning mt-2 d-flex justify-content-between align-items-center">
                                        <div>
                                            <i class="fas fa-exclamation-triangle me-2"></i>
                                            Aucun service opérationnel trouvé. Créez-en au préalable dans les paramètres.
                                        </div>
                                        <a href="{{ route('admin.services.create') }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-plus me-1"></i> Nouveau service
                                        </a>
                                    </div>
                                @else
                                    <div class="form-text">
                                        <i class="fas fa-check-circle text-success me-1"></i>
                                        {{ count($services) }} service(s) opérationnel(s) disponible(s)
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <h5 class="text-primary mb-3">{{ __('operations.create.attachments_title') }}</h5>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label for="fichiers" class="form-label required">{{ __('operations.create.files_label') }} <span style="color:red">*</span></label>
                                <input type="file" name="fichiers[]" id="fichiers" class="form-control @error('fichiers') is-invalid @enderror" multiple required>
                                @error('fichiers')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">{{ __('operations.create.files_help') }}</div>
                                <div id="filePreview" class="file-preview"></div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12 text-center">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-paper-plane me-2"></i>{{ __('operations.create.submit_btn') }}
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
    let selectedCcServices = [];

    function addCcService() {
        const serviceId = 'cc_service_' + Date.now();
        const serviceHtml = `
            <div id="${serviceId}" class="d-flex align-items-center mb-2 p-2 bg-white rounded border">
                <select class="form-select form-select-sm me-2" onchange="updateCcServices()">
                    <option value="">{{ __('operations.create.choose_service') }}</option>
                    ${availableServices.map(service =>
                        `<option value="${service.id}">${service.nom}</option>`
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
</script>
@endpush

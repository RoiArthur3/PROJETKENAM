@extends('layouts.app')

@section('title', 'Nouvelle Caisse')

@section('content')
<div class="container-fluid">
    <div class="row mb-3">
        <div class="col-md-8">
            <h4 class="mb-0">
                <i class="fas fa-cash-register text-primary me-2"></i>
                Nouvelle Caisse
            </h4>
            <small class="text-muted">Créez une nouvelle caisse pour gérer vos opérations de trésorerie.</small>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('tresorerie.caisses.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i>
                Retour à la liste
            </a>
        </div>
    </div>

    @if(session('error'))
        <div class="alert alert-danger">
            {{ session('error') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $message)
                    <li>{{ $message }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('tresorerie.caisses.store') }}" method="POST" id="caisseForm">
        @csrf

        <div class="row">
            <!-- Informations de base -->
            <div class="col-lg-8">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white">
                        <h6 class="mb-0">
                            <i class="fas fa-info-circle text-primary me-2"></i>
                            Informations de la caisse
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nom de la caisse <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('nom') is-invalid @enderror"
                                       name="nom" value="{{ old('nom') }}" required>
                                @error('nom')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Type de caisse <span class="text-danger">*</span></label>
                                <select name="type" class="form-select @error('type') is-invalid @enderror" required>
                                    <option value="" {{ old('type') === null ? 'selected' : '' }}>Sélectionner un type</option>
                                    <option value="principale" {{ old('type') === 'principale' ? 'selected' : '' }}>Principale</option>
                                    <option value="secondaire" {{ old('type') === 'secondaire' ? 'selected' : '' }}>Secondaire</option>
                                </select>
                                @error('type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Solde initial <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="number" step="0.01" min="0" class="form-control @error('solde_initial') is-invalid @enderror"
                                           name="solde_initial" value="{{ old('solde_initial', 0) }}" required>
                                    <span class="input-group-text">FCFA</span>
                                    @error('solde_initial')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Devise <span class="text-danger">*</span></label>
                                <select name="devise" class="form-select @error('devise') is-invalid @enderror" required>
                                    <option value="XOF" {{ old('devise', 'XOF') === 'XOF' ? 'selected' : '' }}>Franc CFA (XOF)</option>
                                    <option value="EUR" {{ old('devise') === 'EUR' ? 'selected' : '' }}>Euro (EUR)</option>
                                    <option value="USD" {{ old('devise') === 'USD' ? 'selected' : '' }}>Dollar US (USD)</option>
                                </select>
                                @error('devise')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Responsable</label>
                            <select name="responsable_id" class="form-select @error('responsable_id') is-invalid @enderror" id="responsableSelect">
                                <option value="">Sélectionner un responsable</option>
                                @foreach($responsables as $id => $name)
                                    <option value="{{ $id }}" {{ old('responsable_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                                @endforeach
                            </select>
                            @error('responsable_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror"
                                     name="description" rows="3" placeholder="Saisissez une description pour cette caisse...">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-check form-switch mb-3">
                            <input type="hidden" name="est_active" value="0">
                            <input class="form-check-input" type="checkbox" role="switch" id="est_active" name="est_active" value="1" {{ old('est_active', true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="est_active">Caisse active</label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Panneau latéral -->
            <div class="col-lg-4">
                <!-- Aide rapide -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-white">
                        <h6 class="mb-0">
                            <i class="fas fa-question-circle text-info me-2"></i>
                            Aide rapide
                        </h6>
                    </div>
                    <div class="card-body small">
                        <p class="mb-2"><strong>Nom de la caisse :</strong> Donnez un nom clair et identifiable.</p>
                        <p class="mb-2"><strong>Type de caisse :</strong> Choisissez entre principale (caisse centrale) ou secondaire.</p>
                        <p class="mb-2"><strong>Solde initial :</strong> Définissez le montant initial de la caisse.</p>
                        <p class="mb-2"><strong>Responsable :</strong> Affectez un utilisateur responsable de cette caisse.</p>
                    </div>
                </div>

                <!-- Actions -->
                <div class="card shadow-sm">
                    <div class="card-body">
                        <button type="submit" class="btn btn-primary w-100 mb-2">
                            <i class="fas fa-save me-1"></i> Enregistrer la caisse
                        </button>
                        <a href="{{ route('tresorerie.caisses.index') }}" class="btn btn-outline-secondary w-100">
                            <i class="fas fa-times me-1"></i> Annuler
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        // Initialisation de Select2 pour le responsable
        $('#responsableSelect').select2({
            theme: 'bootstrap-5',
            width: '100%',
            placeholder: 'Sélectionner un responsable',
            allowClear: true
        });

        // Validation du formulaire (ne doit pas bloquer l'envoi si le plugin n'est pas chargé)
        if ($.fn && $.fn.validate) {
            $('#caisseForm').validate({
                rules: {
                    nom: 'required',
                    type: 'required',
                    solde_initial: {
                        required: true,
                        min: 0
                    },
                    devise: 'required'
                },
                messages: {
                    nom: 'Veuillez saisir un nom pour la caisse',
                    type: 'Veuillez sélectionner un type de caisse',
                    solde_initial: {
                        required: 'Veuillez saisir un solde initial',
                        min: 'Le solde initial ne peut pas être négatif'
                    },
                    devise: 'Veuillez sélectionner une devise'
                },
                errorElement: 'span',
                errorPlacement: function (error, element) {
                    error.addClass('invalid-feedback');

                    const container = element.closest('.mb-3');
                    if (container.length) {
                        container.append(error);
                        return;
                    }

                    const inputGroup = element.closest('.input-group');
                    if (inputGroup.length) {
                        inputGroup.after(error);
                        return;
                    }

                    element.after(error);
                },
                highlight: function (element) {
                    $(element).addClass('is-invalid');
                },
                unhighlight: function (element) {
                    $(element).removeClass('is-invalid');
                },
                submitHandler: function (form) {
                    form.submit();
                }
            });
        }
    });
</script>
@endpush

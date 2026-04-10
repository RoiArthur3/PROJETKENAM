@extends('layouts.app')

@section('title', 'Nouvelle Dépense de Caisse')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Tableau de bord</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('tresorerie.depenses.index') }}">Dépenses de caisse</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Nouvelle dépense</li>
                </ol>
            </nav>
            <h1>Nouvelle Dépense de Caisse</h1>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
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

                    <form action="{{ route('tresorerie.depenses.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="mb-3">
                            <label for="expense_id" class="form-label">Dépense comptable approuvée *</label>
                            <select name="expense_id" id="expense_id" class="form-select @error('expense_id') is-invalid @enderror" required>
                                <option value="">Sélectionner une dépense approuvée</option>
                                @foreach($expenses as $expense)
                                    <option
                                        value="{{ $expense->id }}"
                                        data-intitule="{{ e($expense->intitule ?? '') }}"
                                        data-montant="{{ $expense->montant }}"
                                        data-date="{{ optional($expense->date_depense)->format('Y-m-d') }}"
                                        data-fournisseur="{{ e($expense->fournisseur ?? '') }}"
                                        {{ (string) old('expense_id', $selectedExpenseId ?? request('expense_id')) === (string) $expense->id ? 'selected' : '' }}
                                    >
                                        {{ $expense->reference ?? ('DEP-' . $expense->id) }} - {{ $expense->intitule ?? '—' }} ({{ number_format($expense->montant, 0, ',', ' ') }} FCFA)
                                    </option>
                                @endforeach
                            </select>
                            @error('expense_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">La trésorerie ne peut payer que des dépenses comptables approuvées.</div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="caisse_id" class="form-label">Caisse *</label>
                                <select name="caisse_id" id="caisse_id" class="form-select @error('caisse_id') is-invalid @enderror" required>
                                    <option value="">Sélectionner une caisse</option>
                                    @foreach($caisses as $caisse)
                                        <option value="{{ $caisse->id }}" {{ old('caisse_id') == $caisse->id ? 'selected' : '' }}>
                                            {{ $caisse->nom }} ({{ $caisse->code ?? $caisse->id }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('caisse_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="compte_comptable_id" class="form-label">Compte Comptable *</label>
                                <select name="compte_comptable_id" id="compte_comptable_id" class="form-select @error('compte_comptable_id') is-invalid @enderror" required>
                                    <option value="">Sélectionner un compte</option>
                                    @foreach($comptes as $compte)
                                        <option value="{{ $compte->id }}" {{ (string) old('compte_comptable_id', $selectedCompteId ?? request('compte_comptable_id')) === (string) $compte->id ? 'selected' : '' }}>
                                            {{ $compte->numero }} - {{ $compte->intitule }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('compte_comptable_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="libelle" class="form-label">Libellé</label>
                            <input type="text" class="form-control" id="libelle" name="libelle" value="{{ old('libelle') }}">
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="montant" class="form-label">Montant (FCFA) *</label>
                                <div class="input-group">
                                    <input type="number" step="0.01" min="0.01" class="form-control" id="montant" name="montant" value="{{ old('montant') }}" required>
                                    <span class="input-group-text">FCFA</span>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label for="date_depense" class="form-label">Date de la dépense *</label>
                                <input type="date" class="form-control" id="date_depense" value="{{ old('date_depense', now()->format('Y-m-d')) }}" readonly>
                                <input type="hidden" name="date_depense" id="date_depense_hidden" value="{{ old('date_depense', now()->format('Y-m-d')) }}">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="beneficiaire" class="form-label">Bénéficiaire *</label>
                            <input type="text" class="form-control @error('beneficiaire') is-invalid @enderror"
                                   id="beneficiaire" name="beneficiaire" value="{{ old('beneficiaire') }}" required>
                            @error('beneficiaire')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="mode_paiement" class="form-label">Mode de paiement *</label>
                                <select name="mode_paiement" id="mode_paiement" class="form-select @error('mode_paiement') is-invalid @enderror" required>
                                    @foreach(\App\Models\DepenseCaisse::getModesPaiement() as $value => $label)
                                        <option value="{{ $value }}" {{ old('mode_paiement') == $value ? 'selected' : '' }}>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('mode_paiement')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="reference_paiement" class="form-label">Référence de paiement</label>
                                <input type="text" class="form-control @error('reference_paiement') is-invalid @enderror"
                                       id="reference_paiement" name="reference_paiement" value="{{ old('reference_paiement') }}">
                                @error('reference_paiement')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="operation_id" class="form-label">Opération liée (optionnel)</label>
                            <select name="operation_id" id="operation_id" class="form-select @error('operation_id') is-invalid @enderror">
                                <option value="">-- Aucune opération liée --</option>
                                @foreach(($operations ?? collect()) as $op)
                                    <option value="{{ $op->id }}" {{ old('operation_id') == $op->id ? 'selected' : '' }}>
                                        [{{ $op->numero_ordre ?? '#OP-'.$op->id }}] {{ $op->titre }} - {{ number_format($op->montant ?? 0, 0, ',', ' ') }} FCFA
                                    </option>
                                @endforeach
                            </select>
                            @error('operation_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Permet de rattacher cette dépense à une opération validée.</div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror"
                                      id="description" name="description" rows="3">{{ old('description') }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label for="justificatif" class="form-label">Justificatif (PDF, JPG, PNG - max 5Mo)</label>
                            <input type="file" class="form-control @error('justificatif') is-invalid @enderror"
                                   id="justificatif" name="justificatif" accept=".pdf,.jpg,.jpeg,.png">
                            <div class="form-text">Téléversez un justificatif pour cette dépense (facultatif).</div>
                            @error('justificatif')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="{{ route('tresorerie.depenses.index') }}" class="btn btn-light">
                                <i class="fas fa-arrow-left me-1"></i> Retour
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Enregistrer la dépense
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0">Aide</h5>
                </div>
                <div class="card-body">
                    <h6>Comment remplir ce formulaire ?</h6>
                    <p class="small">
                        <i class="fas fa-info-circle text-primary me-2"></i>
                        Tous les champs marqués d'un astérisque (*) sont obligatoires.
                    </p>
                    <h6>Conseils :</h6>
                    <ul class="small">
                        <li>Vérifiez que le montant est correct avant de valider</li>
                        <li>Joignez un justificatif pour faciliter le suivi</li>
                        <li>Vérifiez que la date de la dépense est exacte</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Initialisation des sélecteurs avec Select2 si nécessaire
    $(document).ready(function() {
        $('#expense_id, #caisse_id, #compte_comptable_id, #mode_paiement').select2({
            theme: 'bootstrap-5',
            width: '100%'
        });

        function syncFromExpenseSelect() {
            const opt = $('#expense_id').find('option:selected');
            const intitule = opt.data('intitule') || '';
            const montant = opt.data('montant') || '';
            const date = opt.data('date') || '';
            const fournisseur = opt.data('fournisseur') || '';

            $('#libelle').val(intitule);
            $('#montant').val(montant);
            if (date) {
                $('#date_depense').val(date);
                $('#date_depense_hidden').val(date);
            }
            if (!$('#beneficiaire').val() && fournisseur) {
                $('#beneficiaire').val(fournisseur);
            }
        }

        $('#expense_id').on('change', syncFromExpenseSelect);
        syncFromExpenseSelect();

        // Validation du formulaire
        if ($.fn && $.fn.validate) {
            $('form').validate({
                rules: {
                    montant: {
                        min: 0.01
                    },
                    date_depense: {
                        date: true
                    }
                },
                messages: {
                    montant: {
                        min: "Le montant doit être supérieur à 0"
                    }
                },
                errorElement: 'div',
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

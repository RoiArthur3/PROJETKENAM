<?php $__env->startSection('title', 'Nouvelle Dépense de Caisse'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?php echo e(route('dashboard')); ?>">Tableau de bord</a></li>
                    <li class="breadcrumb-item"><a href="<?php echo e(route('tresorerie.depenses.index')); ?>">Dépenses de caisse</a></li>
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
                    <?php if(session('error')): ?>
                        <div class="alert alert-danger">
                            <?php echo e(session('error')); ?>

                        </div>
                    <?php endif; ?>

                    <?php if($errors->any()): ?>
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $message): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li><?php echo e($message); ?></li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <form action="<?php echo e(route('tresorerie.depenses.store')); ?>" method="POST" enctype="multipart/form-data">
                        <?php echo csrf_field(); ?>

                        <div class="mb-3">
                            <label for="expense_id" class="form-label">Dépense comptable approuvée *</label>
                            <select name="expense_id" id="expense_id" class="form-select <?php $__errorArgs = ['expense_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                                <option value="">Sélectionner une dépense approuvée</option>
                                <?php $__currentLoopData = $expenses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $expense): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option
                                        value="<?php echo e($expense->id); ?>"
                                        data-intitule="<?php echo e(e($expense->intitule ?? '')); ?>"
                                        data-montant="<?php echo e($expense->montant); ?>"
                                        data-date="<?php echo e(optional($expense->date_depense)->format('Y-m-d')); ?>"
                                        data-fournisseur="<?php echo e(e($expense->fournisseur ?? '')); ?>"
                                        <?php echo e((string) old('expense_id', $selectedExpenseId ?? request('expense_id')) === (string) $expense->id ? 'selected' : ''); ?>

                                    >
                                        <?php echo e($expense->reference ?? ('DEP-' . $expense->id)); ?> - <?php echo e($expense->intitule ?? '—'); ?> (<?php echo e(number_format($expense->montant, 0, ',', ' ')); ?> FCFA)
                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <?php $__errorArgs = ['expense_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            <div class="form-text">La trésorerie ne peut payer que des dépenses comptables approuvées.</div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="caisse_id" class="form-label">Caisse *</label>
                                <select name="caisse_id" id="caisse_id" class="form-select <?php $__errorArgs = ['caisse_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                                    <option value="">Sélectionner une caisse</option>
                                    <?php $__currentLoopData = $caisses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $caisse): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($caisse->id); ?>" <?php echo e(old('caisse_id') == $caisse->id ? 'selected' : ''); ?>>
                                            <?php echo e($caisse->nom); ?> (<?php echo e($caisse->code ?? $caisse->id); ?>)
                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                                <?php $__errorArgs = ['caisse_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>

                            <div class="col-md-6">
                                <label for="compte_comptable_id" class="form-label">Compte Comptable *</label>
                                <select name="compte_comptable_id" id="compte_comptable_id" class="form-select <?php $__errorArgs = ['compte_comptable_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                                    <option value="">Sélectionner un compte</option>
                                    <?php $__currentLoopData = $comptes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $compte): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($compte->id); ?>" <?php echo e((string) old('compte_comptable_id', $selectedCompteId ?? request('compte_comptable_id')) === (string) $compte->id ? 'selected' : ''); ?>>
                                            <?php echo e($compte->numero); ?> - <?php echo e($compte->intitule); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                                <?php $__errorArgs = ['compte_comptable_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="libelle" class="form-label">Libellé</label>
                            <input type="text" class="form-control" id="libelle" name="libelle" value="<?php echo e(old('libelle')); ?>">
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="montant" class="form-label">Montant (FCFA) *</label>
                                <div class="input-group">
                                    <input type="number" step="0.01" min="0.01" class="form-control" id="montant" name="montant" value="<?php echo e(old('montant')); ?>" required>
                                    <span class="input-group-text">FCFA</span>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label for="date_depense" class="form-label">Date de la dépense *</label>
                                <input type="date" class="form-control" id="date_depense" value="<?php echo e(old('date_depense', now()->format('Y-m-d'))); ?>" readonly>
                                <input type="hidden" name="date_depense" id="date_depense_hidden" value="<?php echo e(old('date_depense', now()->format('Y-m-d'))); ?>">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="beneficiaire" class="form-label">Bénéficiaire *</label>
                            <input type="text" class="form-control <?php $__errorArgs = ['beneficiaire'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                   id="beneficiaire" name="beneficiaire" value="<?php echo e(old('beneficiaire')); ?>" required>
                            <?php $__errorArgs = ['beneficiaire'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="mode_paiement" class="form-label">Mode de paiement *</label>
                                <select name="mode_paiement" id="mode_paiement" class="form-select <?php $__errorArgs = ['mode_paiement'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                                    <?php $__currentLoopData = \App\Models\DepenseCaisse::getModesPaiement(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($value); ?>" <?php echo e(old('mode_paiement') == $value ? 'selected' : ''); ?>>
                                            <?php echo e($label); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                                <?php $__errorArgs = ['mode_paiement'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>

                            <div class="col-md-6">
                                <label for="reference_paiement" class="form-label">Référence de paiement</label>
                                <input type="text" class="form-control <?php $__errorArgs = ['reference_paiement'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                       id="reference_paiement" name="reference_paiement" value="<?php echo e(old('reference_paiement')); ?>">
                                <?php $__errorArgs = ['reference_paiement'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="operation_id" class="form-label">Opération liée (optionnel)</label>
                            <select name="operation_id" id="operation_id" class="form-select <?php $__errorArgs = ['operation_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                <option value="">-- Aucune opération liée --</option>
                                <?php $__currentLoopData = ($operations ?? collect()); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $op): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($op->id); ?>" <?php echo e(old('operation_id') == $op->id ? 'selected' : ''); ?>>
                                        [<?php echo e($op->numero_ordre ?? '#OP-'.$op->id); ?>] <?php echo e($op->titre); ?> - <?php echo e(number_format($op->montant ?? 0, 0, ',', ' ')); ?> FCFA
                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <?php $__errorArgs = ['operation_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            <div class="form-text">Permet de rattacher cette dépense à une opération validée.</div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                      id="description" name="description" rows="3"><?php echo e(old('description')); ?></textarea>
                            <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <div class="mb-4">
                            <label for="justificatif" class="form-label">Justificatif (PDF, JPG, PNG - max 5Mo)</label>
                            <input type="file" class="form-control <?php $__errorArgs = ['justificatif'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                   id="justificatif" name="justificatif" accept=".pdf,.jpg,.jpeg,.png">
                            <div class="form-text">Téléversez un justificatif pour cette dépense (facultatif).</div>
                            <?php $__errorArgs = ['justificatif'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>

                        <div class="d-flex justify-content-between">
                            <a href="<?php echo e(route('tresorerie.depenses.index')); ?>" class="btn btn-light">
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
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
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
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\kenam\resources\views/tresorerie/depenses/create.blade.php ENDPATH**/ ?>
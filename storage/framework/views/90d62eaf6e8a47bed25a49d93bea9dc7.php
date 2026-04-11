<?php $__env->startSection('title', __('operations.create.page_title') . ' - KENAM SERVICES'); ?>

<?php $__env->startPush('styles'); ?>
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
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <?php if($errors->any()): ?>
        <div class="alert alert-danger">
            <ul class="mb-0">
                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li><?php echo e($error); ?></li>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </div>
    <?php endif; ?>

    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-plus-circle me-2"></i><?php echo e(__('operations.create.form_title')); ?>

                    </h6>
                    <div class="d-flex align-items-center gap-2">
                        <a href="<?php echo e(route('types-operations.index')); ?>" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-cog me-1"></i> <?php echo e(__('operations.create.manage_types')); ?>

                        </a>
                        <span class="badge bg-secondary"><?php echo e(__('operations.create.draft_badge')); ?></span>
                    </div>
                </div>
                <div class="card-body">
                    <?php if(session('error')): ?>
                        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                            <i class="fas fa-times-circle me-2"></i>
                            <strong><?php echo e(__('operations.create.error_title')); ?></strong><br>
                            <?php echo e(session('error')); ?>

                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <form id="operationForm" method="POST" action="<?php echo e(route('operations.store')); ?>" enctype="multipart/form-data">
                        <?php echo csrf_field(); ?>

                        <!-- Section: Informations Générales -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="text-primary mb-3">
                                    <i class="fas fa-info-circle me-2"></i>Informations Générales
                                </h5>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="titre" class="form-label required"><?php echo e(__('operations.fields.title')); ?> <span style="color:red">*</span></label>
                                <input type="text" name="titre" class="form-control <?php $__errorArgs = ['titre'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                       id="titre" value="<?php echo e(old('titre')); ?>" required>
                                <?php $__errorArgs = ['titre'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                <div class="form-text"><?php echo e(__('operations.create.title_help')); ?></div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="priorite" class="form-label required"><?php echo e(__('operations.fields.priority')); ?> <span style="color:red">*</span></label>
                                <select class="form-select <?php $__errorArgs = ['priorite'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" name="priorite" id="priorite" required>
                                    <option value="basse" <?php echo e(old('priorite') == 'basse' ? 'selected' : ''); ?>><?php echo e(__('operations.traductions.basse') ?? 'Basse'); ?></option>
                                    <option value="moyenne" <?php echo e(old('priorite', 'moyenne') == 'moyenne' ? 'selected' : ''); ?>><?php echo e(__('operations.traductions.moyenne') ?? 'Moyenne'); ?></option>
                                    <option value="haute" <?php echo e(old('priorite') == 'haute' ? 'selected' : ''); ?>><?php echo e(__('operations.traductions.haute') ?? 'Haute'); ?></option>
                                    <option value="urgente" <?php echo e(old('priorite') == 'urgente' ? 'selected' : ''); ?>><?php echo e(__('operations.traductions.urgente') ?? 'Urgente'); ?></option>
                                </select>
                                <?php $__errorArgs = ['priorite'];
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

                            <div class="col-md-6 mb-3">
                                <label for="type_operation_id" class="form-label required">Type d'opération <span style="color:red">*</span></label>
                                <select class="form-select <?php $__errorArgs = ['type_operation_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" name="type_operation_id" id="type_operation_id" required>
                                    <option value=""><?php echo e(__('operations.create.type_select')); ?></option>
                                    <?php $__currentLoopData = ($types ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($type->id); ?>" <?php echo e(old('type_operation_id') == $type->id ? 'selected' : ''); ?>>
                                            <?php echo e($type->libelle); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                                <?php $__errorArgs = ['type_operation_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                <?php if(($types ?? collect())->isEmpty()): ?>
                                    <div class="alert alert-warning mt-2 d-flex justify-content-between align-items-center">
                                        <div>
                                            <i class="fas fa-exclamation-triangle me-2"></i>
                                            <?php echo e(__('operations.create.no_types')); ?>

                                        </div>
                                        <a href="<?php echo e(route('types-operations.create')); ?>" class="btn btn-sm btn-primary">
                                            <i class="fas fa-plus me-1"></i> <?php echo e(__('operations.create.new_type')); ?>

                                        </a>
                                    </div>
                                <?php else: ?>
                                    <div class="form-text"><?php echo e(__('operations.create.types_help')); ?></div>
                                <?php endif; ?>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="destinataire_principal" class="form-label required">
                                    <i class="fas fa-user-tie text-primary me-1"></i>Destinataire Principal <span style="color:red">*</span>
                                </label>
                                <select class="form-select <?php $__errorArgs = ['destinataire_principal'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" name="destinataire_principal" id="destinataire_principal" required>
                                    <option value="">-- Sélectionner le destinataire principal --</option>
                                    <?php $__currentLoopData = ($services ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $service): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($service->id); ?>" <?php echo e(old('destinataire_principal') == $service->id ? 'selected' : ''); ?>>
                                            <?php echo e($service->nom); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                                <?php $__errorArgs = ['destinataire_principal'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                <div class="form-text">
                                    <i class="fas fa-info-circle text-primary me-1"></i>Service principal concerné par l'opération
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="engin_id" class="form-label">
                                    <i class="fas fa-cogs text-primary me-1"></i>Engin
                                </label>
                                <select class="form-select <?php $__errorArgs = ['engin_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" name="engin_id" id="engin_id">
                                    <option value="">-- Sélectionner l'engin --</option>
                                    <?php $__currentLoopData = ($engins ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $engin): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($engin->id); ?>" <?php echo e(old('engin_id') == $engin->id ? 'selected' : ''); ?>>
                                            <?php echo e($engin->immatriculation ?? ('Engin #' . $engin->id)); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                                <?php $__errorArgs = ['engin_id'];
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

                            <div class="col-md-6 mb-3">
                                <label for="fournisseur_id" class="form-label">
                                    <i class="fas fa-truck text-primary me-1"></i>Fournisseur
                                </label>
                                <select class="form-select <?php $__errorArgs = ['fournisseur_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" name="fournisseur_id" id="fournisseur_id">
                                    <option value="">-- Sélectionner le fournisseur --</option>
                                    <?php $__currentLoopData = ($fournisseurs ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $fournisseur): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($fournisseur->id); ?>" <?php echo e(old('fournisseur_id') == $fournisseur->id ? 'selected' : ''); ?>>
                                            <?php echo e($fournisseur->raison_sociale); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                                <?php $__errorArgs = ['fournisseur_id'];
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

                            <div class="col-md-6 mb-3">
                                <label for="echeance" class="form-label">Échéance</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="far fa-calendar-alt"></i></span>
                                    <input type="date" name="echeance" class="form-control <?php $__errorArgs = ['echeance'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                           id="echeance" value="<?php echo e(old('echeance')); ?>" min="<?php echo e(now()->format('Y-m-d')); ?>">
                                </div>
                                <?php $__errorArgs = ['echeance'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                <div class="form-text">
                                    <i class="fas fa-info-circle text-primary me-1"></i>Date limite pour l'exécution (optionnel)
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="montant" class="form-label">Montant</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-money-bill-wave"></i></span>
                                    <input type="number" name="montant" class="form-control <?php $__errorArgs = ['montant'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                           id="montant" value="<?php echo e(old('montant')); ?>" min="0" step="0.01">
                                </div>
                                <?php $__errorArgs = ['montant'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                <div class="form-text"><?php echo e(__('operations.create.amount_help')); ?></div>
                            </div>

                            <div class="col-md-12 mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea name="description" id="description" rows="4" class="form-control <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                          placeholder="<?php echo e(__('operations.create.desc_placeholder')); ?>"><?php echo e(old('description')); ?></textarea>
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
                                <select class="form-select <?php $__errorArgs = ['validateur_1'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" name="validateur_1" id="validateur_1">
                                    <option value="">-- Sélectionner le validateur 1 --</option>
                                    <?php $__currentLoopData = ($services ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $service): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($service->id); ?>" <?php echo e(old('validateur_1') == $service->id ? 'selected' : ''); ?>>
                                            <?php echo e($service->nom); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                                <?php $__errorArgs = ['validateur_1'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                <div class="form-text">
                                    <i class="fas fa-envelope text-success me-1"></i>Recevra l'email en premier
                                </div>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="validateur_2" class="form-label">
                                    <i class="fas fa-user-check text-info me-1"></i>Validateur 2
                                </label>
                                <select class="form-select <?php $__errorArgs = ['validateur_2'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" name="validateur_2" id="validateur_2">
                                    <option value="">-- Sélectionner le validateur 2 --</option>
                                    <?php $__currentLoopData = ($services ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $service): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($service->id); ?>" <?php echo e(old('validateur_2') == $service->id ? 'selected' : ''); ?>>
                                            <?php echo e($service->nom); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                                <?php $__errorArgs = ['validateur_2'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                <div class="form-text">
                                    <i class="fas fa-envelope text-info me-1"></i>Recevra l'email après validation du 1er
                                </div>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="validateur_3" class="form-label">
                                    <i class="fas fa-user-check text-secondary me-1"></i>Validateur 3 (Destinataire Final)
                                </label>
                                <select class="form-select <?php $__errorArgs = ['validateur_3'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" name="validateur_3" id="validateur_3">
                                    <option value="">-- Sélectionner le validateur 3 --</option>
                                    <?php $__currentLoopData = ($services ?? []); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $service): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($service->id); ?>" <?php echo e(old('validateur_3') == $service->id ? 'selected' : ''); ?>>
                                            <?php echo e($service->nom); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                                <?php $__errorArgs = ['validateur_3'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                <div class="form-text">
                                    <i class="fas fa-envelope text-secondary me-1"></i>Recevra l'email en dernier
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="services_cc" class="form-label">Services en CC</label>
                                <small class="text-muted">Services qui recevront une copie de la requête</small>
                                <div class="border rounded p-3 bg-light">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <small class="text-muted"><?php echo e(__('operations.create.add_cc_help')); ?></small>
                                        <button type="button" class="btn btn-sm btn-primary" onclick="addCcService()">
                                            <i class="fas fa-plus me-1"></i><?php echo e(__('operations.create.add_cc_btn')); ?>

                                        </button>
                                    </div>
                                    <div id="ccServicesList"></div>
                                    <input type="hidden" name="services_cc" id="services_cc" value="">
                                </div>
                                <?php if(($services ?? collect())->isEmpty()): ?>
                                    <div class="alert alert-warning mt-2 d-flex justify-content-between align-items-center">
                                        <div>
                                            <i class="fas fa-exclamation-triangle me-2"></i>
                                            Aucun service opérationnel trouvé. Créez-en au préalable dans les paramètres.
                                        </div>
                                        <a href="<?php echo e(route('admin.services.create')); ?>" class="btn btn-sm btn-info">
                                            <i class="fas fa-plus me-1"></i> Nouveau service
                                        </a>
                                    </div>
                                <?php else: ?>
                                    <div class="form-text">
                                        <i class="fas fa-check-circle text-success me-1"></i>
                                        <?php echo e(count($services)); ?> service(s) opérationnel(s) disponible(s)
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <h5 class="text-primary mb-3"><?php echo e(__('operations.create.attachments_title')); ?></h5>
                            </div>
                            <div class="col-md-12 mb-3">
                                <label for="fichiers" class="form-label required"><?php echo e(__('operations.create.files_label')); ?> <span style="color:red">*</span></label>
                                <input type="file" name="fichiers[]" id="fichiers" class="form-control <?php $__errorArgs = ['fichiers'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" multiple required>
                                <?php $__errorArgs = ['fichiers'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                <div class="form-text"><?php echo e(__('operations.create.files_help')); ?></div>
                                <div id="filePreview" class="file-preview"></div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12 text-center">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-paper-plane me-2"></i><?php echo e(__('operations.create.submit_btn')); ?>

                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    let availableServices = <?php echo json_encode($services ?? [], 15, 512) ?>;
    let selectedCcServices = [];

    function addCcService() {
        const serviceId = 'cc_service_' + Date.now();
        const serviceHtml = `
            <div id="${serviceId}" class="d-flex align-items-center mb-2 p-2 bg-white rounded border">
                <select class="form-select form-select-sm me-2" onchange="updateCcServices()">
                    <option value=""><?php echo e(__('operations.create.choose_service')); ?></option>
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
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\kenam\resources\views/operations/create.blade.php ENDPATH**/ ?>
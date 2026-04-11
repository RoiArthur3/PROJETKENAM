

<?php $__env->startSection('title', 'Grand Journal'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
        <div>
            <h1 class="h3 mb-1"><i class="fas fa-book-open me-2 text-primary"></i>Grand Journal</h1>
            <p class="text-muted mb-0">Toutes les écritures manuelles et flux comptables consolidés.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="<?php echo e(route('comptabilite.rapports.grand-journal.export-excel', request()->query())); ?>" class="btn btn-outline-success">
                <i class="fas fa-file-excel me-2"></i>Export Excel
            </a>
            <a href="<?php echo e(route('comptabilite.rapports.grand-journal.export-pdf', request()->query())); ?>" class="btn btn-outline-danger">
                <i class="fas fa-file-pdf me-2"></i>Export PDF
            </a>
            <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#createJournalModal">
                <i class="fas fa-folder-plus me-2"></i>Créer un journal
            </button>
            <a href="<?php echo e(route('comptabilite.ecritures.create')); ?>" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Nouvelle écriture
            </a>
        </div>
    </div>

    <?php if(session('success')): ?>
        <div class="alert alert-success"><?php echo e(session('success')); ?></div>
    <?php endif; ?>

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card shadow-sm border-0 bg-primary-subtle">
                <div class="card-body">
                    <div class="text-muted text-uppercase small mb-2">Écritures</div>
                    <div class="fs-4 fw-bold"><?php echo e(number_format($stats['total'], 0, ',', ' ')); ?></div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0 bg-success-subtle">
                <div class="card-body">
                    <div class="text-muted text-uppercase small mb-2">Total débit</div>
                    <div class="fs-4 fw-bold"><?php echo e(number_format($stats['total_debit'], 0, ',', ' ')); ?> FCFA</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0 bg-info-subtle">
                <div class="card-body">
                    <div class="text-muted text-uppercase small mb-2">Total crédit</div>
                    <div class="fs-4 fw-bold"><?php echo e(number_format($stats['total_credit'], 0, ',', ' ')); ?> FCFA</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card shadow-sm border-0 bg-light">
                <div class="card-body">
                    <div class="text-muted text-uppercase small mb-2">Journaux actifs</div>
                    <div class="fs-4 fw-bold"><?php echo e(number_format($stats['journaux_actifs'], 0, ',', ' ')); ?></div>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white">
            <h5 class="mb-0">Filtres</h5>
        </div>
        <div class="card-body">
            <form method="GET" action="<?php echo e(route('comptabilite.ecritures.index')); ?>" class="row g-3 align-items-end">
                <div class="col-md-2">
                    <label class="form-label">Date début</label>
                    <input type="date" name="date_debut" class="form-control" value="<?php echo e($filters['date_debut']); ?>">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Date fin</label>
                    <input type="date" name="date_fin" class="form-control" value="<?php echo e($filters['date_fin']); ?>">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Journal</label>
                    <select name="journal" class="form-select">
                        <option value="">Tous</option>
                        <?php $__currentLoopData = $journaux; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $journal): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($journal->code); ?>" <?php echo e($filters['journal'] === $journal->code ? 'selected' : ''); ?>>
                                <?php echo e($journal->code); ?> - <?php echo e($journal->libelle); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Source</label>
                    <select name="source" class="form-select">
                        <option value="">Toutes</option>
                        <?php $__currentLoopData = ['manual' => 'Manuelle', 'journal_vente_import' => 'Journal ventes importé', 'facture' => 'Facture', 'encaissement' => 'Encaissement', 'depense' => 'Dépense', 'vehicule_revenue' => 'Revenu véhicule', 'vehicule_expense' => 'Charge véhicule']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $code => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($code); ?>" <?php echo e($filters['source'] === $code ? 'selected' : ''); ?>><?php echo e($label); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Recherche</label>
                    <input type="text" name="search" class="form-control" value="<?php echo e($filters['search']); ?>" placeholder="Référence, libellé, compte...">
                </div>
                <div class="col-md-1 d-grid">
                    <button type="submit" class="btn btn-primary">Filtrer</button>
                </div>
            </form>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-lg-9">
            <div class="card shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Lignes du grand journal</h5>
                    <span class="badge bg-light text-dark border"><?php echo e($entries->count()); ?> lignes</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover table-sm align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Date</th>
                                    <th>Journal</th>
                                    <th>Référence</th>
                                    <th>Libellé</th>
                                    <th>Débit</th>
                                    <th>Crédit</th>
                                    <th class="text-end">Montant</th>
                                    <th>Source</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $entries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $entry): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr class="entry-row"
                                        style="cursor:pointer"
                                        data-source-type="<?php echo e($entry['source_type']); ?>"
                                        data-source-id="<?php echo e($entry['source_id']); ?>"
                                        data-date="<?php echo e($entry['date']->format('d/m/Y')); ?>"
                                        data-reference="<?php echo e(e($entry['reference'])); ?>"
                                        data-journal-code="<?php echo e($entry['journal_code']); ?>"
                                        data-journal-libelle="<?php echo e(e($entry['journal_libelle'])); ?>"
                                        data-libelle="<?php echo e(e($entry['libelle'])); ?>"
                                        data-compte-debit="<?php echo e($entry['compte_debit']); ?>"
                                        data-compte-credit="<?php echo e($entry['compte_credit']); ?>"
                                        data-montant="<?php echo e(number_format($entry['montant'], 0, ',', ' ')); ?>"
                                        data-piece="<?php echo e(e($entry['piece'])); ?>"
                                    >
                                        <td><?php echo e($entry['date']->format('d/m/Y')); ?></td>
                                        <td>
                                            <span class="badge bg-secondary"><?php echo e($entry['journal_code']); ?></span>
                                            <div class="small text-muted"><?php echo e($entry['journal_libelle']); ?></div>
                                        </td>
                                        <td>
                                            <div><?php echo e($entry['reference']); ?></div>
                                            <?php if($entry['piece']): ?>
                                                <div class="small text-muted">Pièce: <?php echo e($entry['piece']); ?></div>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo e($entry['libelle']); ?></td>
                                        <td><code><?php echo e($entry['compte_debit']); ?></code></td>
                                        <td><code><?php echo e($entry['compte_credit']); ?></code></td>
                                        <td class="text-end fw-semibold"><?php echo e(number_format($entry['montant'], 0, ',', ' ')); ?> FCFA</td>
                                        <td>
                                            <span class="badge <?php echo e($entry['editable'] ? 'bg-primary' : 'bg-light text-dark border'); ?>">
                                                <?php echo e($entry['source_type']); ?>

                                            </span>
                                        </td>
                                        <td>
                                            <?php if($entry['editable']): ?>
                                                <div class="d-flex gap-1" onclick="event.stopPropagation()">
                                                    <a href="<?php echo e(route('comptabilite.ecritures.edit', $entry['source_id'])); ?>" class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <form method="POST" action="<?php echo e(route('comptabilite.ecritures.destroy', $entry['source_id'])); ?>" onsubmit="return confirm('Supprimer cette écriture ?')">
                                                        <?php echo csrf_field(); ?>
                                                        <?php echo method_field('DELETE'); ?>
                                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            <?php else: ?>
                                                <span class="text-muted small">Générée</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td colspan="9" class="text-center text-muted py-4">Aucune écriture trouvée sur la période.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Répartition par journal</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <?php $__empty_1 = true; $__currentLoopData = $parJournal; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $code => $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <li class="list-group-item d-flex justify-content-between align-items-start px-0">
                                <div>
                                    <div class="fw-semibold"><?php echo e($code); ?> - <?php echo e($item['journal']); ?></div>
                                    <div class="small text-muted"><?php echo e($item['count']); ?> lignes</div>
                                </div>
                                <span class="fw-semibold"><?php echo e(number_format($item['montant'], 0, ',', ' ')); ?></span>
                            </li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <li class="list-group-item px-0 text-muted">Aucune donnée.</li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="createJournalModal" tabindex="-1" aria-labelledby="createJournalModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-secondary-subtle">
                <h5 class="modal-title" id="createJournalModalLabel">
                    <i class="fas fa-folder-plus me-2"></i>Créer un journal comptable
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="<?php echo e(route('comptabilite.journaux.store')); ?>">
                <?php echo csrf_field(); ?>
                <div class="modal-body">
                    <?php if($errors->any()): ?>
                        <div class="alert alert-danger py-2">
                            <ul class="mb-0 ps-3 small">
                                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li><?php echo e($error); ?></li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Code <span class="text-danger">*</span></label>
                            <input type="text" name="code" class="form-control <?php $__errorArgs = ['code'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                   value="<?php echo e(old('code')); ?>" placeholder="Ex: OD2" maxlength="10" required>
                            <?php $__errorArgs = ['code'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Type <span class="text-danger">*</span></label>
                            <select name="type" class="form-select <?php $__errorArgs = ['type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                                <?php $__currentLoopData = ['achat' => 'Achats', 'vente' => 'Ventes', 'banque' => 'Banque', 'caisse' => 'Caisse', 'od' => 'Opérations diverses', 'paie' => 'Paie', 'fiscal' => 'Fiscal', 'autre' => 'Autre']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $value => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($value); ?>" <?php echo e(old('type') === $value ? 'selected' : ''); ?>><?php echo e($label); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <?php $__errorArgs = ['type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Libellé <span class="text-danger">*</span></label>
                            <input type="text" name="libelle" class="form-control <?php $__errorArgs = ['libelle'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                   value="<?php echo e(old('libelle')); ?>" placeholder="Journal des achats fournisseurs" required>
                            <?php $__errorArgs = ['libelle'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Description</label>
                            <textarea name="description" rows="2" class="form-control" placeholder="Description optionnelle..."><?php echo e(old('description')); ?></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Couleur</label>
                            <input type="color" name="couleur" class="form-control form-control-color w-100"
                                   value="<?php echo e(old('couleur', '#0d6efd')); ?>" title="Couleur du journal">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Icône FontAwesome</label>
                            <input type="text" name="icone" class="form-control" value="<?php echo e(old('icone', 'fas fa-book')); ?>" placeholder="fas fa-book">
                        </div>
                        <div class="col-12">
                            <div class="form-check">
                                <input type="checkbox" name="actif" id="modal-actif" class="form-check-input" value="1" checked>
                                <label for="modal-actif" class="form-check-label">Journal actif</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>


<div class="modal fade" id="detailEcritureModal" tabindex="-1" aria-labelledby="detailEcritureModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-primary-subtle">
                <h5 class="modal-title" id="detailEcritureModalLabel">
                    <i class="fas fa-file-invoice me-2"></i>Détail de l'écriture
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                
                <div id="detail-quick" class="row g-3 mb-3">
                    <div class="col-6 col-md-3">
                        <div class="text-muted small">Date</div>
                        <div class="fw-semibold" id="dq-date">—</div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="text-muted small">Journal</div>
                        <div class="fw-semibold" id="dq-journal">—</div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="text-muted small">Référence</div>
                        <div class="fw-semibold" id="dq-reference">—</div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="text-muted small">Pièce comptable</div>
                        <div class="fw-semibold" id="dq-piece">—</div>
                    </div>
                    <div class="col-12">
                        <div class="text-muted small">Libellé</div>
                        <div class="fw-semibold" id="dq-libelle">—</div>
                    </div>
                    <div class="col-4">
                        <div class="text-muted small">Compte débit</div>
                        <code id="dq-debit">—</code>
                    </div>
                    <div class="col-4">
                        <div class="text-muted small">Compte crédit</div>
                        <code id="dq-credit">—</code>
                    </div>
                    <div class="col-4">
                        <div class="text-muted small">Montant</div>
                        <div class="fw-bold text-primary" id="dq-montant">—</div>
                    </div>
                </div>

                <hr>

                
                <div id="detail-loading" class="text-center py-3">
                    <div class="spinner-border spinner-border-sm text-primary me-2"></div>
                    Chargement des détails…
                </div>
                <div id="detail-extra" class="d-none">
                    <div id="detail-source-info" class="mb-3"></div>
                    <div id="detail-audit" class="mb-0"></div>
                </div>
                <div id="detail-error" class="d-none alert alert-warning py-2 small mb-0"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
(function () {
    'use strict';

    const detailUrl = '<?php echo e(route('comptabilite.ecritures.detail')); ?>';

    function escHtml(str) {
        if (str == null) return '—';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }

    function fmtVal(val) {
        if (val == null || val === '' || val === false) return '<span class="text-muted">—</span>';
        if (typeof val === 'object') return '<pre class="small mb-0 text-break">' + escHtml(JSON.stringify(val, null, 2)) + '</pre>';
        return escHtml(String(val));
    }

    function renderSourceInfo(detail) {
        if (!detail) return '';
        const rows = [
            ['Type de source',   detail.type],
            ['Date',             detail.date],
            ['Journal',          detail.journal],
            ['Statut',          detail.statut],
            ['Catégorie',       detail.categorie],
            ['Mode paiement',   detail.mode_paiement],
            ['Description',     detail.description],
            ['Créé par',        detail.cree_par],
            ['Modifié par',     detail.modifie_par],
            ['Date création',   detail.created_at],
            ['Dernière modif.', detail.updated_at],
        ].filter(([, v]) => v != null && v !== '' && v !== '—');

        if (!rows.length) return '';

        let html = '<h6 class="mb-2"><i class="fas fa-database me-1 text-secondary"></i>Informations source</h6>'
                 + '<table class="table table-sm table-bordered small mb-0"><tbody>';
        rows.forEach(([label, val]) => {
            html += `<tr><th class="text-nowrap bg-light" style="width:35%">${escHtml(label)}</th><td>${fmtVal(val)}</td></tr>`;
        });
        html += '</tbody></table>';
        return html;
    }

    function renderAuditTrail(trail) {
        if (!trail || !trail.length) return '';

        let html = '<h6 class="mt-3 mb-2"><i class="fas fa-history me-1 text-secondary"></i>Historique des actions</h6>';
        trail.forEach(function (log) {
            const badgeClass = log.action === 'created' ? 'bg-success'
                             : log.action === 'deleted'  ? 'bg-danger'
                             : 'bg-warning text-dark';
            html += `<div class="border rounded p-2 mb-2 small">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="badge ${badgeClass}">${escHtml(log.action)}</span>
                    <span class="text-muted">${escHtml(log.date)} — ${escHtml(log.par)}${log.ip_address ? ' (' + escHtml(log.ip_address) + ')' : ''}</span>
                </div>`;
            if (log.old_values && Object.keys(log.old_values).length) {
                html += `<div><strong>Avant :</strong> ${fmtVal(log.old_values)}</div>`;
            }
            if (log.new_values && Object.keys(log.new_values).length) {
                html += `<div><strong>Après :</strong> ${fmtVal(log.new_values)}</div>`;
            }
            html += '</div>';
        });
        return html;
    }

    document.addEventListener('click', function (e) {
        const row = e.target.closest('tr.entry-row');
        if (!row) return;

        const ds       = row.dataset;
        const modal    = document.getElementById('detailEcritureModal');
        const bsModal  = bootstrap.Modal.getOrCreateInstance(modal);

        // Remplir la partie rapide
        document.getElementById('dq-date').textContent      = ds.date      || '—';
        document.getElementById('dq-journal').textContent   = ds.journalCode + ' – ' + (ds.journalLibelle || '');
        document.getElementById('dq-reference').textContent = ds.reference  || '—';
        document.getElementById('dq-piece').textContent     = ds.piece      || '—';
        document.getElementById('dq-libelle').textContent   = ds.libelle    || '—';
        document.getElementById('dq-debit').textContent     = ds.compteDebit  || '—';
        document.getElementById('dq-credit').textContent    = ds.compteCredit || '—';
        document.getElementById('dq-montant').textContent   = (ds.montant || '—') + ' FCFA';

        // Réinitialiser la section asynchrone
        document.getElementById('detail-loading').classList.remove('d-none');
        document.getElementById('detail-extra').classList.add('d-none');
        document.getElementById('detail-error').classList.add('d-none');

        bsModal.show();

        // Charger les détails via AJAX
        const params = new URLSearchParams({
            source_type: ds.sourceType,
            source_id:   ds.sourceId,
        });

        fetch(detailUrl + '?' + params.toString(), {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            credentials: 'same-origin',
        })
        .then(function (res) { return res.json().then(function (data) { return { ok: res.ok, data }; }); })
        .then(function ({ ok, data }) {
            document.getElementById('detail-loading').classList.add('d-none');
            if (!ok) {
                const errEl = document.getElementById('detail-error');
                errEl.textContent = data.error || 'Impossible de charger les détails.';
                errEl.classList.remove('d-none');
                return;
            }
            document.getElementById('detail-source-info').innerHTML = renderSourceInfo(data.detail);
            document.getElementById('detail-audit').innerHTML       = renderAuditTrail(data.audit_trail);
            document.getElementById('detail-extra').classList.remove('d-none');
        })
        .catch(function () {
            document.getElementById('detail-loading').classList.add('d-none');
            const errEl = document.getElementById('detail-error');
            errEl.textContent = 'Erreur réseau lors du chargement des détails.';
            errEl.classList.remove('d-none');
        });
    });
})();
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\kenam\resources\views/comptabilite/ecritures.blade.php ENDPATH**/ ?>
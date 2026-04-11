<?php $__env->startSection('title', 'Décaissements - KENAM SERVICES'); ?>

<?php $__env->startSection('content'); ?>
<div class="content-wrapper">
    <?php if(session('success')): ?>
        <div class="alert alert-success alert-dismissible fade show mb-4">
            <i class="fas fa-check-circle me-2"></i>
            <strong>Succès !</strong> <?php echo e(session('success')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if(session('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show mb-4">
            <i class="fas fa-exclamation-circle me-2"></i>
            <strong>Erreur !</strong> <?php echo e(session('error')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Décaissements de Caisse</h1>
        <div class="d-flex gap-2">
            <a href="<?php echo e(route('comptabilite.dashboard')); ?>" class="btn btn-outline-secondary">
                <i class="fas fa-chart-line me-2"></i>Comptabilité
            </a>
            <a href="<?php echo e(route('tresorerie.caisses.index')); ?>" class="btn btn-outline-info">
                <i class="fas fa-cash-register me-2"></i>Caisse
            </a>
            <a href="<?php echo e(route('tresorerie.virements')); ?>" class="btn btn-outline-warning">
                <i class="fas fa-exchange-alt me-2"></i>Virements
            </a>
            <a href="<?php echo e(route('tresorerie.decaissements.create')); ?>" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i>Nouveau Décaissement
            </a>
        </div>
    </div>

    <!-- KPIs Décaissements -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Total Décaissements</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo e($decaissements->count()); ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-money-bill-wave fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Montant Total</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo e(number_format($decaissements->sum('montant'), 0, ',', ' ')); ?> FCFA</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-coins fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">En Attente</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo e($decaissements->where('statut', 'en attente')->count()); ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Validés</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?php echo e($decaissements->where('statut', 'validé')->count()); ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tableau des Décaissements -->
    <div class="card">
        <div class="card-body">
            <h5 class="card-title mb-4">Liste des Décaissements</h5>

            <!-- Filtres -->
            <div class="row mb-3">
                <div class="col-md-3">
                    <input type="text" class="form-control" placeholder="Rechercher un décaissement..." id="searchInput">
                </div>
                <div class="col-md-2">
                    <select class="form-select" id="statutFilter">
                        <option value="">Tous les statuts</option>
                        <option value="validé">Validé</option>
                        <option value="en attente">En attente</option>
                        <option value="annulé">Annulé</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-select" id="caisseFilter">
                        <option value="">Toutes les caisses</option>
                        <option value="Caisse Principale">Caisse Principale</option>
                        <option value="Caisse Secondaire">Caisse Secondaire</option>
                        <option value="Caisse Mobile">Caisse Mobile</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select class="form-select" id="beneficiaireFilter">
                        <option value="">Tous les bénéficiaires</option>
                        <option value="BUREAU PLUS">BUREAU PLUS</option>
                        <option value="TOTAL CI">TOTAL CI</option>
                        <option value="Transporteur">Transporteur</option>
                        <option value="Restaurant">Restaurant</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-outline-info" onclick="exportDecaissements()">
                        <i class="fas fa-download me-2"></i>Exporter
                    </button>
                </div>
            </div>

            <!-- Tableau -->
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Référence</th>
                            <th>Date</th>
                            <th>Libellé</th>
                            <th>Bénéficiaire</th>
                            <th>Caisse</th>
                            <th>Opération</th>
                            <th>Montant</th>
                            <th>Motif / Description</th>
                            <th>Créé par</th>
                            <th>Mode Paiement</th>
                            <th>Statut Chèque</th>
                            <th>Statut</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $decaissements; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $decaissement): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar-circle bg-secondary text-white me-2" style="width: 30px; height: 30px; font-size: 12px;">
                                        <?php echo e(strtoupper(substr($decaissement->reference, -3))); ?>

                                    </div>
                                    <div>
                                        <div class="fw-bold"><?php echo e($decaissement->reference); ?></div>
                                        <div class="text-muted small">ID: <?php echo e($decaissement->id); ?></div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div>
                                    <div><?php echo e(\Carbon\Carbon::parse($decaissement->date_depense)->format('d/m/Y')); ?></div>
                                    <div class="text-muted small"><?php echo e(\Carbon\Carbon::parse($decaissement->date_depense)->format('H:i')); ?></div>
                                </div>
                            </td>
                            <td><?php echo e($decaissement->libelle); ?></td>
                            <td>
                                <?php
                                    $beneficiaireTexte = null;
                                    if (!empty($decaissement->notes) && preg_match('/B[ée]n[ée]ficiaire\s*:\s*(.+)/iu', $decaissement->notes, $matches)) {
                                        $beneficiaireTexte = trim(strtok($matches[1], "\n"));
                                    }
                                ?>

                                <?php if($decaissement->beneficiaire_id && $decaissement->beneficiaire): ?>
                                    <?php echo e($decaissement->beneficiaire->name ?? $decaissement->beneficiaire->nom ?? 'Bénéficiaire'); ?>

                                <?php elseif(!empty($beneficiaireTexte)): ?>
                                    <?php echo e($beneficiaireTexte); ?>

                                <?php else: ?>
                                    <em class="text-muted">Non défini</em>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge bg-info">
                                    <?php if(isset($decaissement->caisse) && $decaissement->caisse): ?>
                                        <?php echo e($decaissement->caisse->nom); ?>

                                    <?php else: ?>
                                        <em>Non défini</em>
                                    <?php endif; ?>
                                </span>
                            </td>
                            <td>
                                <?php if($decaissement->operation): ?>
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle">
                                        <?php echo e($decaissement->operation->numero_ordre ?? '#OP-'.$decaissement->operation->id); ?>

                                    </span>
                                    <div class="small text-muted mt-1"><?php echo e(\Illuminate\Support\Str::limit($decaissement->operation->titre, 30)); ?></div>
                                <?php else: ?>
                                    <em class="text-muted">Aucune</em>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="fw-bold text-danger"><?php echo e(number_format($decaissement->montant, 0, ',', ' ')); ?> FCFA</span>
                            </td>
                            <td>
                                <small><?php echo e(Str::limit($decaissement->description ?? $decaissement->motif_rejet, 50)); ?></small>
                            </td>
                            <td>
                                <?php if($decaissement->createur): ?>
                                    <small><?php echo e($decaissement->createur->name); ?></small>
                                <?php else: ?>
                                    <em class="text-muted">-</em>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge bg-<?php echo e($decaissement->mode_paiement === 'cheque' ? 'info' : 'secondary'); ?>">
                                    <?php echo e(ucfirst($decaissement->mode_paiement ?? 'Autre')); ?>

                                </span>
                            </td>
                            <td>
                                <?php if($decaissement->mode_paiement === 'cheque' || !empty($decaissement->numero_cheque)): ?>
                                    <?php if($decaissement->est_encaisse): ?>
                                        <span class="badge bg-success">
                                            <i class="fas fa-check-circle me-1"></i>Encaissé
                                        </span>
                                        <?php if($decaissement->date_encaissement): ?>
                                            <div class="small text-muted mt-1"><?php echo e($decaissement->date_encaissement->format('d/m/Y')); ?></div>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <span class="badge bg-warning text-dark">
                                            <i class="fas fa-clock me-1"></i>Non encaissé
                                        </span>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <em class="text-muted">—</em>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge bg-<?php echo e($decaissement->statut == 'validé' ? 'success' : 'warning'); ?>">
                                    <?php echo e(ucfirst($decaissement->statut)); ?>

                                </span>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="<?php echo e(route('tresorerie.decaissements.show', $decaissement->id)); ?>" class="btn btn-sm btn-outline-primary" title="Voir">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="<?php echo e(route('tresorerie.decaissements.edit', $decaissement->id)); ?>" class="btn btn-sm btn-outline-warning" title="Modifier">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <?php if(($decaissement->mode_paiement === 'cheque' || !empty($decaissement->numero_cheque)) && $decaissement->numero_cheque): ?>
                                        <form action="<?php echo e(route('tresorerie.decaissements.encaisser', $decaissement->id)); ?>" method="POST" class="d-inline" onsubmit="return confirm('Marquer ce chèque comme <?php echo e($decaissement->est_encaisse ? 'non encaissé' : 'encaissé'); ?> ?');">
                                            <?php echo csrf_field(); ?>
                                            <button type="submit" class="btn btn-sm <?php echo e($decaissement->est_encaisse ? 'btn-outline-danger' : 'btn-outline-success'); ?>" title="<?php echo e($decaissement->est_encaisse ? 'Marquer non encaissé' : 'Marquer encaissé'); ?>">
                                                <i class="fas <?php echo e($decaissement->est_encaisse ? 'fa-times-circle' : 'fa-check-circle'); ?>"></i>
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                    <form action="<?php echo e(route('tresorerie.decaissements.destroy', $decaissement->id)); ?>" method="POST" class="d-inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce décaissement ?');">
                                        <?php echo csrf_field(); ?>
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Supprimer">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Fonction de recherche
    function filterDecaissements() {
        const search = document.getElementById('searchInput').value.toLowerCase();
        const statut = document.getElementById('statutFilter').value;
        const caisse = document.getElementById('caisseFilter').value;
        const beneficiaire = document.getElementById('beneficiaireFilter').value;

        const rows = document.querySelectorAll('tbody tr');

        rows.forEach(row => {
            const cells = row.getElementsByTagName('td');
            const rowText = row.textContent.toLowerCase();

            const matchesSearch = !search || rowText.includes(search);
            const matchesStatut = !statut || cells[9]?.textContent.toLowerCase().includes(statut);
            const matchesCaisse = !caisse || cells[4]?.textContent.toLowerCase().includes(caisse.toLowerCase());
            const matchesBeneficiaire = !beneficiaire || cells[3]?.textContent.toLowerCase().includes(beneficiaire.toLowerCase());

            row.style.display = (matchesSearch && matchesStatut && matchesCaisse && matchesBeneficiaire) ? '' : 'none';
        });
    }

    // Fonction d'export
    function exportDecaissements() {
        const decaissements = Array.from(document.querySelectorAll('tbody tr')).map(row => {
            const cells = row.getElementsByTagName('td');
            return {
                reference: cells[0]?.textContent.trim(),
                date: cells[1]?.textContent.trim(),
                libelle: cells[2]?.textContent.trim(),
                beneficiaire: cells[3]?.textContent.trim(),
                caisse: cells[4]?.textContent.trim(),
                operation: cells[5]?.textContent.trim(),
                montant: cells[6]?.textContent.trim(),
                motif: cells[7]?.textContent.trim(),
                responsable: cells[8]?.textContent.trim(),
                statut: cells[9]?.textContent.trim()
            };
        });

        console.log('Décaissements à exporter:', decaissements);
        alert('Export des décaissements simulé (voir console)');
    }

    // Écouteurs d'événements
    document.getElementById('searchInput').addEventListener('input', filterDecaissements);
    document.getElementById('statutFilter').addEventListener('change', filterDecaissements);
    document.getElementById('caisseFilter').addEventListener('change', filterDecaissements);
    document.getElementById('beneficiaireFilter').addEventListener('change', filterDecaissements);
});
</script>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\kenam\resources\views/tresorerie/decaissements.blade.php ENDPATH**/ ?>
<!-- Header Navigation -->
<nav class="navbar-custom">
    <div class="header-content">

        <div class="header-title">
            <?php echo $__env->yieldContent('header-title', 'Tableau de bord'); ?>
        </div>

        <div class="header-actions">
            <?php if(auth()->check()): ?>
                <?php ($canAccessValidations = auth()->user()->canAccessModule('validations')); ?>
                <!-- Cloche de Notification -->
                <?php if($canAccessValidations): ?>
                <a href="<?php echo e(route('validations.pending')); ?>" class="nav-button position-relative" title="Validations en attente">
                    <i class="fas fa-bell <?php echo e(($pendingValidationsCount ?? 0) > 0 ? 'text-warning' : ''); ?>"></i>
                    <?php if(($pendingValidationsCount ?? 0) > 0): ?>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.65rem; margin-top: 5px; margin-left: -5px;">
                            <?php echo e($pendingValidationsCount); ?>

                            <span class="visually-hidden">notifications en attente</span>
                        </span>
                    <?php endif; ?>
                    <span class="d-none d-md-inline ms-1">Notifications</span>
                </a>
                <?php endif; ?>

                <!-- Bouton Paramètres (visible uniquement pour superadmin) -->
                <?php ($userRole = strtolower((string) (auth()->user()->role ?? ''))); ?>
                <?php if($userRole === 'superadmin'): ?>
                <a href="/parametrage" class="nav-button">
                    <i class="fas fa-cog"></i>
                    <span>Paramètres</span>
                </a>
                <?php endif; ?>

                <!-- Profil & Déconnexion -->
                <a href="<?php echo e(route('profile.dashboard')); ?>" class="nav-button" title="Mon profil">
                    <i class="fas fa-user-circle"></i>
                    <span><?php echo e(auth()->user()->name); ?></span>
                </a>

                <a href="#" class="nav-button" title="Déconnexion"
                   onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="fas fa-power-off"></i>
                    <span class="d-none d-md-inline">Déconnexion</span>
                </a>

                <form id="logout-form" action="/logout" method="POST" style="display: none;">
                    <?php echo csrf_field(); ?>
                </form>
            <?php endif; ?>
        </div>
    </div>
</nav>
<?php /**PATH C:\laragon\www\kenam\resources\views/layouts/navigation.blade.php ENDPATH**/ ?>
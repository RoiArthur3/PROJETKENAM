<?php if(auth()->check() && auth()->user()): ?>
    <?php echo $__env->make('layouts.sidebar-superadmin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php else: ?>
    <?php echo $__env->make('layouts.sidebar-user', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
<?php endif; ?>
<?php /**PATH C:\laragon\www\kenam\resources\views/layouts/sidebar.blade.php ENDPATH**/ ?>
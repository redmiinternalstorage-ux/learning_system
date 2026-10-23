<?php $__env->startSection('title', 'Authentication — Learning Curve'); ?>

<?php $__env->startSection('content'); ?>
<div class="mt-2">
    <!-- Container where active form is rendered -->
    <div class="w-full">
        <div id="login-tab-content">
            <?php echo $__env->make('auth.login-content', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        </div>
        <div id="register-tab-content" class="hidden">
            <?php echo $__env->make('auth.register-content', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    // Auto-switch to register tab if coming from /register route or if register fields have old input
    <?php if(request()->is('register') || old('name')): ?>
        document.addEventListener('DOMContentLoaded', function() {
            switchAuthTab('register');
        });
    <?php endif; ?>
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.auth', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\a\lj\learning_system\resources\views/auth/index.blade.php ENDPATH**/ ?>
 
<?php $__env->startSection('content'); ?>
<br>
Hello,
<br>
<br>
<div>You have made a forgot password request. Please use this otp for change password<br>
    <br>
    <b>Otp : </b>  <?php echo e($otp); ?>

</div>
<br>
 
<?php $__env->stopSection(); ?>  


<?php echo $__env->make('admin.mail.default', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/stagepicker/resources/views/admin/mail/forget_mobile.blade.php ENDPATH**/ ?>
 
<?php $__env->startSection('content'); ?>
<br>
Hi <?php echo e($name); ?>,
<br>
<br>
<div>
You have successfully registered with Acthound, please verify your email and signing in using the credentials at sign up.

 <br />
    <b>Otp : </b> <?php echo e($otp); ?>

</div>
<br>
 
<?php $__env->stopSection(); ?>  


<?php echo $__env->make('admin.mail.default', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/stagepicker/resources/views/admin/mail/verificaton_email_mobile.blade.php ENDPATH**/ ?>
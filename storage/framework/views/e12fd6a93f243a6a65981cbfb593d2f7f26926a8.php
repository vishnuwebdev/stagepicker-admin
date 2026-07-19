 
<?php $__env->startSection('content'); ?>
<br>
Hello,
<br>
<br>
<div><?php
        if($update) {

             echo 'Your Login Details has been changed. New login credentials is as follows.<br>';
        } else {
           echo 'You have successfully registered with Stagepicker as Staff. Login credentials is as follows.<br>';
        }
     ?>
    <br>
     Email : <?php echo e($email); ?>

     <br>
     Password : <?php echo e($password); ?>

     <br>
    <a href="<?php echo e($link); ?>">Login Here</a>
</div>
<br>
 
<?php $__env->stopSection(); ?>  


<?php echo $__env->make('admin.mail.default', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/stagepicker/resources/views/admin/mail/admin_new_user_welcome.blade.php ENDPATH**/ ?>
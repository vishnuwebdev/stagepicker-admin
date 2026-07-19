<?php $__env->startSection('content'); ?>
<link href="https://designreset.com/cork/ltr/demo4/assets/css/users/account-setting.css" rel="stylesheet" type="text/css" />
            <div class="layout-px-spacing">                
                <div class="account-settings-container layout-top-spacing">
                    <div class="account-content">
                        <?php if($message = Session::get('success')): ?>
                            <div class="alert alert-success mb-4" role="alert">
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x close" data-dismiss="alert"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg></button>
                                <strong><?php echo e($message); ?></strong></button>
                            </div>
                        <?php endif; ?>
                        <div class="scrollspy-example" data-spy="scroll" data-target="#account-settings-scroll" data-offset="-100">
                            <div class="row">
                                <div class="col-xl-12 col-lg-12 col-md-12 layout-spacing">
                                    <form id="general-info" class="section general-info" action="<?php echo e(url('admin/updateprofile')); ?>">
                                        <?php echo csrf_field(); ?>
                                        <div class="info">
                                            <h6 class="">General Information</h6>
                                            <div class="row">
                                                <div class="col-lg-11 mx-auto">
                                                    <div class="row">
                                                        <div class="col-xl-12 col-lg-12 col-md-12 mt-md-12 mt-12">
                                                            <div class="forms"> 
                                                                <div class="row">
                                                                    <div class="col-sm-6">
                                                                        <div class="form-group">
                                                                            <label for="name">Username</label>
                                                                            <input type="text" class="form-control mb-4" id="name" name="name" placeholder="Full Name" value="<?php echo e($user['name']); ?>" required>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            <label for="phone">Phone</label>
                                                                            <input type="text" class="form-control mb-4" id="phone" name="phone" placeholder="Write your phone number here" value="<?php echo e($user['phone']); ?>">
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="form-group">
                                                                    <label for="profession">Email</label>
                                                                    <input type="text" class="form-control mb-4" id="Email" placeholder="Email" value="<?php echo e($user['email']); ?>" readonly>
                                                                </div>
                                                                <div class="form-group">
                                                                     <button id="multiple-messages" class="btn btn-dark">Save Changes</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
           
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/stagepicker/resources/views/admin/profile.blade.php ENDPATH**/ ?>
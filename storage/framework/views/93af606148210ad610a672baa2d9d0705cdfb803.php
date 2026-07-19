<?php $__env->startSection('content'); ?>
            <div class="containers">
                <div class="container">
                    <div class="row">
                         
                        <div id="flFormsGrid" class="col-lg-12 layout-spacing">
                            <div class="statbox widget box box-shadow">
                                <div class="widget-header">
                                    <div class="row">
                                        <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                                            <h4>Create Crew Role</h4>
                                        </div>                                                                
                                    </div>
                                </div>
                                <div class="widget-content widget-content-area">
                                    <form action="<?php echo e(url('admin/crewrole/store')); ?>" method="POST" enctype="multipart/form-data">
                                        <?php echo csrf_field(); ?>
                                        <div class="form-group mb-4">
                                            <label for="role_title">Role Title</label>
                                            <input type="text" class="form-control" name="role_title" id="role_title" placeholder="Enter Role Title" value="<?php echo e(old('role_title', isset($role) ? $role->role_title : '')); ?>" required>
                                            <?php if($errors->has('role_title')): ?>
                                                <span class="text-danger"><?php echo e($errors->first('role_title')); ?></span>
                                            <?php endif; ?>
                                        </div>
                                        
                                    
                                        
                                         
                                        
                                      <button type="submit" class="btn btn-primary mt-3">Create</button>
                                    </form>

                                </div>
                            </div>
                        </div>
                    </div>

                    
                </div>
            </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/stagepicker/resources/views/admin/crewrole/add.blade.php ENDPATH**/ ?>
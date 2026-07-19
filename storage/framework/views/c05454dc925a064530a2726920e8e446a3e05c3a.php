<?php $__env->startSection('content'); ?>
            <div class="containers">
                <div class="container">
                    <div class="row">
                         
                        <div id="flFormsGrid" class="col-lg-12 layout-spacing">
                            <div class="statbox widget box box-shadow">
                                <div class="widget-header">
                                    <div class="row">
                                        <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                                            <h4>Create Role</h4>
                                        </div>                                                                
                                    </div>
                                </div>
                                <div class="widget-content widget-content-area">
                                    <form action="<?php echo e(url('admin/role/storeroletype')); ?>" method="POST" enctype="multipart/form-data">
                                        <?php echo csrf_field(); ?>
                                        <div class="form-group mb-4">
                                            <label for="title">Title</label>
                                            <input type="text" class="form-control" name="title" id="title" placeholder="Enter Title" value="<?php echo e(old('title', isset($role) ? $role->title : '')); ?>" required>
                                            <?php if($errors->has('title')): ?>
                                                <span class="text-danger"><?php echo e($errors->first('title')); ?></span>
                                            <?php endif; ?>
                                        </div>
										
										<div class="form-group mb-4">
                                            <label for="status">Status</label>
                                            <select class="form-control" id="status" name="status">
												<option value="">Select Status</option>
												<option value="1">Active</option>
												<option value="2">In Active</option> 
											</select>
                                            <?php if($errors->has('status')): ?>
                                                <span class="text-danger"><?php echo e($errors->first('status')); ?></span>
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
<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/stagepicker/resources/views/admin/role/add_roletype.blade.php ENDPATH**/ ?>
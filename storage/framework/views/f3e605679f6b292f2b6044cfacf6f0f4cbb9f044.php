<?php $__env->startSection('content'); ?>
<div class="containers">
                <div class="container">
                    <div class="row">
                        <div id="flFormsGrid" class="col-lg-12 layout-spacing">
                            <div class="statbox widget box box-shadow">
                                <div class="widget-header">
                                    <div class="row">
                                        <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                                            <h4>Create Category</h4>
                                        </div>                                                                
                                    </div>
                                </div>
                                <div class="widget-content widget-content-area">
                                    <form action="<?php echo e(url('admin/update-category',$category->id)); ?>" method="POST" enctype="multipart/form-data">
                                        <?php echo csrf_field(); ?>
                                        <input type="hidden" name="category_id" value="<?php echo  $category->id; ?>">
                                        <div class="form-group mb-4">
                                            <label for="category_name">Category Name</label>
                                            <input type="text" class="form-control" name="category_name" id="category_name" placeholder="Enter Category Name" value="<?php echo e(old('category_name', isset($category) ? $category->category_name : '')); ?>">
                                            <?php if($errors->has('category_name')): ?>
                                                <span class="text-danger"><?php echo e($errors->first('category_name')); ?></span>
                                            <?php endif; ?>
                                        </div>
										<div class="form-group mb-4">
                                            <label for="type">Type</label>
                                            <select class="form-control" id="type" name="type">
												
												<option value="0" <?php if($category->type == '0'): ?> selected <?php endif; ?>> Audition/Photography</option> 
														
												<option value="1" <?php if($category->type == '1'): ?> selected <?php endif; ?>> Production Crew</option>  
												
											</select>
                                            <?php if($errors->has('type')): ?>
                                                <span class="text-danger"><?php echo e($errors->first('type')); ?></span>
                                            <?php endif; ?>
                                        </div>
										
                                      <button type="submit" class="btn btn-primary mt-3">Update</button>
                                    </form>

                                </div>
                            </div>
                        </div>
                    </div>

                    
                </div>
            </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/stagepicker/resources/views/admin/category/edit.blade.php ENDPATH**/ ?>
<?php $__env->startSection('content'); ?>
<div class="containers">
                <div class="container">
                    <div class="row">
                        <div id="flFormsGrid" class="col-lg-12 layout-spacing">
                            <div class="statbox widget box box-shadow">
                                <div class="widget-header">
                                    <div class="row">
                                        <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                                            <h4>Edit Skill</h4>
                                        </div>                                                                
                                    </div>
                                </div>
                                <div class="widget-content widget-content-area">
                                    <form action="<?php echo e(url('admin/update-skill',$skill->id)); ?>" method="POST" enctype="multipart/form-data">
                                        <?php echo csrf_field(); ?>
                                        <input type="hidden" name="skill_id" value="<?php echo  $skill->id; ?>">
                                        <div class="form-group mb-4">
                                            <label for="skill_name">Skill Name</label>
                                            <input type="text" class="form-control" name="skill_name" id="skill_name" placeholder="Enter skill Name" value="<?php echo e(old('skill_name', isset($skill) ? $skill->skill_name : '')); ?>">
                                            <?php if($errors->has('skill_name')): ?>
                                                <span class="text-danger"><?php echo e($errors->first('skill_name')); ?></span>
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
<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/stagepicker/resources/views/admin/skill/edit.blade.php ENDPATH**/ ?>
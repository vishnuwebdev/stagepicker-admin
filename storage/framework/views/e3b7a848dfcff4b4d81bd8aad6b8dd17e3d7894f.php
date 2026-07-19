<?php $__env->startSection('content'); ?>
<div class="containers">
	<div class="container">
		<div class="row">
			<div id="flFormsGrid" class="col-lg-12 layout-spacing">
				<div class="statbox widget box box-shadow">
					<div class="widget-header">
						<div class="row">
							<div class="col-xl-12 col-md-12 col-sm-12 col-12">
								<h4>Edit Crew Role</h4>
							</div>                                                                
						</div>
					</div>
					<div class="widget-content widget-content-area">
						<form action="<?php echo e(url('admin/update-crewrole',$role->id)); ?>" method="POST" enctype="multipart/form-data">
							<?php echo csrf_field(); ?>
							<input type="hidden" name="role_id" value="<?php echo  $role->id; ?>">
							<div class="form-group mb-4">
								<label for="role_title">Role Title</label>
								<input type="text" class="form-control" name="role_title" id="role_title" placeholder="Enter Role Title" value="<?php echo e(old('role_title', isset($role) ? $role->role_title : '')); ?>" required>
								<?php if($errors->has('role_title')): ?>
									<span class="text-danger"><?php echo e($errors->first('role_title')); ?></span>
								<?php endif; ?>
							</div>
							
							<div class="form-group mb-4">
								<label for="role_type">Role Type</label>
								<select class="form-control" id="role_type" name="role_type" required>
									<option value="">Select Role Type</option> 
									<?php if($roletype->count()): ?>
		
										<?php $__currentLoopData = $roletype; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tag): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
											<option value="<?php echo e($tag->id); ?>"  
											<?php if($role->role_type == $tag->id): ?> selected <?php endif; ?> > <?php echo e($tag->title); ?></option>    
										<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
										
									<?php endif; ?>
									
								</select>
								<?php if($errors->has('role_type')): ?>
									<span class="text-danger"><?php echo e($errors->first('role_type')); ?></span>
								<?php endif; ?>
							</div>
							
							<div class="form-group mb-4">
								<label for="category">Category</label>
								<select class="form-control" id="category" name="category" required>
									<option value="">Select Category</option> 
									<?php if($category->count()): ?>
		
										<?php $__currentLoopData = $category; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tag): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
											<option value="<?php echo e($tag->id); ?>"  
											<?php if($role->category == $tag->id): ?> selected <?php endif; ?> > <?php echo e($tag->category_name); ?></option>    
										<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
										
									<?php endif; ?>
									
								</select>
								<?php if($errors->has('category')): ?>
									<span class="text-danger"><?php echo e($errors->first('category')); ?></span>
								<?php endif; ?>
							</div>
							 
							<div class="form-group mb-4">
								<label for="skills">Skills</label>
								<input type="text" class="form-control" name="skills" id="skills" placeholder="Enter Skills" value="<?php echo e(old('skills', isset($role) ? $role->skills : '')); ?>" required>
								<?php if($errors->has('skills')): ?>
									<span class="text-danger"><?php echo e($errors->first('skills')); ?></span>
								<?php endif; ?>
							</div>
							
							<div class="form-group mb-4">
								<label for="description">Description</label>
								<textarea name="description" class="form-control"><?php echo e($role->description); ?></textarea>
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
<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/stagepicker/resources/views/admin/crewrole/edit.blade.php ENDPATH**/ ?>
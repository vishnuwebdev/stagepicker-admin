
<?php $__env->startSection('content'); ?>
<style type="text/css">
	.select2-container .select2-selection--multiple{
		    min-height: 43px !important;
	}
	.select2-container .select2-search--inline .select2-search__field{
		margin-top: 9px !important;
		margin-left: 20px !important;
	}
</style>
<div class="layout-px-spacing">
	<div class="row layout-top-spacing layout-spacing">
			<?php if(count($errors) > 0): ?>
		   
		        <div class="col-md-12">
		            <div class="alert alert-danger">
		                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
		                    <div><?php echo e($error); ?></div>
		                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
		            </div>
		        </div>
		   
		<?php endif; ?>
			<div id="flFormsGrid" class="col-lg-12 layout-spacing">
				<div class="statbox widget box box-shadow">
					<div class="widget-header">
						<div class="row">
							<div class="col-xl-12 col-md-12 col-sm-12 col-12">
								<h4>Edit User</h4>
							</div>                                                                
						</div>
					</div>
					<div class="widget-content widget-content-area">
						<form action="<?php echo e(url('admin/adminuser/update')); ?>" method="POST" enctype="multipart/form-data">
							<?php echo csrf_field(); ?>
							<input type="hidden" name="user_id" value="<?php echo e($uid); ?>">
							<div class="row">
							    <div class="col-md-4">
							       <div class="form-group mb-4">
								<label for="name">Name</label>
								<input type="text" class="form-control" name="name" id="name" placeholder="Enter Name" value="<?php echo e(old('name', isset($userdetail) ? $userdetail->name : '')); ?>">
								<?php if($errors->has('name')): ?>
									<span class="text-danger"><?php echo e($errors->first('name')); ?></span>
								<?php endif; ?>
							</div> 
							</div>
							
							<div class="col-md-4">
								<div class="form-group mb-4">
									<label for="email">Email</label>
									<input readonly type="text" class="form-control" name="email" id="email" placeholder="Email" value="<?php echo e(old('email', isset($userdetail) ? $userdetail->email : '')); ?>">
									<?php if($errors->has('email')): ?>
										<span class="text-danger"><?php echo e($errors->first('email')); ?></span>
									<?php endif; ?>
								</div>
							</div>
							<div class="col-md-4">
							       	
							<div class="form-group mb-4">
								<label for="phone">Password</label>
								<input type="password" class="form-control" name="password" id="password" placeholder="Password" value="">
								<?php if($errors->has('password')): ?>
									<span class="text-danger"><?php echo e($errors->first('password')); ?></span>
								<?php endif; ?>
							</div> 
							</div>
							<div class="col-md-6">
							       	
							<div class="form-group mb-4">
								<label for="phone">Phone</label>
								<input type="text" class="form-control" name="phone" id="phone" placeholder="phone" value="<?php echo e(old('phone', isset($userdetail) ? $userdetail->phone : '')); ?>">
								<?php if($errors->has('phone')): ?>
									<span class="text-danger"><?php echo e($errors->first('phone')); ?></span>
								<?php endif; ?>
							</div> 
							</div>
							<div class="col-md-4">
							      <div class="form-group mb-4">
								<label for="status">Status</label>
								<select class="form-control" id="status" name="status">
									<option value="">Select Status</option>
									<option value="1"  <?php echo ($userdetail->status == 1)?"selected":"";?>>Active</option>
									<option value="0" <?php echo ($userdetail->status == 2)?"selected":"";?>>In Active</option> 
								</select>
								<?php if($errors->has('status')): ?>
									<span class="text-danger"><?php echo e($errors->first('status')); ?></span>
								<?php endif; ?>
							</div>  
						</div>

						<hr>
                        <div class="col-md-12">

                        	<h4>Permission</h4>

                        	<table class="table">
                        		<tr>
                        			<th>Modual Name</th>
                        			<th style="text-align: center;">View</th>
                        			<th style="text-align: center;">Add</th>
                        			<th style="text-align: center;">Edit</th>
                        			<th style="text-align: center;">Delete</th>
                        		</tr>
                            
                               <?php
                                 $permission = json_decode($userdetail->permissions);
                               
                                 foreach($permission as $key => $per) {

                               ?>
                               
                                  <tr>
                        			<td><?php echo e($per->module); ?></td>
                        			<input type="hidden" name="permission[<?=$key?>][module]" value="<?php echo e($per->module); ?>">
                        			<input type="hidden" name="permission[<?=$key?>][moduleId]" value="<?php echo e($per->moduleId); ?>">
                        			<td style="text-align: center;"><input type="checkbox" <?=($per->view) ? 'checked' : ''; ?> name="permission[<?=$key?>][view]" value="1"></td>
                        			<td style="text-align: center;"><input type="checkbox" <?=($per->add) ? 'checked' : ''; ?> name="permission[<?=$key?>][add]" value="1"></td>
                        			<td style="text-align: center;"><input type="checkbox" <?=($per->edit) ? 'checked' : ''; ?> name="permission[<?=$key?>][edit]" value="1"></td>
                        			<td style="text-align: center;"><input type="checkbox" <?=($per->delete) ? 'checked' : ''; ?> name="permission[<?=$key?>][delete]" value="1"></td>
                        		</tr>

                              <?php } ?>

                        	
                        		
                        	</table>
                        </div>
						

						<div class="col-md-12">
							<button type="submit" class="btn btn-primary mt-3">Update</button>
						</div>
					</div>
					
				</form> 
			</div>
		</div>
	</div>

		
</div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('script'); ?>

	<script type="text/javascript">
        // var skillsString = "<?=$userdetail->skills?>";
        // var roleString = "<?=$userdetail->producer_type?>";

		// var skills = new Array();
		// var roles = new Array();

		// skills = skillsString.split(',');
		// roles = roleString.split(',');

		// console.log("<?=$userdetail->skills?>");

		$('#producer_type').select2({
			placeholder: "Select Role Type",
		});
		$('#skills').select2({
			placeholder: "Select Skills",
		});

	</script>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/stagepicker/resources/views/admin/adminusers/edit.blade.php ENDPATH**/ ?>
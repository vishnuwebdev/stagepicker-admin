
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
						<form action="<?php echo e(url('admin/editUser')); ?>" method="POST" enctype="multipart/form-data">
							<?php echo csrf_field(); ?>
							<input type="hidden" name="user_id" value="<?php echo e($uid); ?>">
							<div class="row">
							    <div class="col-md-6">
							       <div class="form-group mb-4">
								<label for="name">Name</label>
								<input type="text" class="form-control" name="name" id="name" placeholder="Enter Name" value="<?php echo e(old('name', isset($userdetail) ? $userdetail->name : '')); ?>">
								<?php if($errors->has('name')): ?>
									<span class="text-danger"><?php echo e($errors->first('name')); ?></span>
								<?php endif; ?>
							</div> 
							</div>
							
							<?php if($userdetail->user_type == 1){ ?>
							<div class="col-md-6">
								<div class="form-group mb-4">
									<label for="company_name">Stage Name</label>
									<input readonly type="text" class="form-control" name="stage_name" id="stage_name" placeholder="Enter Stage Name" value="<?php echo e(old('stage_name', isset($userdetail) ? $userdetail->stage_name : '')); ?>">
									<?php if($errors->has('stage_name')): ?>
										<span class="text-danger"><?php echo e($errors->first('stage_name')); ?></span>
									<?php endif; ?>
								</div>
							</div>
							<?php } elseif($userdetail->user_type == 2){ ?>
							<div class="col-md-6">
								<div class="form-group mb-4">
									<label for="company_name">Company Name</label>
									<input type="text" class="form-control" name="company_name" id="company_name" placeholder="Enter Company Name" value="<?php echo e(old('company_name', isset($userdetail) ? $userdetail->company_name : '')); ?>">
									<?php if($errors->has('company_name')): ?>
										<span class="text-danger"><?php echo e($errors->first('company_name')); ?></span>
									<?php endif; ?>
								</div>
							</div>
							<?php } ?>
							<div class="col-md-6">
								<div class="form-group mb-4">
									<label for="email">Email</label>
									<input readonly type="text" class="form-control" name="email" id="email" placeholder="Email" value="<?php echo e(old('email', isset($userdetail) ? $userdetail->email : '')); ?>">
									<?php if($errors->has('email')): ?>
										<span class="text-danger"><?php echo e($errors->first('email')); ?></span>
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
									<option value="2" <?php echo ($userdetail->status == 2)?"selected":"";?>>In Active</option> 
								</select>
								<?php if($errors->has('status')): ?>
									<span class="text-danger"><?php echo e($errors->first('status')); ?></span>
								<?php endif; ?>
							</div>  
						</div>
						<?php if($userdetail->user_type == 1){ ?>
						<div class="col-md-4">
						   <div class="form-group mb-4">
								<label for="gender">Gender</label>
								<select class="form-control" id="gender" name="gender">
									<option value="">Select Gender</option>
									<option value="Male"  <?php echo ($userdetail->gender == 'Male')?"selected":"";?>>Male</option>
									<option value="FeMale" <?php echo ($userdetail->gender == 'FeMale')?"selected":"";?>>FeMale</option> 
									<option value="Other" <?php echo ($userdetail->gender == 'Other')?"selected":"";?>>Other</option> 
								</select>
								<?php if($errors->has('gender')): ?>
									<span class="text-danger"><?php echo e($errors->first('gender')); ?></span>
								<?php endif; ?>
							</div> 
						</div>
						
						<?php } ?>
						<div class="col-md-4">
							  <div class="form-group mb-4">
								<label for="union_type">Group Union</label>
								<select class="form-control" id="union_type" name="union_type">
									<option value="">Group Union</option>
									<option value="0"  <?php echo ($userdetail->union_type == 0)?"selected":"";?>>Non - Union</option>
									<option value="1" <?php echo ($userdetail->union_type == 2)?"selected":"";?>>Union</option> 
								</select>
								<?php if($errors->has('union_type')): ?>
									<span class="text-danger"><?php echo e($errors->first('union_type')); ?></span>
								<?php endif; ?>
							</div>  
						</div>

						<div class="col-md-6">
							       	
							<div class="form-group mb-4">
								<label for="age">Age</label>
								<input type="text" class="form-control" name="age" id="age" placeholder="Age" value="<?php echo e(old('age', isset($userdetail) ? $userdetail->age : '')); ?>">
								<?php if($errors->has('age')): ?>
									<span class="text-danger"><?php echo e($errors->first('age')); ?></span>
								<?php endif; ?>
							</div> 
							</div>

							<div class="col-md-6">
							       	
							<div class="form-group mb-4">
								<label for="height">Height</label>
								<input type="height" class="form-control" name="height" id="height" placeholder="Height" value="<?php echo e(old('height', isset($userdetail) ? $userdetail->height : '')); ?>">
								<?php if($errors->has('height')): ?>
									<span class="text-danger"><?php echo e($errors->first('height')); ?></span>
								<?php endif; ?>
							</div> 
							</div>
                        
                        <?php if(!empty($skills)){
                         
                         ?>
						<div class="col-md-6">
						   <div class="form-group mb-4">
								<label for="skills">Skills</label>
								<select multiple class="form-control" id="skills" name="skills[]">
									
									<?php foreach($skills as $crr){ ?>

										<?php
										  $userSkills = ($userdetail->skills) ? explode(',',$userdetail->skills) : [];
										  
                                          if(in_array($crr->skill_name, $userSkills)) {
                                          	echo '<option selected value="'.$crr->skill_name.'">'.$crr->skill_name.'</option>';
                                          } else {
                                          	echo '<option value="'.$crr->skill_name.'">'.$crr->skill_name.'</option>';
                                          }
										?>
										
									<?php } ?> 
								</select>
								
								<?php if($errors->has('skills')): ?>
									<span class="text-danger"><?php echo e($errors->first('skills')); ?></span>
								<?php endif; ?>
							</div>  
						</div>

						<?php } ?>
						
						<?php if(!empty($role_types)){ ?>
						<div class="col-md-6">
							<div class="form-group mb-4">
								<label for="career">Select Role Type</label>
								 
								<select multiple class="form-control" id="producer_type" name="producer_type[]">
									
									<?php foreach($role_types as $crr){ ?>
										<?php
										  $userRoles = ($userdetail->producer_type) ? explode(',',$userdetail->producer_type) : [];
                                          if(in_array($crr->name, $userRoles)) {
                                          	echo '<option selected value="'.$crr->name.'">'.$crr->name.'</option>';
                                          } else {
                                          	echo '<option value="'.$crr->name.'">'.$crr->name.'</option>';
                                          }
										?>
									<?php } ?> 
								</select>
								
								<?php if($errors->has('producer_type')): ?>
									<span class="text-danger"><?php echo e($errors->first('producer_type')); ?></span>
								<?php endif; ?>
							</div>
						</div>
						<?php } ?>

						<div class="col-md-6">
							       	
							<div class="form-group mb-4">
								<label for="website">Website</label>
								<input type="url" class="form-control" name="website" id="website" placeholder="Website" value="<?php echo e(old('website', isset($userdetail) ? $userdetail->website : '')); ?>">
								<?php if($errors->has('website')): ?>
									<span class="text-danger"><?php echo e($errors->first('website')); ?></span>
								<?php endif; ?>
							</div> 
							</div>

							<div class="col-md-6">
							       	
							<div class="form-group mb-4">
								<label for="facebook">Facebook</label>
								<input type="facebook" class="form-control" name="facebook" id="facebook" placeholder="Facebook" value="<?php echo e(old('facebook', isset($userdetail) ? $userdetail->facebook : '')); ?>">
								<?php if($errors->has('facebook')): ?>
									<span class="text-danger"><?php echo e($errors->first('facebook')); ?></span>
								<?php endif; ?>
							</div> 
							</div>

                           <div class="col-md-6">
							       	
							<div class="form-group mb-4">
								<label for="instagram">Instagram</label>
								<input type="text" class="form-control" name="instagram" id="instagram" placeholder="Instagram" value="<?php echo e(old('instagram', isset($userdetail) ? $userdetail->instagram : '')); ?>">
								<?php if($errors->has('instagram')): ?>
									<span class="text-danger"><?php echo e($errors->first('instagram')); ?></span>
								<?php endif; ?>
							</div> 
							</div>

							<div class="col-md-6">
							       	
							<div class="form-group mb-4">
								<label for="tiktok">Tiktok</label>
								<input type="tiktok" class="form-control" name="tiktok" id="tiktok" placeholder="Tiktok" value="<?php echo e(old('tiktok', isset($userdetail) ? $userdetail->tiktok : '')); ?>">
								<?php if($errors->has('tiktok')): ?>
									<span class="text-danger"><?php echo e($errors->first('tiktok')); ?></span>
								<?php endif; ?>
							</div> 
							</div>
							<div class="col-md-6">
							       	
							<div class="form-group mb-4">
								<label for="snapchat">Snapchat</label>
								<input type="snapchat" class="form-control" name="snapchat" id="snapchat" placeholder="Snapchat" value="<?php echo e(old('snapchat', isset($userdetail) ? $userdetail->snapchat : '')); ?>">
								<?php if($errors->has('snapchat')): ?>
									<span class="text-danger"><?php echo e($errors->first('snapchat')); ?></span>
								<?php endif; ?>
							</div> 
							</div> 
							<div class="col-md-6">
							<div class="form-group mb-4">
								<label for="password">Password</label>
								<input type="password" class="form-control" name="password" id="password" placeholder="Password" value="">	
							</div> 
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
<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/vishnusharma/StagePicker App/stagePicker/resources/views/admin/users/edit.blade.php ENDPATH**/ ?>
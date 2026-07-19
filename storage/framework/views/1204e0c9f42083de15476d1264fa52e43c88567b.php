
<?php $__env->startSection('content'); ?>

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
								<h4>Add Admin User</h4>
							</div>                                                                
						</div>
					</div>
					<div class="widget-content widget-content-area">
						<form action="<?php echo e(url('admin/adminuser/store')); ?>" method="POST" enctype="multipart/form-data">
							<?php echo csrf_field(); ?>
							<input type="hidden" name="_token" id="csrf-token" value="<?php echo e(Session::token()); ?>">
							<div class="row">
							    <div class="col-md-4">
							       <div class="form-group mb-4">
								<label for="name">Name</label>
								<input type="text" class="form-control" name="name" id="name" placeholder="Enter Name" value="">
								<?php if($errors->has('name')): ?>
									<span class="text-danger"><?php echo e($errors->first('name')); ?></span>
								<?php endif; ?>
							</div> 
							</div>
							
							<div class="col-md-4">
								<div class="form-group mb-4">
									<label for="email">Email</label>
									<input type="text" class="form-control" name="email" id="email" placeholder="Email" value="">
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
								<input type="text" class="form-control" name="phone" id="phone" placeholder="phone" value="">
								<?php if($errors->has('phone')): ?>
									<span class="text-danger"><?php echo e($errors->first('phone')); ?></span>
								<?php endif; ?>
							</div> 
							</div>
							<div class="col-md-6">
							      <div class="form-group mb-4">
								<label for="status">Status</label>
								<select class="form-control" id="status" name="status">
									<option value="">Select Status</option>
									<option value="1">Active</option>
									<option value="0">In Active</option> 
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
                        		<tr>
                        			<td>User Management</td>
                        			<input type="hidden" name="permission[0][module]" value="User Management">
                        			<input type="hidden" name="permission[0][moduleId]" value="p1">
                        			<td style="text-align: center;"><input type="checkbox" name="permission[0][view]" value="1"></td>
                        			<td style="text-align: center;"><input type="checkbox" name="permission[0][add]" value="1"></td>
                        			<td style="text-align: center;"><input type="checkbox" name="permission[0][edit]" value="1"></td>
                        			<td style="text-align: center;"><input type="checkbox" name="permission[0][delete]" value="1"></td>
                        		</tr>
                        			<tr>
                        			<td>Admin User Management</td>
                        			<input type="hidden" name="permission[1][module]" value="Admin User Management">
                        			<input type="hidden" name="permission[1][moduleId]" value="p2">
                        			<td style="text-align: center;"><input type="checkbox" name="permission[1][view]" value="1"></td>
                        			<td style="text-align: center;"><input type="checkbox" name="permission[0][add]" value="1"></td>
                        			<td style="text-align: center;"><input type="checkbox" name="permission[1][edit]" value="1"></td>
                        			<td style="text-align: center;"><input type="checkbox" name="permission[1][delete]" value="1"></td>
                        		</tr>
                        			<tr>
                        			<td>Audition Control</td>
                        			<input type="hidden" name="permission[2][module]" value="Audition Control">
                        			<input type="hidden" name="permission[2][moduleId]" value="p3">
                        			<td style="text-align: center;"><input type="checkbox" name="permission[2][view]" value="1"></td>
                        			<td style="text-align: center;"><input type="checkbox" name="permission[0][add]" value="1"></td>
                        			<td style="text-align: center;"><input type="checkbox" name="permission[2][edit]" value="1"></td>
                        			<td style="text-align: center;"><input type="checkbox" name="permission[2][delete]" value="1"></td>
                        		</tr>
                        			<tr>
                        			<td>Production Crew</td>
                        			<input type="hidden" name="permission[3][module]" value="Production Crew">
                        			<input type="hidden" name="permission[3][moduleId]" value="p4">
                        			<td style="text-align: center;"><input type="checkbox" name="permission[3][view]" value="1"></td>
                        			<td style="text-align: center;"><input type="checkbox" name="permission[0][add]" value="1"></td>
                        			<td style="text-align: center;"><input type="checkbox" name="permission[3][edit]" value="1"></td>
                        			<td style="text-align: center;"><input type="checkbox" name="permission[3][delete]" value="1"></td>
                        		</tr>
                        			<tr>
                        			<td>Producer Verification</td>
                        			<input type="hidden" name="permission[4][module]" value="Producer Verification">
                        			<input type="hidden" name="permission[4][moduleId]" value="p5">
                        			<td style="text-align: center;"><input type="checkbox" name="permission[4][view]" value="1"></td>
                        			<td style="text-align: center;"><input type="checkbox" name="permission[0][add]" value="1"></td>
                        			<td style="text-align: center;"><input type="checkbox" name="permission[4][edit]" value="1"></td>
                        			<td style="text-align: center;"><input type="checkbox" name="permission[4][delete]" value="1"></td>
                        		</tr>
                        		</tr>
                        			<tr>
                        			<td>Store</td>
                        			<input type="hidden" name="permission[5][module]" value="Store">
                        			<input type="hidden" name="permission[5][moduleId]" value="p6">
                        			<td style="text-align: center;"><input type="checkbox" name="permission[5][view]" value="1"></td>
                        			<td style="text-align: center;"><input type="checkbox" name="permission[0][add]" value="1"></td>
                        			<td style="text-align: center;"><input type="checkbox" name="permission[5][edit]" value="1"></td>
                        			<td style="text-align: center;"><input type="checkbox" name="permission[5][delete]" value="1"></td>>
                        		</tr>
                        		</tr>
                        			<tr>
                        			<td>Category Management</td>
                        			<input type="hidden" name="permission[6][module]" value="Category Management">
                        			<input type="hidden" name="permission[6][moduleId]" value="p7">
                        			<td style="text-align: center;"><input type="checkbox" name="permission[6][view]" value="1"></td>
                        			<td style="text-align: center;"><input type="checkbox" name="permission[0][add]" value="1"></td>
                        			<td style="text-align: center;"><input type="checkbox" name="permission[6][edit]" value="1"></td>
                        			<td style="text-align: center;"><input type="checkbox" name="permission[6][delete]" value="1"></td>
                        		</tr>
                        		</tr>
                        			<tr>
                        			<td>App Setting</td>
                        			<input type="hidden" name="permission[7][module]" value="App Setting">
                        			<input type="hidden" name="permission[7][moduleId]" value="p8">
                        			<td style="text-align: center;"><input type="checkbox" name="permission[7][view]" value="1"></td>
                        			<td style="text-align: center;"><input type="checkbox" name="permission[0][add]" value="1"></td>
                        			<td style="text-align: center;"><input type="checkbox" name="permission[7][edit]" value="1"></td>
                        			<td style="text-align: center;"><input type="checkbox" name="permission[7][delete]" value="1"></td>
                        		</tr>
                        		</tr>
                        			<tr>
                        			<td>Announcement & Reports</td>
                        			<input type="hidden" name="permission[8][module]" value="Announcement & Reports">
                        			<input type="hidden" name="permission[8][moduleId]" value="p9">
                        			<td style="text-align: center;"><input type="checkbox" name="permission[8][view]" value="1"></td>
                        			<td style="text-align: center;"><input type="checkbox" name="permission[0][add]" value="1"></td>
                        			<td style="text-align: center;"><input type="checkbox" name="permission[8][edit]" value="1"></td>
                        			<td style="text-align: center;"><input type="checkbox" name="permission[8][delete]" value="1"></td>
                        		</tr>
                        		
                        	</table>
                        </div>
						
						

						<div class="col-md-12">
							<button type="submit" class="btn btn-primary mt-3">Add</button>
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



<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/stagepicker/resources/views/admin/adminusers/add.blade.php ENDPATH**/ ?>
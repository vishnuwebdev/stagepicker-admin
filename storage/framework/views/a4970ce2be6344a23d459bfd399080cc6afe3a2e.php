
<?php $__env->startSection('content'); ?>
<div class="containers">
	<div class="container">
		<div class="row">
			 
			<div id="flFormsGrid" class="col-lg-12 layout-spacing">
				<div class="statbox widget box box-shadow">
					<div class="widget-header">
						<div class="row">
							<div class="col-xl-12 col-md-12 col-sm-12 col-12">
								<h4>Edit Slider<a href="<?php echo e(url('admin/slider')); ?>" class="btn btn-outline-danger" style="float: right;">Back</a></h4>
							</div>                                                                
						</div>
					</div>
					<div class="widget-content widget-content-area">
						 <?php if($message = Session::get('success')): ?>
							<div class="alert alert-primary mb-4" role="alert">
								<button type="button" class="close" data-dismiss="alert" aria-label="Close"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x close" data-dismiss="alert"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg></button>
								<strong><?php echo e($message); ?></strong></button>
							</div>
						<?php endif; ?>
						<?php if(session('error')): ?>
                        <div class="alert alert-danger alert-dismissible" role="alert">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">×</span></button>
                            <?php echo e(session('error')); ?>

                        </div>
                        <?php endif; ?>
						
						<form action="<?php echo e(url('admin/update-slider')); ?>" method="POST" enctype="multipart/form-data">
							<?php echo csrf_field(); ?>
							<input type="hidden" name="slider_id" value="<?php echo e($editSlider->id); ?>">
							<div class="form-group mb-4">
								<label for="skill_name">Title</label>
								<input type="text" class="form-control" name="title" placeholder="Please Enter Slider Title" required value="<?php echo e($editSlider->title); ?>">
								<?php if($errors->has('title')): ?>
									<span class="text-danger"><?php echo e($errors->first('title')); ?></span>
								<?php endif; ?>
							</div>
							<div class="form-group mb-4">
								<label for="website">Website URL</label>
								<input type="text" class="form-control" name="website" placeholder="Website URL" value="<?php echo e($editSlider->website); ?>" required>
								<?php if($errors->has('website')): ?>
									<span class="text-danger"><?php echo e($errors->first('website')); ?></span>
								<?php endif; ?>
							</div>
							<div class="form-group mb-4">
								<label for="skill_name">Status</label>
								<select name="status" class="selectpicker form-control">
									<option value="1" <?php if($editSlider['status'] == 1){ echo "selected";} ?>>Active</option>
									<option value="0" <?php if($editSlider['status'] == 0){ echo "selected";} ?>>Inactive</option>
								</select>
								<?php if($message = Session::get('error->status')): ?>
								<span role="alert" style="color:#ff0000; font-size:10px; font-weight: 600; line-height: 3; float: left;">
									<?php echo e($message); ?>

								</span>
								<?php endif; ?>
							</div>
							<div class="form-group mb-4"> 
								<div class="custom-file-container" data-upload-id="myFirstImage">
									<label>Upload Slider Image </label><br/> 
									<input type="file" name="image" class="custom-file-container__custom-file__custom-file-input" accept="image/*">
									<br/> <br/> 
									<?php if(!empty($editSlider->image)){ ?> 
										<img src="<?php echo e(asset($editSlider->image)); ?>" width="300px">
									<?php } ?>
										 
								</div>
							</div>
							
							<button type="submit" class="btn btn-primary mt-3">Submit</button>
						</form>

					</div>
				</div>
			</div>
		</div>

		
	</div>
</div>
<?php $__env->stopSection(); ?>  
 

<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/stagepicker/resources/views/admin/Sliders/edit_slider.blade.php ENDPATH**/ ?>
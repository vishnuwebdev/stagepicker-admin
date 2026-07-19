<?php $__env->startSection('content'); ?>
<!--  BEGIN CONTENT AREA  -->
<div class="containers">
<div class="container">
	<div class="row layout-top-spacing">
		<div id="basic" class="col-lg-12 layout-spacing">
			<div class="statbox widget box box-shadow">
				<div class="widget-header">
					<div class="row">
						<div class="col-xl-12 col-md-12 col-sm-12 col-12">
							<h4>Push Notification </h4>
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
							<i class="fa fa-times-circle"></i><?php echo e(session('error')); ?>

						</div>
					<?php endif; ?>
					<form class="simple-example" action="<?php echo e(url('admin/sendpushnotification')); ?>" method="post" enctype="multipart/form-data">
						<?php echo e(csrf_field()); ?>

						 
						<div class="form-row">
						   
							<div class="col-md-12 ">
								<label for="contact_no">Sent To</label> 
								<select class="custom-select" id="sent_to" name="send_to" required> 
									<option value="1">All Users</option>
									
									<?php if(!empty($users)){
										foreach($users as $user){
									?>
										<option value="<?= $user->id;?>">
										<?= $user->name;?> 
										  <?php echo ($user->email != '')? " ( $user->email ) ":'';?>  
										</option>
									<?php 
										} 
									} 
									?>
									 
								</select>
							</div> 
							
							<div class="col-md-12 ">
								<label for="contact_no">Title</label>
								<input type="text" class="form-control" id="title" name="title" placeholder="Enter Title" required> 
							</div>
							
							<div class="col-md-12 ">
								<label for="contact_no">Push Message</label> 
								<textarea class="form-control" id="push" name="message" required></textarea> 
							</div> 
					   
						</div>
						<button class="btn btn-primary submit-fn mt-2" type="submit">Submit</button>
					</form>
				</div>
			</div>
		</div>
	</div>
</div> 
</div> 

 <?php $__env->stopSection(); ?> 
<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/stagepicker/resources/views/admin/users/push_notification.blade.php ENDPATH**/ ?>
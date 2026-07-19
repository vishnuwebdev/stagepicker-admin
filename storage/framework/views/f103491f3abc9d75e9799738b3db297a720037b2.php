
<?php $__env->startSection('content'); ?>
<div class="right-content">
	<div class="container">
		<div class="row">
			<div id="flFormsGrid" class="col-lg-12 layout-spacing">
				<div class="statbox widget box box-shadow">
					<div class="widget-header">
						<div class="row">
							<div class="col-xl-12 col-md-12 col-sm-12 col-12">
								<h4>User Detail</h4>
							</div>                                                                
						</div>
					</div>
					<div class="widget-content widget-content-area">
						<div class="stage-profile-details-section">
							<div class="row">
								<div class="col-md-2">
									<!--div class="profile-img"--> 
									<div class="user-img"> 
										<?php if(!empty($userDetail->image)){ ?> 
											<img src="<?php echo e(asset($userDetail->image)); ?>" width="100px">
										<?php }else{ ?>
											<img src="<?php echo e(asset('public/assets/img/stagepicker-logo.png')); ?>">
										<?php } ?>
											
									</div>
								</div>
								
								<div class="col-md-10"> 
									<div class="profile-information">
									    <div class="row">
									        <div class="col-md-6">
									           	<p><strong>Name : </strong><?php echo e($userDetail->name); ?> ( <?php if($userDetail->user_type == 1){ echo "Auditioners"; }elseif($userDetail->user_type == 2){ echo "Producer"; } ?> ) </p>
										 
									        </div>
									         <div class="col-md-6">
									           	<p><strong>Email : </strong><?php echo e($userDetail->email); ?></p>
									 
									        </div>
									        <div class="col-md-6">
									            <p><strong>Phone : </strong><?php echo e($userDetail->phone); ?></p>
									
									        </div>
									         <div class="col-md-6">
									           <?php if($userDetail->user_type == 1){ ?>
											<p><strong>Stage Name : </strong><?php echo e($userDetail->stage_name); ?></p>
										<?php } ?> 
									        </div>
									         <div class="col-md-6">
									           	<?php if($userDetail->user_type == 1){ ?>
										<p><strong>Age : </strong><?php if(!empty($userDetail->age)){ echo $userDetail->age; echo "Years old";  } ?></p>
										 
									        </div>
									         <div class="col-md-6">
									           <p><strong>Height : </strong><?php echo e($userDetail->height); ?></p>
									 
									        </div>
									        <div class="col-md-6">
									           <p><strong>Gender : </strong><?php if(!empty($userDetail->gender) && $userDetail->gender == 0){ echo "Male"; }elseif($userDetail->gender == 1){ echo "Female"; } ?> </p>
										<?php } ?> 
									        </div>
									    </div>
									
									</div>
								</div>
								<div class="col-md-12">
									<div class="profile-information-right">
										<p><strong>Website : </strong><?php echo e($userDetail->website); ?></p>
										<p><strong>Role Type : </strong><?php echo e($userDetail->roles); ?></p>
										<p><strong>Union Type : </strong><?php if($userDetail->union_type == 0){ echo "Non-union"; }else{ echo "Union"; } ?></p>
										 
										<?php  if($userDetail->user_type == 1){?>
											<!--p><strong>User Type : </strong>Auditioners</p-->
											<p><strong>Skills : </strong><?php echo e($userDetail->skills); ?></p>
										<?php }  ?>
										<?php if($userDetail->user_type == 2){?>
											<!--p><strong>User Type : </strong>Producer</p-->
											<p><strong>Location : </strong><?php echo e($userDetail->location); ?></p>
										<?php } ?>
										
										<?php if($userDetail->producer_type != ''){?>
										<p><strong>Producer Type : </strong><?php echo e($userDetail->producer_type); ?></p>
										<?php } ?>
										
										<?php if($userDetail->user_type == 1){?>
										<p><strong>Member Type : </strong> 
											<?php if($userDetail->pro_member == 1){?>
											Paid (Pro Member)
											<?php } ?>
											<?php if($userDetail->pro_member == 0){?>
												Free
											<?php } ?>
										</p>
										<?php } ?>
										
										<p class="member-part"><strong>Instagram : </strong><?php echo e($userDetail->instagram); ?></p>
										<p><strong>Facebook : </strong><?php echo e($userDetail->facebook); ?></p>
										<p><strong>Tiktok : </strong><?php echo e($userDetail->tiktok); ?></p>
										<p><strong>Snapchat : </strong><?php echo e($userDetail->snapchat); ?></p>
										
									</div>
								</div>
							</div>
							<?php if($userDetail->user_type == 1){ ?>
							<div class="row mt-3">
								<div class="col-md-12">
								    <section id="gallery">
                                  
                                    <div id="image-gallery">
									<div class="headshot-section">
										<h3> Headshots </h3>
										<div class="row">
											<div class="col-md-3">
												<div class="headshots-img-part">
													<?php if(!empty($portfolio['headshot_first'])){ ?> 
													<img src="<?php echo e(asset($portfolio['headshot_first'])); ?>">
													<?php }else{ ?>
													<div class="img-wrapper">
                                                    <a href="<?php echo e(asset('public/assets/img/stagepicker-logo.png')); ?>"><img src="<?php echo e(asset('public/assets/img/stagepicker-logo.png')); ?>" class="img-responsive"></a>
                                                    <div class="img-overlay">
                                                      <i class="fa fa-plus-circle" aria-hidden="true"></i>
                                                    </div>
                                                  </div>
													<?php } ?>
												</div>
												 
											</div>
											<div class="col-md-3">
												<div class="headshots-img-part">
													<?php if(!empty($portfolio['headshot_second'])){ ?> 
													<img src="<?php echo e(asset($portfolio['headshot_second'])); ?>">
													<?php }else{ ?>
														<div class="img-wrapper">
                                                    <a href="<?php echo e(asset('public/assets/img/stagepicker-logo.png')); ?>"><img src="<?php echo e(asset('public/assets/img/stagepicker-logo.png')); ?>" class="img-responsive"></a>
                                                    <div class="img-overlay">
                                                      <i class="fa fa-plus-circle" aria-hidden="true"></i>
                                                    </div>
                                                  </div>
													<?php } ?>
												</div>
											</div>
										</div>
										<?php
										if($userDetail->pro_member == 1){ ?>
										<div class="row">
											<div class="col-md-3">
												<div class="headshots-img-part">
													<?php if(!empty($portfolio['headshot_third'])){ ?> 
													<img src="<?php echo e(asset($portfolio['headshot_third'])); ?>">
													<?php }else{ ?>
													<img src="<?php echo e(asset('public/assets/img/stagepicker-logo.png')); ?>">
													<?php } ?>
												</div>
											</div>
											<div class="col-md-3">
												<div class="headshots-img-part">
													<?php if(!empty($portfolio['headshot_fourth'])){ ?> 
													<img src="<?php echo e(asset($portfolio['headshot_fourth'])); ?>">
													<?php }else{ ?>
													<img src="<?php echo e(asset('public/assets/img/stagepicker-logo.png')); ?>">
													<?php } ?>
												</div>
											</div>
										</div>
									<?php } ?>
									</div>
									</div>
									</section>
								</div>
								<div class="col-md-12">
									<div class="headshot-section">
										<h3> Documents </h3>
										<div class="row">
											<div class="col-md-3 document-section">
												
												<?php if(!empty($portfolio['document_first'])){ ?> 
													<a href="<?php echo e(asset($portfolio['document_first'])); ?>" target="_blank">
													<div class="headshots-img-part">
														<i class="fa fa-file-text" aria-hidden="true"></i>
													</div>
													</a>
													<?php }else{ ?>
													<div class="headshots-img-part">
														<i class="fa fa-file-text" aria-hidden="true"></i>
													</div>
													<?php } ?>
												<!--<p>ABC.pdf</p>-->
											</div>
											<div class="col-md-3 document-section">
												 <?php if(!empty($portfolio['document_second'])){ ?>  
													<a href="<?php echo e(asset($portfolio['document_second'])); ?>" target="_blank">
													<div class="headshots-img-part">
														<i class="fa fa-file-text" aria-hidden="true"></i>
													</div>
													</a>
													<?php }else{ ?>
													<div class="headshots-img-part">
														<i class="fa fa-file-text" aria-hidden="true"></i>
													</div>
													<?php } ?>
											</div>
										</div>
										<?php
										if($userDetail->pro_member == 1){ ?>
										<div class="row">
											<div class="col-md-3 document-section">
												<?php if(!empty($portfolio['document_first'])){ ?> 
													<a href="<?php echo e(asset($portfolio['document_first'])); ?>" target="_blank">
													<div class="headshots-img-part">
														<i class="fa fa-file-text" aria-hidden="true"></i>
													</div>
													</a>
													<?php }else{ ?>
													<div class="headshots-img-part">
														<i class="fa fa-file-text" aria-hidden="true"></i>
													</div>
													<?php } ?>
											</div>
											<div class="col-md-3 document-section">
												 <?php if(!empty($portfolio['document_second'])){ ?>  
													<a href="<?php echo e(asset($portfolio['document_second'])); ?>" target="_blank">
													<div class="headshots-img-part">
														<i class="fa fa-file-text" aria-hidden="true"></i>
													</div>
													</a>
													<?php }else{ ?>
													<div class="headshots-img-part">
														<i class="fa fa-file-text" aria-hidden="true"></i>
													</div>
													<?php } ?>
											</div>
										</div>
										
										<?php } ?>

									</div>
								</div>
							</div>
							<div class="row mt-3">
								<div class="col-md-12">
									<div class="headshot-section">
										<h3> Audio </h3>
										<div class="row">
											<div class="col-md-3 document-section">
												<?php if(!empty($portfolio['audio_first'])){ ?> 

													<a href="<?php echo e(asset($portfolio['audio_first'])); ?>" target="_blank">
													<div class="headshots-img-part">
														<i class="fa fa-music" aria-hidden="true"></i>
													</div>
													</a>
													<?php }else{ ?>
													<div class="headshots-img-part">
														<i class="fa fa-file-text" aria-hidden="true"></i>
													</div>
													<?php } ?>
												<!--<p> Audio name</p>-->
											</div>
											<div class="col-md-3 document-section">
												<?php if(!empty($portfolio['audio_second'])){ ?> 
												
													<a href="<?php echo e(asset($portfolio['audio_second'])); ?>" target="_blank">
													<div class="headshots-img-part">
														<i class="fa fa-music" aria-hidden="true"></i>
													</div>
													</a>
													<?php }else{ ?>
													<div class="headshots-img-part">
														<i class="fa fa-file-text" aria-hidden="true"></i>
													</div>
													<?php } ?>
												<!--<p> Audio name</p>-->
										  </div>
										</div>
									</div>
								</div>
								<div class="col-md-6">
									<div class="headshot-section">
										<h3> Videos </h3>
										<div class="row">
											<div class="col-md-6 document-section">
												<?php if(!empty($portfolio['video_first'])){ 
												preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $portfolio['video_first'], $match);
												$youtube_id = $match[1];
												?> 
												
													<a href="<?php echo e(asset($portfolio['video_first'])); ?>" target="_blank">
													<div class="headshots-img-part">
														<img
            src="https://img.youtube.com/vi/<?php echo $youtube_id; ?>/hqdefault.jpg"
            width="150" />
													</div>
													</a>
													<?php }else{ ?>
													<div class="headshots-img-part">
														<i class="fa fa-file-text" aria-hidden="true"></i>
													</div>
													<?php } ?>
												<!--<p>Video Name</p>-->
											</div>
											<div class="col-md-6 document-section">
												<?php if(!empty($portfolio['video_second'])){ 
												preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $portfolio['video_second'], $match1);
												$youtube_id1 = $match1[1];
												?> 
												
													<a href="<?php echo e(asset($portfolio['video_second'])); ?>" target="_blank">
														<div class="headshots-img-part">
															<img
            src="https://img.youtube.com/vi/<?php echo $youtube_id1; ?>/hqdefault.jpg"
            width="150" />
														</div>
													</a>
													<?php }else{ ?>
													<div class="headshots-img-part">
														<i class="fa fa-file-text" aria-hidden="true"></i>
													</div>
													<?php } ?>
												<!--<p>Video Name</p>-->
											</div>
										</div>
									</div>
								</div>
							</div>
							<?php } ?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.admin', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /var/www/html/stagepicker/resources/views/admin/users/userdetail.blade.php ENDPATH**/ ?>
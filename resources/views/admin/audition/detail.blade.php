@extends('layouts.admin')
@section('content')
<style>
.post-audition-details-part span {
    width: 220px;
    display: inline-block;
    font-size: 16px;
    color: #000;
    font-weight: 500;
}
.participant-img1 {
    width: 150px;
    float: left;
    margin-right: 15px;
}
.participant-img1 img {
    width: 150px;
    height: 150px; 
}
.accordion .fas {
    float: right;
    width: 70%;
    text-align: right;
    font-size: 20px;
}
.accordion-button {
    font-size: 14px;
    color: #4f4f4f;
}
</style>

<div class="right-content">
	<div class="container">
		<div class="row">
			<div id="flFormsGrid" class="col-lg-12 layout-spacing">
				<div class="statbox widget box box-shadow">
					<div class="widget-header">
						<div class="row">
							<div class="col-xl-12 col-md-12 col-sm-12 col-12">
								<h4>Post Audition Details</h4>
							</div>                                                                
						</div>
					</div>
					<div class="widget-content widget-content-area">
						<div class="stage-profile-details-section">
							<div class="row">
							 <div class="col-md-12">
							  <div class="post-audition-details-part post-audition-details-section">
							      <div class="row">
							          <div class="col-md-6">
							            	<p><span>Title : </span> <span class="audition-title">{{ $detail->audition_title }}</span></p>
								  
							          </div>
							          <div class="col-md-6">
							            	<p><span>Producer : </span> <span class="audition-title">{{ $detail->producer_name }}</span></p>
								  
							          </div>
							          <div class="col-md-6">
							            <p><span>Category : </span> <span class="audition-title"><?php if(!empty($detail->category_name)){ echo $detail->category_name; }else{ echo "Photography"; } ?> </span></p>
							  
							          </div>
							          <div class="col-md-6">
							              	<p><span>Audition Date  : </span> <span class="audition-title">{{ $detail->expire_date }} </span></p> 
								
							          </div>
							          <div class="col-md-6">
							             <p><span>Age Group : </span><span class="audition-title">{{ $detail->age_group }} Years</span></p>
								 
							          </div>
							          <div class="col-md-12">
							             <p><span>Location : </span> <span class="audition-title">{{ $detail->location }} </span></p>
								 
							          </div>
							          <div class="col-md-12">
							            <p><span>Description : </span> <span class="audition-title">{{ $detail->production_description }} </span></p>
								  
							          </div>
							          <div class="col-md-12">
							             	<p><span>Skills :  </span> <span class="audition-title">{{ $detail->skills }} </span></p>
								</div>
								<div class="col-md-6">
								   <?php if(!empty($detail->studio_name)){ ?>
									<p><span>Studio Name  : </span>  <span class="audition-title"><?php echo $detail->studio_name;  ?> </span></p>
								<?php } ?> 
								</div>
								<div class="col-md-6">
								<?php //if(!empty($detail->compensation)){ ?>
									<p><span>Compensation  : </span> <span class="audition-title"><?php echo ($detail->compensation == '1')?'Yes':'No';  ?> </span></p>
								<?php //} ?> 
								</div>
								<div class="col-md-12">
								   	<?php if(!empty($detail->compensation_description)){ ?>
									<p><span>Compensation Description  : </span>  <span class="audition-title"><?php echo $detail->compensation_description;  ?> </span></p>
								<?php } ?> 
								</div>
								<div class="col-md-6">
								    	<?php if(!empty($detail->production_website)){ ?>
									<p><span>Production Website  : </span>  <span class="audition-title"><?php echo $detail->production_website;  ?> </span></p>
								<?php } ?>
								</div>
								<div class="col-md-6">
								   <?php if(!empty($detail->audition_location)){ ?>
									<p><span>Audition Location  : </span>  <span class="audition-title"><?php echo $detail->audition_location;  ?> </span></p>
								<?php } ?> 
								</div>
							      </div> 
						 
							  </div>
							</div>
							</div>
							
							<?php if(!empty($detail->roles) && $detail->audition_type != '2'){ ?>
							<div class="row">
								<div class="col-md-12">
									<div class="post-audition-details-part"> 
										<p><span>Roles : </span></p>
									</div>
									<!--Accordion wrapper-->
									<div class="accordion md-accordion" id="accordionEx" role="tablist" aria-multiselectable="true">
									 <?php 
									 foreach($detail->roles as $key=>$role){ 
										 $expand = 'true';
										 $expandcls = 'show';
										 if($key > 0){
											 $expand = 'false';
											 $expandcls = '';
										 }
									 ?>
									 
									  <!-- Accordion card -->
									  <div class="card">

										<!-- Card header -->
										<div class="card-header" role="tab" id="headingOne<?= $key;?>">
										  <a data-toggle="collapse" data-parent="#accordionEx" href="#collapseOne<?= $key;?>" aria-expanded="<?= $expand;?>"
											aria-controls="collapseOne<?= $key;?>" class="accordion-button"> 
											{{$role->role_title}} | {{$role->gender}} | {{$role->age_min}}-{{$role->age_max}}<i class="fas fa-angle-down rotate-icon"></i> 
										  </a>
										</div>

										<!-- Card body -->
										<div id="collapseOne<?= $key;?>" class="collapse <?= $expandcls;?>" role="tabpanel" aria-labelledby="headingOne<?= $key;?>"
										  data-parent="#accordionEx">
										  <div class="card-body">
											 <div class="col-md-12 information-text">
											   <b> (<?php echo count($role->participants); ?>) Participants </b>
											 </div>
											 <div class="col-md-6">
											  <div class="post-audition-details-part">
												  <?php foreach($role->participants as $participantsval){ ?> 
												<div class="participant-member">
												  <div class="participant-img">
													<?php if(!empty($participantsval->image)){ ?>
														<img src="{{asset($participantsval->image)}}">
													<?php }else{ ?> 
														<img src="{{asset('public/assets/img/stagepicker-logo.png')}}">
													<?php } ?>
												  </div>
												  <div class="participant-details">
													<p>{{ $participantsval->name }}</p>
													<p>Age: <?php if(!empty($participantsval->age)){echo $participantsval->age.' Years'; } ?> <span> | <?php if(!empty($participantsval->height)){echo $participantsval->height; } ?>  </span></p>
													<?php if(!empty($participantsval->role_title)){ ?>
														<p>Role: <?php echo $participantsval->role_title;  ?> </p>
													<?php } ?> 
												  </div>
												</div>
												<?php } ?>
											  </div>
											</div>
										  </div>
										</div>

									  </div>
									  <!-- Accordion card -->
									 <?php } ?>
									   

									</div>
									<!-- Accordion wrapper -->
								</div>
							</div>
							<?php } ?>
							
							<?php if($detail->audition_type == '2'){ ?>

								<div class="row">
								 <div class="col-md-12 information-text">
								   <h5> (<?php echo count($participants); ?>) Participants </h5>
								 </div>
								 <div class="col-md-6">
								  <div class="post-audition-details-part">
									  <?php foreach($participants as $participantsval){ ?> 
									<div class="participant-member">
									  <div class="participant-img">
										<?php if(!empty($participantsval->image)){ ?>
											<img src="{{asset($participantsval->image)}}">
										<?php }else{ ?> 
											<img src="{{asset('public/assets/img/stagepicker-logo.png')}}">
										<?php } ?>
									  </div>
									  <div class="participant-details">
										<p>{{ $participantsval->name }}</p>
										<p>Age: <?php if(!empty($participantsval->age)){echo $participantsval->age.' Years'; } ?> <span> | <?php if(!empty($participantsval->height)){echo $participantsval->height; } ?>  </span></p>
										<?php if(!empty($participantsval->role_title)){ ?>
											<p>Role: <?php echo $participantsval->role_title;  ?> </p>
										<?php } ?> 
									  </div>
									</div>
									<?php } ?>
								  </div>
								</div>
								</div>
								
								<?php if(!empty($photos)){ ?>
								<div class="row">
								  <div class="col-md-12 information-text">
								   <h5> (<?php echo count($photos); ?>) Photography Image </h5>
								 </div>
								 <div class="col-md-6">
								  <div class="post-audition-details-part">
									  <?php foreach($photos as $img){ ?> 
									 
									  <div class="participant-img1">
										<?php if(!empty($img->image)){ ?>
											<img src="{{asset($img->image)}}">
										<?php }else{ ?> 
											<img src="{{asset('public/assets/img/stagepicker-logo.png')}}">
										<?php } ?>
									  </div>  
									<?php } ?>
								  </div>
								</div>
								</div>
								<?php } ?>
							
							<?php } ?>
							
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
@endsection
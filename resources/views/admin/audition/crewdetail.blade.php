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
								<h4>Production Crew Details</h4>
							</div>                                                                
						</div>
					</div>
					<div class="widget-content widget-content-area">
						<div class="stage-profile-details-section">
							<div class="row">
							 <div class="col-md-12">
							  <div class="post-audition-details-part"> 
								<p><span>Title : </span> {{ $detail->title }}</p>
								<p><span>Category : </span> <?php if(!empty($detail->category_name)){ echo $detail->category_name; }else{ echo ""; } ?> </p>
								<p><span>Audition Date  : </span> {{ $detail->expire_date }} </p> 
								<p><span>Location : </span> {{ $detail->location }} </p>
								<p><span>Description : </span> {{ $detail->description }} </p>
								<p><span>Skills :  </span> {{ $detail->skills }} </p>
								
								 
								
								<?php if(!empty($detail->compensation)){ ?>
									<p><span>Compensation  : </span> <?php echo ($detail->compensation == '1')?'Yes':'No';  ?> </p>
								<?php } ?>
								
								<?php if(!empty($detail->compensation_description)){ ?>
									<p><span>Compensation Description  : </span>  <?php echo $detail->compensation_description;  ?> </p>
								<?php } ?>
								
								<?php if(!empty($detail->website_url)){ ?>
									<p><span>Website URL : </span>  <?php echo $detail->website_url;  ?> </p>
								<?php } ?> 
							  </div>
							</div>
							</div>
							
							<?php if(!empty($detail->roles)){ ?>
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
											{{$role->role_title}} | {{$role->category_name}}<i class="fas fa-angle-down rotate-icon"></i> 
										  </a>
										</div>

										<!-- Card body -->
										<?php if(!empty($role->participants)){?>
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
										<?php } ?>
									  </div>
									  <!-- Accordion card -->
									 <?php } ?>
									   

									</div>
									<!-- Accordion wrapper -->
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
@endsection
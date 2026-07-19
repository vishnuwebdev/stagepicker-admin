@extends('layouts.admin')
@section('content')
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
			@if(count($errors) > 0)
		   
		        <div class="col-md-12">
		            <div class="alert alert-danger">
		                @foreach ($errors->all() as $error)
		                    <div>{{ $error }}</div>
		                @endforeach
		            </div>
		        </div>
		   
		@endif
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
						<form action="{{ url('admin/editUser')  }}" method="POST" enctype="multipart/form-data">
							@csrf
							<input type="hidden" name="user_id" value="{{ $uid }}">
							<div class="row">
							    <div class="col-md-6">
							       <div class="form-group mb-4">
								<label for="name">Name</label>
								<input type="text" class="form-control" name="name" id="name" placeholder="Enter Name" value="{{ old('name', isset($userdetail) ? $userdetail->name : '') }}">
								@if ($errors->has('name'))
									<span class="text-danger">{{ $errors->first('name') }}</span>
								@endif
							</div> 
							</div>
							
							<?php if($userdetail->user_type == 1){ ?>
							<div class="col-md-6">
								<div class="form-group mb-4">
									<label for="company_name">Stage Name</label>
									<input readonly type="text" class="form-control" name="stage_name" id="stage_name" placeholder="Enter Stage Name" value="{{ old('stage_name', isset($userdetail) ? $userdetail->stage_name : '') }}">
									@if ($errors->has('stage_name'))
										<span class="text-danger">{{ $errors->first('stage_name') }}</span>
									@endif
								</div>
							</div>
							<?php } elseif($userdetail->user_type == 2){ ?>
							<div class="col-md-6">
								<div class="form-group mb-4">
									<label for="company_name">Company Name</label>
									<input type="text" class="form-control" name="company_name" id="company_name" placeholder="Enter Company Name" value="{{ old('company_name', isset($userdetail) ? $userdetail->company_name : '') }}">
									@if ($errors->has('company_name'))
										<span class="text-danger">{{ $errors->first('company_name') }}</span>
									@endif
								</div>
							</div>
							<?php } ?>
							<div class="col-md-6">
								<div class="form-group mb-4">
									<label for="email">Email</label>
									<input readonly type="text" class="form-control" name="email" id="email" placeholder="Email" value="{{ old('email', isset($userdetail) ? $userdetail->email : '') }}">
									@if ($errors->has('email'))
										<span class="text-danger">{{ $errors->first('email') }}</span>
									@endif
								</div>
							</div>
							<div class="col-md-6">
							       	
							<div class="form-group mb-4">
								<label for="phone">Phone</label>
								<input type="text" class="form-control" name="phone" id="phone" placeholder="phone" value="{{ old('phone', isset($userdetail) ? $userdetail->phone : '') }}">
								@if ($errors->has('phone'))
									<span class="text-danger">{{ $errors->first('phone') }}</span>
								@endif
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
								@if ($errors->has('status'))
									<span class="text-danger">{{ $errors->first('status') }}</span>
								@endif
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
								@if ($errors->has('gender'))
									<span class="text-danger">{{ $errors->first('gender') }}</span>
								@endif
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
								@if ($errors->has('union_type'))
									<span class="text-danger">{{ $errors->first('union_type') }}</span>
								@endif
							</div>  
						</div>

						<div class="col-md-6">
							       	
							<div class="form-group mb-4">
								<label for="age">Age</label>
								<input type="text" class="form-control" name="age" id="age" placeholder="Age" value="{{ old('age', isset($userdetail) ? $userdetail->age : '') }}">
								@if ($errors->has('age'))
									<span class="text-danger">{{ $errors->first('age') }}</span>
								@endif
							</div> 
							</div>

							<div class="col-md-6">
							       	
							<div class="form-group mb-4">
								<label for="height">Height</label>
								<input type="height" class="form-control" name="height" id="height" placeholder="Height" value="{{ old('height', isset($userdetail) ? $userdetail->height : '') }}">
								@if ($errors->has('height'))
									<span class="text-danger">{{ $errors->first('height') }}</span>
								@endif
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
								
								@if ($errors->has('skills'))
									<span class="text-danger">{{ $errors->first('skills') }}</span>
								@endif
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
								
								@if ($errors->has('producer_type'))
									<span class="text-danger">{{ $errors->first('producer_type') }}</span>
								@endif
							</div>
						</div>
						<?php } ?>

						<div class="col-md-6">
							       	
							<div class="form-group mb-4">
								<label for="website">Website</label>
								<input type="url" class="form-control" name="website" id="website" placeholder="Website" value="{{ old('website', isset($userdetail) ? $userdetail->website : '') }}">
								@if ($errors->has('website'))
									<span class="text-danger">{{ $errors->first('website') }}</span>
								@endif
							</div> 
							</div>

							<div class="col-md-6">
							       	
							<div class="form-group mb-4">
								<label for="facebook">Facebook</label>
								<input type="facebook" class="form-control" name="facebook" id="facebook" placeholder="Facebook" value="{{ old('facebook', isset($userdetail) ? $userdetail->facebook : '') }}">
								@if ($errors->has('facebook'))
									<span class="text-danger">{{ $errors->first('facebook') }}</span>
								@endif
							</div> 
							</div>

                           <div class="col-md-6">
							       	
							<div class="form-group mb-4">
								<label for="instagram">Instagram</label>
								<input type="text" class="form-control" name="instagram" id="instagram" placeholder="Instagram" value="{{ old('instagram', isset($userdetail) ? $userdetail->instagram : '') }}">
								@if ($errors->has('instagram'))
									<span class="text-danger">{{ $errors->first('instagram') }}</span>
								@endif
							</div> 
							</div>

							<div class="col-md-6">
							       	
							<div class="form-group mb-4">
								<label for="tiktok">Tiktok</label>
								<input type="tiktok" class="form-control" name="tiktok" id="tiktok" placeholder="Tiktok" value="{{ old('tiktok', isset($userdetail) ? $userdetail->tiktok : '') }}">
								@if ($errors->has('tiktok'))
									<span class="text-danger">{{ $errors->first('tiktok') }}</span>
								@endif
							</div> 
							</div>
							<div class="col-md-6">
							       	
							<div class="form-group mb-4">
								<label for="snapchat">Snapchat</label>
								<input type="snapchat" class="form-control" name="snapchat" id="snapchat" placeholder="Snapchat" value="{{ old('snapchat', isset($userdetail) ? $userdetail->snapchat : '') }}">
								@if ($errors->has('snapchat'))
									<span class="text-danger">{{ $errors->first('snapchat') }}</span>
								@endif
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
@endsection

@section('script')

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

@endsection
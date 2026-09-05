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
						<form action="{{ url('admin/adminuser/update')  }}" method="POST" enctype="multipart/form-data">
							@csrf
							<input type="hidden" name="user_id" value="{{ $uid }}">
							<div class="row">
							    <div class="col-md-4">
							       <div class="form-group mb-4">
								<label for="name">Name</label>
								<input type="text" class="form-control" name="name" id="name" placeholder="Enter Name" value="{{ old('name', isset($userdetail) ? $userdetail->name : '') }}">
								@if ($errors->has('name'))
									<span class="text-danger">{{ $errors->first('name') }}</span>
								@endif
							</div> 
							</div>
							
							<div class="col-md-4">
								<div class="form-group mb-4">
									<label for="email">Email</label>
									<input readonly type="text" class="form-control" name="email" id="email" placeholder="Email" value="{{ old('email', isset($userdetail) ? $userdetail->email : '') }}">
									@if ($errors->has('email'))
										<span class="text-danger">{{ $errors->first('email') }}</span>
									@endif
								</div>
							</div>
							<div class="col-md-4">
							       	
							<div class="form-group mb-4">
								<label for="phone">Password</label>
								<input type="password" class="form-control" name="password" id="password" placeholder="Password" value="">
								@if ($errors->has('password'))
									<span class="text-danger">{{ $errors->first('password') }}</span>
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
									<option value="0" <?php echo ($userdetail->status == 2)?"selected":"";?>>In Active</option> 
								</select>
								@if ($errors->has('status'))
									<span class="text-danger">{{ $errors->first('status') }}</span>
								@endif
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
                        			<td>{{$per->module}}</td>
                        			<input type="hidden" name="permission[<?=$key?>][module]" value="{{$per->module}}">
                        			<input type="hidden" name="permission[<?=$key?>][moduleId]" value="{{$per->moduleId}}">
                        			<td style="text-align: center;"><input type="checkbox" <?=($per->view) ? 'checked' : ''; ?> name="permission[<?=$key?>][view]" value="1"></td>
                        			<td style="text-align: center;"><input type="checkbox" <?=($per->add) ? 'checked' : ''; ?> name="permission[<?=$key?>][add]" value="1"></td>
                        			<td style="text-align: center;"><input type="checkbox" <?=($per->edit) ? 'checked' : ''; ?> name="permission[<?=$key?>][edit]" value="1"></td>
                        			<td style="text-align: center;"><input type="checkbox" <?=($per->delete) ? 'checked' : ''; ?> name="permission[<?=$key?>][delete]" value="1"></td>
                        		</tr>

                              <?php } ?>
                              <?php
                                // Existing admin users won't have a p10 (Animal Audition
                                // Management) entry in their stored permissions JSON yet —
                                // this form only renders rows already present above, so
                                // without this the module could never be granted through
                                // the edit screen. Render one extra, unchecked-by-default
                                // row for it here, keyed past the last existing index.
                                $hasAnimalAuditionModule = false;
                                foreach ($permission as $per) {
                                    if ($per->moduleId == 'p10') { $hasAnimalAuditionModule = true; break; }
                                }
                                $nextKey = count($permission);
                              ?>
                              <?php if (!$hasAnimalAuditionModule) { ?>
                                  <tr>
                        			<td>Animal Audition Management</td>
                        			<input type="hidden" name="permission[<?=$nextKey?>][module]" value="Animal Audition Management">
                        			<input type="hidden" name="permission[<?=$nextKey?>][moduleId]" value="p10">
                        			<td style="text-align: center;"><input type="checkbox" name="permission[<?=$nextKey?>][view]" value="1"></td>
                        			<td style="text-align: center;"><input type="checkbox" name="permission[<?=$nextKey?>][add]" value="1"></td>
                        			<td style="text-align: center;"><input type="checkbox" name="permission[<?=$nextKey?>][edit]" value="1"></td>
                        			<td style="text-align: center;"><input type="checkbox" name="permission[<?=$nextKey?>][delete]" value="1"></td>
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
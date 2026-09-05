@extends('layouts.admin')
@section('content')

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
								<h4>Add Admin User</h4>
							</div>                                                                
						</div>
					</div>
					<div class="widget-content widget-content-area">
						<form action="{{ url('admin/adminuser/store')  }}" method="POST" enctype="multipart/form-data">
							@csrf
							<input type="hidden" name="_token" id="csrf-token" value="{{ Session::token() }}">
							<div class="row">
							    <div class="col-md-4">
							       <div class="form-group mb-4">
								<label for="name">Name</label>
								<input type="text" class="form-control" name="name" id="name" placeholder="Enter Name" value="">
								@if ($errors->has('name'))
									<span class="text-danger">{{ $errors->first('name') }}</span>
								@endif
							</div> 
							</div>
							
							<div class="col-md-4">
								<div class="form-group mb-4">
									<label for="email">Email</label>
									<input type="text" class="form-control" name="email" id="email" placeholder="Email" value="">
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
								<input type="text" class="form-control" name="phone" id="phone" placeholder="phone" value="">
								@if ($errors->has('phone'))
									<span class="text-danger">{{ $errors->first('phone') }}</span>
								@endif
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
                        			<tr>
                        			<td>Animal Audition Management</td>
                        			<input type="hidden" name="permission[9][module]" value="Animal Audition Management">
                        			<input type="hidden" name="permission[9][moduleId]" value="p10">
                        			<td style="text-align: center;"><input type="checkbox" name="permission[9][view]" value="1"></td>
                        			<td style="text-align: center;"><input type="checkbox" name="permission[9][add]" value="1"></td>
                        			<td style="text-align: center;"><input type="checkbox" name="permission[9][edit]" value="1"></td>
                        			<td style="text-align: center;"><input type="checkbox" name="permission[9][delete]" value="1"></td>
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
@endsection

@section('script')



@endsection